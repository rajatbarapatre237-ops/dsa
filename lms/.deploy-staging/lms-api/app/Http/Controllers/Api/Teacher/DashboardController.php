<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $email = $request->attributes->get('api_user')['email'];

        $teacher = DB::table('teacher')->where('email', $email)->first();
        $courses = DB::table('courses_subjects')
            ->where('teacher_email', $email)
            ->get(['id', 'course_name', 'subject_name']);

        $assigned = DB::table('course_assign')
            ->where('email', $email)
            ->distinct()
            ->pluck('course');

        $studentCount = DB::table('stud_details')
            ->whereIn('course_name', $assigned->isEmpty() ? [-1] : $assigned)
            ->count();

        return response()->json([
            'status' => 'success',
            'dashboard' => [
                'teacher' => $teacher,
                'courses' => $courses,
                'assigned_courses' => $assigned,
                'student_count' => $studentCount,
                'menu' => [
                    ['key' => 'students', 'title' => 'View Students'],
                    ['key' => 'attendance', 'title' => 'Attendance'],
                    ['key' => 'assignments', 'title' => 'Assignments'],
                    ['key' => 'class_tests', 'title' => 'Class Tests'],
                    ['key' => 'salary', 'title' => 'Salary'],
                ],
            ],
        ]);
    }

    public function students(Request $request): JsonResponse
    {
        $email = $request->attributes->get('api_user')['email'];
        $assigned = DB::table('course_assign')->where('email', $email)->pluck('course');

        $page = max(1, (int) $request->query('page', 1));
        $perPage = min(50, max(10, (int) $request->query('per_page', 20)));
        $search = trim((string) $request->query('search', ''));
        $course = trim((string) $request->query('course', ''));
        $batch = trim((string) $request->query('batch', ''));

        $q = DB::table('stud_details')
            ->when($assigned->isNotEmpty(), fn ($qq) => $qq->whereIn('course_name', $assigned))
            ->when($search !== '', function ($qq) use ($search) {
                $qq->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', '%'.$search.'%')
                        ->orWhere('id', 'like', '%'.$search.'%');
                });
            })
            ->when($course !== '', fn ($qq) => $qq->where('course_name', $course))
            ->when($batch !== '', fn ($qq) => $qq->where('batch', $batch));

        $total = (clone $q)->count();
        $students = $q
            ->orderByDesc('id')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        return response()->json([
            'status' => 'success',
            'students' => $students,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => max(1, (int) ceil($total / $perPage)),
            ],
        ]);
    }

    public function showStudent(Request $request, string $id): JsonResponse
    {
        $email = $request->attributes->get('api_user')['email'];
        $assigned = DB::table('course_assign')->where('email', $email)->pluck('course');

        $student = DB::table('stud_details')
            ->where('id', $id)
            ->when($assigned->isNotEmpty(), fn ($q) => $q->whereIn('course_name', $assigned))
            ->first();

        if (! $student) {
            return response()->json(['status' => 'error', 'message' => 'Student not found'], 404);
        }

        $attendance = DB::table('attendance')
            ->where('sid', $id)
            ->orderByDesc('date')
            ->limit(15)
            ->get();

        return response()->json([
            'status' => 'success',
            'student' => $student,
            'recent_attendance' => $attendance,
        ]);
    }
}
