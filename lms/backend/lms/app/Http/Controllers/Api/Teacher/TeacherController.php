<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    private function email(Request $request): string
    {
        return (string) $request->attributes->get('api_user')['email'];
    }

    private function assignedCourses(string $email)
    {
        return DB::table('course_assign')->where('email', $email)->pluck('course');
    }

    public function assignments(Request $request): JsonResponse
    {
        $courses = $this->assignedCourses($this->email($request));
        $batches = DB::table('batches')
            ->when($courses->isNotEmpty(), fn ($q) => $q->whereIn('course', $courses))
            ->pluck('name');

        $rows = DB::table('assignement')
            ->when($batches->isNotEmpty(), fn ($q) => $q->whereIn('batch_name', $batches))
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        return response()->json(['status' => 'success', 'assignments' => $rows]);
    }

    public function attendance(Request $request): JsonResponse
    {
        $email = $this->email($request);
        $courses = $this->assignedCourses($email);
        $month = $request->query('month', now()->format('Y-m'));

        $rows = DB::table('attendance')
            ->when($courses->isNotEmpty(), fn ($q) => $q->whereIn('course', $courses))
            ->where('date', 'like', $month.'%')
            ->orderByDesc('date')
            ->limit(200)
            ->get();

        return response()->json(['status' => 'success', 'month' => $month, 'records' => $rows]);
    }

    public function salary(Request $request): JsonResponse
    {
        $email = $this->email($request);
        $rows = DB::table('teacher_salary')
            ->where('email', $email)
            ->orderByDesc('id')
            ->get();

        return response()->json(['status' => 'success', 'salary' => $rows]);
    }

    public function classTests(Request $request): JsonResponse
    {
        $email = $this->email($request);
        $rows = DB::table('class_tests as ct')
            ->leftJoin('course_details as cd', 'cd.id', '=', 'ct.course_id')
            ->leftJoin('subject as s', 's.id', '=', 'ct.subject_id')
            ->where('ct.created_by', $email)
            ->orWhere('ct.created_by_role', 'admin')
            ->orderByDesc('ct.test_date')
            ->select('ct.*', 'cd.course_name', 's.subject_name')
            ->limit(50)
            ->get();

        return response()->json(['status' => 'success', 'tests' => $rows]);
    }

    public function testResults(Request $request): JsonResponse
    {
        $studentId = $request->query('student_id');
        $testId = $request->query('test_id');

        $q = DB::table('test_results as tr')
            ->join('class_tests as ct', 'ct.id', '=', 'tr.test_id')
            ->leftJoin('stud_details as sd', 'sd.id', '=', 'tr.student_id')
            ->select('tr.*', 'ct.test_name', 'ct.test_date', 'ct.total_marks', 'sd.name as student_name');

        if ($testId) {
            $q->where('tr.test_id', $testId);
        }
        if ($studentId) {
            $q->where('tr.student_id', $studentId);
        }

        return response()->json(['status' => 'success', 'results' => $q->limit(100)->get()]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:4'],
        ]);
        $email = $this->email($request);
        $teacher = DB::table('teacher')->where('email', $email)->first();

        if (! $teacher || $teacher->password !== $data['current_password']) {
            return response()->json(['status' => 'error', 'message' => 'Current password is incorrect'], 422);
        }

        DB::table('teacher')->where('email', $email)->update(['password' => $data['new_password']]);

        return response()->json(['status' => 'success', 'message' => 'Password updated']);
    }
}
