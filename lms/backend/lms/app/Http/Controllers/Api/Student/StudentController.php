<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Support\AssignmentFiles;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    private function studentId(Request $request): string
    {
        return (string) $request->attributes->get('api_user')['id'];
    }

    public function courses(Request $request): JsonResponse
    {
        $id = $this->studentId($request);
        $taken = DB::table('courses_taken')->where('sid', $id)->first();
        $profile = DB::table('stud_details')->where('id', $id)->first();
        $course = $profile && $profile->course_name
            ? DB::table('course_details')->where('course_name', $profile->course_name)->first()
            : null;

        return response()->json([
            'status' => 'success',
            'courses_taken' => $taken,
            'current_course' => $course,
            'profile' => $profile,
        ]);
    }

    public function attendance(Request $request): JsonResponse
    {
        $id = $this->studentId($request);
        $summary = DB::table('attendance')
            ->selectRaw("sid, course, batch, COUNT(DISTINCT date) AS total_days,
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) AS present_days,
                SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) AS absent_days")
            ->where('sid', $id)
            ->groupBy('sid', 'course', 'batch')
            ->get();

        $records = DB::table('attendance')
            ->where('sid', $id)
            ->orderByDesc('date')
            ->limit(60)
            ->get();

        return response()->json([
            'status' => 'success',
            'summary' => $summary,
            'records' => $records,
        ]);
    }

    public function assignments(Request $request): JsonResponse
    {
        $id = $this->studentId($request);
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
        $studentId = $this->studentId($request);
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
        $studentId = $this->studentId($request);
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

    public function updateProfile(Request $request): JsonResponse
    {
        $id = $this->studentId($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'age' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'school_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'aadhar' => ['nullable', 'string', 'max:20'],
        ]);

        DB::table('stud_details')->where('id', $id)->update([
            'name' => $data['name'],
            'age' => $data['age'] ?? '',
            'mobile' => $data['mobile'] ?? '',
            'school_name' => $data['school_name'] ?? '',
            'email' => $data['email'] ?? '',
            'city' => $data['city'] ?? '',
            'state' => $data['state'] ?? '',
            'aadhar' => $data['aadhar'] ?? '',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated',
            'profile' => DB::table('stud_details')->where('id', $id)->first(),
        ]);
    }

    public function transactions(Request $request): JsonResponse
    {
        $id = $this->studentId($request);
        $rows = DB::table('transaction_history')
            ->where('sid', $id)
            ->orderByDesc('id')
            ->get();

        return response()->json(['status' => 'success', 'transactions' => $rows]);
    }

    public function classTestResults(Request $request): JsonResponse
    {
        $id = $this->studentId($request);
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
        $id = $this->studentId($request);
        $user = DB::table('users')->where('sid', $id)->first();

        if (! $user || $user->pass !== $data['current_password']) {
            return response()->json(['status' => 'error', 'message' => 'Current password is incorrect'], 422);
        }

        DB::table('users')->where('sid', $id)->update(['pass' => $data['new_password']]);

        return response()->json(['status' => 'success', 'message' => 'Password updated']);
    }
}
