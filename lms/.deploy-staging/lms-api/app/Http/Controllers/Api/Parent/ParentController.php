<?php

namespace App\Http\Controllers\Api\Parent;

use App\Http\Controllers\Controller;
use App\Support\AssignmentFiles;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParentController extends Controller
{
    private function childId(Request $request): string
    {
        return (string) $request->attributes->get('api_user')['parent_id'];
    }

    public function assignments(Request $request): JsonResponse
    {
        $id = $this->childId($request);
        $profile = DB::table('stud_details')->where('id', $id)->first();
        $batch = $profile->batch ?? '';

        $rows = DB::table('assignement')
            ->where('batch_name', $batch)
            ->where('status', 1)
            ->orderByDesc('id')
            ->get()
            ->map(function ($row) {
                $data = (array) $row;
                if ($row->type === 'link') {
                    $data['link_url'] = $row->document;
                }

                return $data;
            });

        return response()->json(['status' => 'success', 'assignments' => $rows]);
    }

    public function showAssignment(Request $request, int $id): JsonResponse
    {
        $studentId = $this->childId($request);
        $profile = DB::table('stud_details')->where('id', $studentId)->first();
        $batch = $profile->batch ?? '';

        $row = DB::table('assignement')
            ->where('id', $id)
            ->where('batch_name', $batch)
            ->where('status', 1)
            ->first();

        if (! $row) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $data = (array) $row;
        if ($row->type === 'link') {
            $data['link_url'] = $row->document;
        }

        return response()->json(['status' => 'success', 'assignment' => $data]);
    }

    public function downloadAssignment(Request $request, int $id): \Symfony\Component\HttpFoundation\BinaryFileResponse|JsonResponse
    {
        $studentId = $this->childId($request);
        $profile = DB::table('stud_details')->where('id', $studentId)->first();
        $batch = $profile->batch ?? '';

        $row = DB::table('assignement')
            ->where('id', $id)
            ->where('batch_name', $batch)
            ->where('status', 1)
            ->first();

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

    public function classTestResults(Request $request): JsonResponse
    {
        $id = $this->childId($request);
        $rows = DB::table('test_results as tr')
            ->join('class_tests as ct', 'ct.id', '=', 'tr.test_id')
            ->leftJoin('subject as s', 's.id', '=', 'ct.subject_id')
            ->where('tr.student_id', $id)
            ->select('tr.*', 'ct.test_name', 'ct.test_date', 'ct.total_marks', 'ct.passing_marks', 's.subject_name')
            ->orderByDesc('ct.test_date')
            ->get();

        return response()->json(['status' => 'success', 'results' => $rows]);
    }

    public function allTestMarks(Request $request): JsonResponse
    {
        return $this->classTestResults($request);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:4'],
        ]);
        $id = $this->childId($request);
        $parent = DB::table('parent')->where('id', $id)->first();

        if (! $parent || $parent->pass !== $data['current_password']) {
            return response()->json(['status' => 'error', 'message' => 'Current password is incorrect'], 422);
        }

        DB::table('parent')->where('id', $id)->update(['pass' => $data['new_password']]);

        return response()->json(['status' => 'success', 'message' => 'Password updated']);
    }
}
