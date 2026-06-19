<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Support\AssignmentContent;
use App\Support\AssignmentFiles;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AssignmentController extends Controller
{
    private function formatRow(object $row): array
    {
        return AssignmentContent::formatRow($row);
    }

    private function findRow(Request $request, int $id): ?object
    {
        $email = (string) $request->attributes->get('api_user')['email'];
        $courses = DB::table('course_assign')->where('email', $email)->pluck('course');
        $batches = DB::table('batches')
            ->when($courses->isNotEmpty(), fn ($q) => $q->whereIn('course', $courses))
            ->pluck('name');

        return DB::table('assignement')
            ->when($batches->isNotEmpty(), fn ($q) => $q->whereIn('batch_name', $batches))
            ->where('id', $id)
            ->first();
    }

    public function index(Request $request): JsonResponse
    {
        $email = (string) $request->attributes->get('api_user')['email'];
        $courses = DB::table('course_assign')->where('email', $email)->pluck('course');
        $batches = DB::table('batches')
            ->when($courses->isNotEmpty(), fn ($q) => $q->whereIn('course', $courses))
            ->pluck('name');

        $kind = $request->query('content_kind');

        $rows = DB::table('assignement')
            ->when($batches->isNotEmpty(), fn ($q) => $q->whereIn('batch_name', $batches))
            ->when(in_array($kind, ['assignment', 'note'], true), fn ($q) => $q->where('content_kind', $kind))
            ->orderByDesc('id')
            ->limit(200)
            ->get()
            ->map(fn ($r) => $this->formatRow($r));

        return response()->json(['status' => 'success', 'assignments' => $rows]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $row = $this->findRow($request, $id);
        if (! $row) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'assignment' => $this->formatRow($row),
        ]);
    }

    public function download(Request $request, int $id): BinaryFileResponse|JsonResponse
    {
        $row = $this->findRow($request, $id);
        if (! $row || $row->type !== 'file') {
            return response()->json(['status' => 'error', 'message' => 'File not found'], 404);
        }

        $path = AssignmentFiles::resolvePath((string) $row->document);
        if (! $path) {
            return response()->json(['status' => 'error', 'message' => 'File missing on server'], 404);
        }

        return response()->file($path, [
            'Content-Type' => AssignmentFiles::mimeType($path),
            'Content-Disposition' => 'inline; filename="'.basename($path).'"',
        ]);
    }

    public function allBatches(): JsonResponse
    {
        $rows = DB::table('batches')
            ->where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'course']);

        return response()->json(['status' => 'success', 'batches' => $rows]);
    }

    public function subjectsForBatch(Request $request): JsonResponse
    {
        $batch = (string) $request->query('batch_name', '');
        $email = (string) $request->attributes->get('api_user')['email'];
        $subjects = AssignmentContent::subjectsForBatch($batch ?: null, $email ?: null);

        return response()->json(['status' => 'success', 'subjects' => $subjects]);
    }

    public function store(Request $request): JsonResponse
    {
        $type = $request->input('type');
        $validated = $request->validate([
            'type' => ['required', 'in:link,file'],
            'content_kind' => ['nullable', 'in:assignment,note'],
            'batch_name' => ['required', 'string', 'max:255'],
            'document_name' => ['required', 'string', 'max:255'],
            'subject_id' => ['nullable', 'integer'],
            'subject_name' => ['nullable', 'string', 'max:255'],
            'link' => ['required_if:type,link', 'nullable', 'string', 'max:2000'],
            'file' => ['required_if:type,file', 'nullable', 'file', 'max:20480', 'mimes:pdf,txt,jpg,jpeg,png,gif,webp,doc,docx'],
        ]);

        $subject = AssignmentContent::resolveSubject(
            isset($validated['subject_id']) ? (int) $validated['subject_id'] : null,
            $validated['subject_name'] ?? null,
        );
        $contentKind = $validated['content_kind'] ?? 'assignment';

        $document = '';
        if ($type === 'link') {
            $document = $validated['link'];
        } else {
            $file = $request->file('file');
            $ext = $file->getClientOriginalExtension() ?: 'dat';
            $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $validated['document_name'])
                .'_'.date('d-m-Y').'.'.$ext;
            $file->storeAs('assignments', $filename, 'public');
            $document = $filename;
        }

        DB::table('assignement')->insert([
            'batch_name' => $validated['batch_name'],
            'type' => $type,
            'document_name' => $validated['document_name'],
            'document' => $document,
            'status' => 1,
            'content_kind' => $contentKind,
            'subject_id' => $subject['subject_id'],
            'subject_name' => $subject['subject_name'],
        ]);

        $label = $contentKind === 'note' ? 'Note' : 'Assignment';

        return response()->json(['status' => 'success', 'message' => "{$label} added"]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $data = $request->validate(['status' => ['required', 'boolean']]);
        $row = $this->findRow($request, $id);
        if (! $row) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        DB::table('assignement')->where('id', $id)->update(['status' => $data['status'] ? 1 : 0]);

        return response()->json(['status' => 'success', 'message' => 'Status updated']);
    }

    public function destroy(int $id): JsonResponse
    {
        $row = DB::table('assignement')->where('id', $id)->first();
        if (! $row) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        if ($row->type === 'file' && $row->document) {
            Storage::disk('public')->delete('assignments/'.$row->document);
        }

        DB::table('assignement')->where('id', $id)->delete();

        return response()->json(['status' => 'success', 'message' => 'Assignment deleted']);
    }
}
