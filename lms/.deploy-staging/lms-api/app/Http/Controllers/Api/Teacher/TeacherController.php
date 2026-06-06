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

        $availableMonths = DB::table('attendance as a')
            ->when($courses->isNotEmpty(), fn ($q) => $q->whereIn('a.course', $courses))
            ->selectRaw("DATE_FORMAT(a.date, '%Y-%m') as month")
            ->distinct()
            ->orderByDesc('month')
            ->pluck('month');

        $rows = DB::table('attendance as a')
            ->leftJoin('stud_details as sd', 'sd.id', '=', 'a.sid')
            ->when($courses->isNotEmpty(), fn ($q) => $q->whereIn('a.course', $courses))
            ->when($month !== 'all', fn ($q) => $q->where('a.date', 'like', $month.'%'))
            ->orderByDesc('a.date')
            ->select('a.*', 'sd.name as student_name')
            ->limit($month === 'all' ? 500 : 1000)
            ->get();

        // Today’s roster counts (must match dashboard “Students” count base):
        // - Total = all students enrolled in the teacher’s assigned courses
        // - Present = those with a “present” attendance row for today
        // - Absent = total - present (unmarked students count as absent)
        $today = now()->format('Y-m-d');

        $todayTotalStudents = 0;
        $todayPresentStudents = 0;
        $todayAbsentStudents = 0;

        if ($courses->isNotEmpty()) {
            $todayCounts = DB::table('stud_details as sd')
                ->whereIn('sd.course_name', $courses)
                ->leftJoin('attendance as a', function ($join) use ($today, $courses) {
                    $join->on('a.sid', '=', 'sd.id')
                        ->where('a.date', $today)
                        ->whereIn('a.course', $courses);
                })
                ->selectRaw('COUNT(sd.id) as total_students')
                ->selectRaw("SUM(CASE WHEN LOWER(a.status) = 'present' THEN 1 ELSE 0 END) as present_students")
                ->selectRaw(
                    "COUNT(sd.id) - SUM(CASE WHEN LOWER(a.status) = 'present' THEN 1 ELSE 0 END) as absent_students"
                )
                ->first();

            $todayTotalStudents = (int) ($todayCounts->total_students ?? 0);
            $todayPresentStudents = (int) ($todayCounts->present_students ?? 0);
            $todayAbsentStudents = (int) ($todayCounts->absent_students ?? 0);
        }

        return response()->json([
            'status' => 'success',
            'month' => $month,
            'available_months' => $availableMonths,
            'records' => $rows,
            'today_total_students' => $todayTotalStudents,
            'today_present_students' => $todayPresentStudents,
            'today_absent_students' => $todayAbsentStudents,
        ]);
    }

    public function salary(Request $request): JsonResponse
    {
        $email = $this->email($request);
        $rows = DB::table('teacher_salary')
            ->where('email', $email)
            ->orderByDesc('srno')
            ->get();

        return response()->json(['status' => 'success', 'salary' => $rows]);
    }

    public function classTests(Request $request): JsonResponse
    {
        $email = $this->email($request);
        $assignedCourses = $this->assignedCourses($email);

        $rows = DB::table('class_tests as ct')
            ->leftJoin('course_details as cd', 'cd.id', '=', 'ct.course_id')
            ->leftJoin('subject as s', 's.id', '=', 'ct.subject_id')
            ->where(function ($q) use ($email, $assignedCourses) {
                $q->where('ct.created_by', $email);
                if ($assignedCourses->isNotEmpty()) {
                    $q->orWhere(function ($q2) use ($assignedCourses) {
                        $q2->where('ct.created_by_role', 'admin')
                            ->whereIn('cd.course_name', $assignedCourses);
                    });
                }
            })
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
            ->leftJoin('course_details as cd', 'cd.id', '=', 'ct.course_id')
            ->leftJoin('subject as s', 's.id', '=', 'ct.subject_id')
            ->leftJoin('stud_details as sd', 'sd.id', '=', 'tr.student_id')
            ->select(
                'tr.*',
                'ct.test_name',
                'ct.test_date',
                'ct.total_marks',
                'ct.passing_marks',
                'cd.course_name',
                's.subject_name',
                'sd.name as student_name'
            )
            ->orderByDesc('ct.test_date')
            ->orderBy('sd.name');

        if ($testId) {
            $q->where('tr.test_id', $testId);
        }
        if ($studentId) {
            $q->where('tr.student_id', $studentId);
        }

        return response()->json(['status' => 'success', 'results' => $q->limit(1000)->get()]);
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
