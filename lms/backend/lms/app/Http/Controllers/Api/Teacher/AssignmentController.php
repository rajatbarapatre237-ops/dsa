<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Support\AssignmentContent;
use App\Support\AssignmentDocuments;
use App\Support\AssignmentFiles;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AssignmentController extends Controller
{
    private function formatRow(object $row): array
    {
        return AssignmentContent::formatRow($row);
    }

    private function findRow(Request $request, int $id): ?object
    {
        $batches = AssignmentContent::accessibleBatchNames();

        return DB::table('assignement')
            ->when($batches->isNotEmpty(), fn ($q) => $q->whereIn('batch_name', $batches))
            ->where('id', $id)
            ->first();
    }

    public function index(Request $request): JsonResponse
    {
        $batches = AssignmentContent::accessibleBatchNames();

        $kind = $request->query('content_kind');

        $query = DB::table('assignement')
            ->when($batches->isNotEmpty(), fn ($q) => $q->whereIn('batch_name', $batches));

        $rows = AssignmentContent::applyKindFilter($query, is_string($kind) ? $kind : null)
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

        $index = max(0, (int) $request->query('index', 0));
        $path = AssignmentDocuments::resolvePath((string) $row->document, $index);
        if (! $path) {
            return response()->json(['status' => 'error', 'message' => 'File missing on server'], 404);
        }

        $filename = AssignmentDocuments::resolveName((string) $row->document, $index);

        return response()->file($path, [
            'Content-Type' => AssignmentFiles::mimeType($path),
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
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
        $appendToId = (int) $request->input('append_to_id', 0);
        if ($appendToId > 0) {
            return $this->appendFile($request, $appendToId);
        }

        $type = $request->input('type');

        $uploadedFiles = [];
        if ($type === 'file' && $request->hasFile('files')) {
            $files = $request->file('files');
            $uploadedFiles = is_array($files) ? array_values($files) : [$files];
            $uploadedFiles = array_values(array_filter(
                $uploadedFiles,
                fn ($file) => $file && $file->isValid()
            ));
        } elseif ($type === 'file' && $request->hasFile('file') && $request->file('file')->isValid()) {
            $uploadedFiles = [$request->file('file')];
        }

        $hasMultipart = count($uploadedFiles) > 0;
        $filesPayload = $request->input('files_payload');
        $hasFilesPayload = $type === 'file'
            && is_array($filesPayload)
            && count($filesPayload) > 0;
        $hasBase64 = $type === 'file' && filled($request->input('file_base64'));

        if ($type === 'file' && ! $hasMultipart && ! $hasBase64 && ! $hasFilesPayload) {
            return response()->json([
                'status' => 'error',
                'message' => 'No file received. Send a file upload or file_base64 payload.',
                'errors' => ['file' => ['No file received in the request.']],
            ], 422);
        }

        $validated = $request->validate([
            'type' => ['required', 'in:link,file'],
            'content_kind' => ['nullable', 'in:assignment,note'],
            'batch_name' => ['required', 'string', 'max:255'],
            'document_name' => ['required', 'string', 'max:255'],
            'document_names' => ['nullable', 'array', 'max:20'],
            'document_names.*' => ['string', 'max:255'],
            'subject_id' => ['nullable', 'integer'],
            'subject_name' => ['nullable', 'string', 'max:255'],
            'link' => ['required_if:type,link', 'nullable', 'string', 'max:2000'],
            'file' => ['nullable', 'file', 'max:20480', 'mimes:pdf,txt,jpg,jpeg,png,gif,webp,heic,heif,doc,docx'],
            'files' => ['nullable', 'array', 'max:20'],
            'files.*' => ['file', 'max:20480', 'mimes:pdf,txt,jpg,jpeg,png,gif,webp,heic,heif,doc,docx'],
            'file_base64' => ['nullable', 'string'],
            'file_name' => ['nullable', 'string', 'max:255'],
            'file_mime' => ['nullable', 'string', 'max:120'],
            'files_payload' => ['nullable', 'array', 'max:20'],
            'files_payload.*.file_base64' => ['required', 'string'],
            'files_payload.*.file_name' => ['nullable', 'string', 'max:255'],
            'files_payload.*.file_mime' => ['nullable', 'string', 'max:120'],
        ]);

        $subject = AssignmentContent::resolveSubject(
            isset($validated['subject_id']) ? (int) $validated['subject_id'] : null,
            $validated['subject_name'] ?? null,
        );
        $contentKind = AssignmentContent::normalizeKind($validated['content_kind'] ?? 'assignment');
        $label = $contentKind === 'note' ? 'Note' : 'Assignment';

        if ($type === 'link') {
            DB::table('assignement')->insert(
                AssignmentContent::buildInsertData([
                    'batch_name' => $validated['batch_name'],
                    'type' => $type,
                    'document_name' => $validated['document_name'],
                    'document' => $validated['link'],
                    'status' => 1,
                ], $contentKind, $subject)
            );

            return response()->json(['status' => 'success', 'message' => "{$label} added", 'count' => 1]);
        }

        if ($hasFilesPayload) {
            return $this->storeBundledFiles(
                $filesPayload,
                $contentKind,
                $validated,
                $subject,
                $label,
                fn (array $item) => $this->decodePayloadBinary($item, 'files_payload'),
            );
        }

        if ($hasMultipart) {
            $entries = [];
            $storedDocuments = [];
            try {
                foreach ($uploadedFiles as $index => $file) {
                    $ext = $file->getClientOriginalExtension() ?: 'dat';
                    $originalName = 'upload.'.$ext;
                    $filename = AssignmentFiles::makeFilename($validated['document_name'], $originalName, $index + 1);
                    $storedDocument = AssignmentFiles::storeBinary(
                        $contentKind,
                        $filename,
                        (string) $file->get()
                    );
                    $storedDocuments[] = $storedDocument;
                    $entries[] = [
                        'path' => $storedDocument,
                        'name' => basename($filename),
                    ];
                }

                $assignmentId = $this->insertFileAssignment($validated, $contentKind, $subject, $entries);
            } catch (\Throwable $e) {
                foreach ($storedDocuments as $storedDocument) {
                    AssignmentFiles::deleteStoredFile($storedDocument);
                }

                throw $e;
            }

            $fileCount = count($entries);

            return response()->json([
                'status' => 'success',
                'message' => "{$label} added",
                'count' => 1,
                'file_count' => $fileCount,
                'assignment_id' => $assignmentId,
            ]);
        }

        $document = '';
        $storedDocument = null;
        $assignmentId = null;
        try {
            $binary = base64_decode((string) $request->input('file_base64'), true);
            if ($binary === false) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid file data',
                    'errors' => ['file_base64' => ['Could not decode uploaded file.']],
                ], 422);
            }
            if (strlen($binary) > 20480 * 1024) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File is too large (max 20 MB).',
                    'errors' => ['file_base64' => ['File is too large (max 20 MB).']],
                ], 422);
            }

            $originalName = (string) ($validated['file_name'] ?? 'upload.jpg');
            $filename = AssignmentFiles::makeFilename($validated['document_name'], $originalName);
            $storedDocument = AssignmentFiles::storeBinary($contentKind, $filename, $binary);
            $document = $storedDocument;

            $assignmentId = DB::table('assignement')->insertGetId(
                AssignmentContent::buildInsertData([
                    'batch_name' => $validated['batch_name'],
                    'type' => $type,
                    'document_name' => $validated['document_name'],
                    'document' => $document,
                    'status' => 1,
                ], $contentKind, $subject)
            );
        } catch (\Throwable $e) {
            if ($storedDocument) {
                AssignmentFiles::deleteStoredFile($storedDocument);
            }

            throw $e;
        }

        return response()->json([
            'status' => 'success',
            'message' => "{$label} added",
            'count' => 1,
            'file_count' => 1,
            'assignment_id' => $assignmentId,
        ]);
    }

    public function appendFile(Request $request, int $id): JsonResponse
    {
        $row = $this->findRow($request, $id);
        if (! $row || $row->type !== 'file') {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'file_base64' => ['required', 'string'],
            'file_name' => ['nullable', 'string', 'max:255'],
            'file_mime' => ['nullable', 'string', 'max:120'],
        ]);

        $existingCount = AssignmentDocuments::fileCount($row->document ?? null);
        if ($existingCount >= 20) {
            return response()->json([
                'status' => 'error',
                'message' => 'Maximum 20 files per assignment.',
                'errors' => ['file_base64' => ['Maximum 20 files per assignment.']],
            ], 422);
        }

        $binary = $this->decodePayloadBinary($validated, 'file_base64');
        if ($binary === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid file data',
                'errors' => ['file_base64' => ['Could not decode uploaded file.']],
            ], 422);
        }

        $contentKind = AssignmentContent::resolveRowKind($row);
        $originalName = (string) ($validated['file_name'] ?? 'upload.jpg');
        $sequence = $existingCount + 1;
        $filename = AssignmentFiles::makeFilename($row->document_name, $originalName, $sequence);
        $storedDocument = null;

        try {
            $storedDocument = AssignmentFiles::storeBinary($contentKind, $filename, $binary);
            $document = AssignmentDocuments::append((string) $row->document, [
                'path' => $storedDocument,
                'name' => basename($filename),
            ]);

            DB::table('assignement')->where('id', $id)->update(['document' => $document]);
        } catch (QueryException $e) {
            if ($storedDocument) {
                AssignmentFiles::deleteStoredFile($storedDocument);
            }

            if (str_contains(strtolower($e->getMessage()), 'data too long')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Database column is too small for multiple files. Run: ALTER TABLE assignement MODIFY document TEXT NOT NULL;',
                ], 422);
            }

            throw $e;
        } catch (\Throwable $e) {
            if ($storedDocument) {
                AssignmentFiles::deleteStoredFile($storedDocument);
            }

            report($e);

            return response()->json([
                'status' => 'error',
                'message' => 'Could not attach file to this assignment.',
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'File added',
            'assignment_id' => $id,
            'file_count' => AssignmentDocuments::fileCount($document),
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $row = $this->findRow($request, $id);
        if (! $row) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        if ($request->boolean('append_file')) {
            if ($row->type !== 'file') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot append a file to a link entry.',
                ], 422);
            }

            return $this->appendFile($request, $id);
        }

        $validated = $request->validate([
            'append_file' => ['nullable', 'boolean'],
            'batch_name' => ['sometimes', 'required', 'string', 'max:255'],
            'document_name' => ['sometimes', 'required', 'string', 'max:255'],
            'subject_id' => ['nullable', 'integer'],
            'subject_name' => ['nullable', 'string', 'max:255'],
            'link' => ['nullable', 'string', 'max:2000'],
            'file' => ['nullable', 'file', 'max:20480', 'mimes:pdf,txt,jpg,jpeg,png,gif,webp,heic,heif,doc,docx'],
            'file_base64' => ['nullable', 'string'],
            'file_name' => ['nullable', 'string', 'max:255'],
            'file_mime' => ['nullable', 'string', 'max:120'],
        ]);

        $updates = [];
        if (array_key_exists('batch_name', $validated)) {
            $updates['batch_name'] = $validated['batch_name'];
        }
        if (array_key_exists('document_name', $validated)) {
            $updates['document_name'] = $validated['document_name'];
        }
        if (array_key_exists('subject_id', $validated) || array_key_exists('subject_name', $validated)) {
            $subject = AssignmentContent::resolveSubject(
                isset($validated['subject_id']) ? (int) $validated['subject_id'] : null,
                $validated['subject_name'] ?? null,
            );
            $updates = AssignmentContent::buildUpdateData($updates, $subject);
        }

        if ($row->type === 'link' && array_key_exists('link', $validated) && filled($validated['link'])) {
            $updates['document'] = $validated['link'];
        }

        $storedDocument = null;
        $hasMultipart = $row->type === 'file'
            && $request->hasFile('file')
            && $request->file('file')->isValid();

        if ($row->type === 'file' && $hasMultipart) {
            $file = $request->file('file');
            $docName = $validated['document_name'] ?? $row->document_name;
            $ext = $file->getClientOriginalExtension() ?: 'dat';
            $filename = AssignmentFiles::makeFilename($docName, 'upload.'.$ext);
            $contentKind = AssignmentContent::resolveRowKind($row);
            $storedDocument = AssignmentFiles::storeBinary($contentKind, $filename, (string) $file->get());
            $updates['document'] = $storedDocument;
        } elseif ($row->type === 'file' && filled($request->input('file_base64'))) {
            $binary = base64_decode((string) $request->input('file_base64'), true);
            if ($binary === false) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid file data',
                    'errors' => ['file_base64' => ['Could not decode uploaded file.']],
                ], 422);
            }
            if (strlen($binary) > 20480 * 1024) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File is too large (max 20 MB).',
                    'errors' => ['file_base64' => ['File is too large (max 20 MB).']],
                ], 422);
            }

            $docName = $validated['document_name'] ?? $row->document_name;
            $originalName = (string) ($validated['file_name'] ?? 'upload.jpg');
            $filename = AssignmentFiles::makeFilename($docName, $originalName);
            $contentKind = AssignmentContent::resolveRowKind($row);
            $storedDocument = AssignmentFiles::storeBinary($contentKind, $filename, $binary);
            $updates['document'] = $storedDocument;
        }

        if ($updates === []) {
            return response()->json(['status' => 'error', 'message' => 'Nothing to update'], 422);
        }

        DB::table('assignement')->where('id', $id)->update($updates);

        if ($storedDocument && $row->document) {
            AssignmentFiles::deleteStoredFile((string) $row->document);
        }

        $updated = $this->findRow($request, $id);
        $label = AssignmentContent::resolveRowKind($row) === 'note' ? 'Note' : 'Assignment';

        return response()->json([
            'status' => 'success',
            'message' => "{$label} updated",
            'assignment' => $updated ? $this->formatRow($updated) : null,
        ]);
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
            AssignmentFiles::deleteStoredFile((string) $row->document);
        }

        DB::table('assignement')->where('id', $id)->delete();

        return response()->json(['status' => 'success', 'message' => 'Assignment deleted']);
    }

    /**
     * @param  array<int, array{file_base64?: string, file_name?: string}>  $items
     */
    private function storeBundledFiles(
        array $items,
        string $contentKind,
        array $validated,
        array $subject,
        string $label,
        callable $decodeBinary,
    ): JsonResponse {
        $entries = [];
        $storedDocuments = [];

        try {
            foreach ($items as $index => $item) {
                if (! is_array($item)) {
                    continue;
                }

                $binary = $decodeBinary($item);
                if ($binary === null) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid file data',
                        'errors' => ['files_payload' => ['Could not decode uploaded file.']],
                    ], 422);
                }

                $originalName = (string) ($item['file_name'] ?? 'upload.jpg');
                $filename = AssignmentFiles::makeFilename($validated['document_name'], $originalName, $index + 1);
                $storedDocument = AssignmentFiles::storeBinary($contentKind, $filename, $binary);
                $storedDocuments[] = $storedDocument;
                $entries[] = [
                    'path' => $storedDocument,
                    'name' => basename($filename),
                ];
            }

            if ($entries === []) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No file received in the request.',
                    'errors' => ['files_payload' => ['No file received in the request.']],
                ], 422);
            }

            $assignmentId = $this->insertFileAssignment($validated, $contentKind, $subject, $entries);
        } catch (\Throwable $e) {
            foreach ($storedDocuments as $storedDocument) {
                AssignmentFiles::deleteStoredFile($storedDocument);
            }

            throw $e;
        }

        return response()->json([
            'status' => 'success',
            'message' => "{$label} added",
            'count' => 1,
            'file_count' => count($entries),
            'assignment_id' => $assignmentId,
        ]);
    }

    /**
     * @param  array<int, array{path: string, name: string}>  $entries
     */
    private function insertFileAssignment(array $validated, string $contentKind, array $subject, array $entries): int
    {
        return (int) DB::table('assignement')->insertGetId(
            AssignmentContent::buildInsertData([
                'batch_name' => $validated['batch_name'],
                'type' => 'file',
                'document_name' => $validated['document_name'],
                'document' => AssignmentDocuments::encode($entries),
                'status' => 1,
            ], $contentKind, $subject)
        );
    }

    /**
     * @param  array{file_base64?: string}  $item
     */
    private function decodePayloadBinary(array $item, string $field): ?string
    {
        $binary = base64_decode((string) ($item['file_base64'] ?? ''), true);
        if ($binary === false) {
            return null;
        }
        if (strlen($binary) > 20480 * 1024) {
            throw new HttpResponseException(response()->json([
                'status' => 'error',
                'message' => 'File is too large (max 20 MB).',
                'errors' => [$field => ['File is too large (max 20 MB).']],
            ], 422));
        }

        return $binary;
    }
}
