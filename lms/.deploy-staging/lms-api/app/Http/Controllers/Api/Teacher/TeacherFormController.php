<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Services\ClassTestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherFormController extends Controller
{
    public function __construct(private ClassTestService $classTests) {}

    private function email(Request $request): string
    {
        return (string) $request->attributes->get('api_user')['email'];
    }

    public function sessions(): JsonResponse
    {
        $rows = DB::table('academic_sessions')
            ->where('status', 1)
            ->orderByDesc('session_name')
            ->get(['id', 'session_name']);

        return response()->json(['status' => 'success', 'sessions' => $rows]);
    }

    public function assignedCourses(Request $request): JsonResponse
    {
        $email = $this->email($request);
        $courses = DB::table('course_assign')
            ->where('email', $email)
            ->pluck('course')
            ->unique()
            ->values();

        return response()->json(['status' => 'success', 'courses' => $courses]);
    }

    public function batches(Request $request): JsonResponse
    {
        $course = $request->query('course');
        if (! $course) {
            return response()->json(['status' => 'error', 'message' => 'course required'], 422);
        }

        $batches = DB::table('batches')
            ->where('course', $course)
            ->where('status', 1)
            ->orderBy('name')
            ->pluck('name');

        return response()->json(['status' => 'success', 'batches' => $batches]);
    }

    public function studentsForAttendance(Request $request): JsonResponse
    {
        $data = $request->validate([
            'course' => ['required', 'string'],
            'session' => ['required', 'string'],
            'batch' => ['nullable', 'string'],
        ]);

        $q = DB::table('stud_details')
            ->where('course_name', $data['course'])
            ->where('session_name', $data['session']);

        if (! empty($data['batch'])) {
            $q->where('batch', $data['batch']);
        }

        $students = $q->orderBy('name')->get(['id', 'name', 'batch', 'uid']);

        return response()->json(['status' => 'success', 'students' => $students]);
    }

    public function saveAttendance(Request $request): JsonResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'course' => ['required', 'string'],
            'batch' => ['required', 'string'],
            'records' => ['required', 'array'],
            'records.*.student_id' => ['required'],
            'records.*.status' => ['required', 'in:present,absent'],
        ]);

        $saved = 0;
        foreach ($data['records'] as $row) {
            $sid = (string) $row['student_id'];
            $exists = DB::table('attendance')
                ->where('sid', $sid)
                ->where('date', $data['date'])
                ->exists();

            if ($exists) {
                DB::table('attendance')
                    ->where('sid', $sid)
                    ->where('date', $data['date'])
                    ->update(['status' => $row['status'], 'course' => $data['course'], 'batch' => $data['batch']]);
            } else {
                DB::table('attendance')->insert([
                    'sid' => $sid,
                    'date' => $data['date'],
                    'course' => $data['course'],
                    'batch' => $data['batch'],
                    'status' => $row['status'],
                ]);
            }
            $saved++;
        }

        return response()->json(['status' => 'success', 'message' => 'Attendance saved', 'saved' => $saved]);
    }

    public function classTestCourses(Request $request): JsonResponse
    {
        $session = $request->query('session_name');
        $courses = $this->classTests->listCoursesTeacher($this->email($request), $session ?: null);

        return response()->json(['status' => 'success', 'courses' => $courses]);
    }

    public function classTestSubjects(Request $request): JsonResponse
    {
        $courseId = (int) $request->query('course_id');
        if ($courseId <= 0) {
            return response()->json(['status' => 'error', 'message' => 'course_id required'], 422);
        }

        $subjects = $this->classTests->listSubjectsTeacher($this->email($request), $courseId);

        return response()->json(['status' => 'success', 'subjects' => $subjects]);
    }

    public function classTestList(Request $request): JsonResponse
    {
        $courseId = (int) $request->query('course_id');
        $subjectId = (int) $request->query('subject_id');
        if ($courseId <= 0 || $subjectId <= 0) {
            return response()->json(['status' => 'error', 'message' => 'course_id and subject_id required'], 422);
        }

        $tests = $this->classTests->listTests($courseId, $subjectId, $this->email($request));

        return response()->json(['status' => 'success', 'tests' => $tests]);
    }

    public function createClassTest(Request $request): JsonResponse
    {
        $data = $request->validate([
            'course_id' => ['required', 'integer'],
            'subject_id' => ['required', 'integer'],
            'test_name' => ['required', 'string', 'max:255'],
            'test_date' => ['required', 'date'],
            'total_marks' => ['required', 'numeric', 'min:1'],
            'passing_marks' => ['required', 'numeric', 'min:0'],
        ]);

        $res = $this->classTests->createTest(
            $this->email($request),
            $data['test_name'],
            (int) $data['course_id'],
            (int) $data['subject_id'],
            $data['test_date'],
            (float) $data['total_marks'],
            (float) $data['passing_marks']
        );

        if (! ($res['ok'] ?? false)) {
            return response()->json(['status' => 'error', 'message' => $res['error'] ?? 'Failed'], 422);
        }

        return response()->json(['status' => 'success', 'message' => 'Class test created', 'id' => $res['id']]);
    }

    public function studentsForMarks(Request $request): JsonResponse
    {
        $testId = (int) $request->query('test_id');
        if ($testId <= 0) {
            return response()->json(['status' => 'error', 'message' => 'test_id required'], 422);
        }

        $session = $request->query('session_name');
        $batch = trim((string) $request->query('batch', ''));

        $res = $this->classTests->studentsMarks(
            $testId,
            $this->email($request),
            $session ?: null,
            $batch !== '' ? $batch : null
        );

        if (! ($res['ok'] ?? false)) {
            return response()->json(['status' => 'error', 'message' => $res['error'] ?? 'Failed'], 422);
        }

        return response()->json(['status' => 'success', ...$res]);
    }

    public function saveClassTestMarks(Request $request): JsonResponse
    {
        $data = $request->validate([
            'test_id' => ['required', 'integer'],
            'marks' => ['required', 'array'],
        ]);

        $res = $this->classTests->saveMarks(
            (int) $data['test_id'],
            $this->email($request),
            $data['marks']
        );

        if (! ($res['ok'] ?? false)) {
            return response()->json(['status' => 'error', 'message' => $res['error'] ?? 'Failed'], 422);
        }

        return response()->json(['status' => 'success', 'message' => 'Marks saved', 'saved' => $res['saved']]);
    }
}
