<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ClassTestService
{
    public function courseName(int $courseId): ?string
    {
        return DB::table('course_details')->where('id', $courseId)->value('course_name');
    }

    public function teacherAssigned(string $email, int $courseId, int $subjectId): bool
    {
        $cn = $this->courseName($courseId);
        if (! $cn) {
            return false;
        }

        return DB::table('subject as s')
            ->join('courses_subjects as cs', function ($j) {
                $j->on('cs.course_name', '=', 's.course_name')
                    ->on('cs.subject_name', '=', 's.subject_name');
            })
            ->where('s.id', $subjectId)
            ->where('s.course_name', $cn)
            ->where('cs.teacher_email', $email)
            ->exists();
    }

    public function listCoursesTeacher(string $email, ?string $sessionName = null): array
    {
        $q = DB::table('course_details as cd')
            ->join('courses_subjects as cs', 'cs.course_name', '=', 'cd.course_name')
            ->where('cs.teacher_email', $email)
            ->select('cd.id', 'cd.course_name')
            ->distinct();

        if ($sessionName) {
            $q->join('stud_details as sd', 'sd.course_name', '=', 'cd.course_name')
                ->where('sd.session_name', $sessionName);
        }

        return $q->orderBy('cd.course_name')->get()->map(fn ($r) => [
            'id' => (int) $r->id,
            'course_name' => $r->course_name,
        ])->all();
    }

    public function listSubjectsTeacher(string $email, int $courseId): array
    {
        $cn = $this->courseName($courseId);
        if (! $cn) {
            return [];
        }

        return DB::table('subject as s')
            ->join('courses_subjects as cs', function ($j) {
                $j->on('cs.course_name', '=', 's.course_name')
                    ->on('cs.subject_name', '=', 's.subject_name');
            })
            ->where('s.course_name', $cn)
            ->where('cs.teacher_email', $email)
            ->select('s.id', 's.subject_name')
            ->distinct()
            ->orderBy('s.subject_name')
            ->get()
            ->map(fn ($r) => ['id' => (int) $r->id, 'subject_name' => $r->subject_name])
            ->all();
    }

    public function listTests(int $courseId, int $subjectId, ?string $teacherEmail): array
    {
        $q = DB::table('class_tests as ct')
            ->where('ct.course_id', $courseId)
            ->where('ct.subject_id', $subjectId);

        if ($teacherEmail) {
            $cn = $this->courseName($courseId);
            $q->join('course_details as cd', 'cd.id', '=', 'ct.course_id')
                ->join('subject as s', 's.id', '=', 'ct.subject_id')
                ->join('courses_subjects as cs', function ($j) {
                    $j->on('cs.course_name', '=', 'cd.course_name')
                        ->on('cs.subject_name', '=', 's.subject_name');
                })
                ->where('cs.teacher_email', $teacherEmail);
        }

        return $q->orderByDesc('ct.test_date')
            ->select('ct.id', 'ct.test_name', 'ct.test_date', 'ct.total_marks', 'ct.passing_marks')
            ->get()
            ->map(fn ($r) => [
                'id' => (int) $r->id,
                'test_name' => $r->test_name,
                'test_date' => $r->test_date,
                'total_marks' => (float) $r->total_marks,
                'passing_marks' => (float) $r->passing_marks,
            ])
            ->all();
    }

    public function createTest(
        string $email,
        string $testName,
        int $courseId,
        int $subjectId,
        string $testDate,
        float $totalMarks,
        float $passingMarks
    ): array {
        if (! $this->teacherAssigned($email, $courseId, $subjectId)) {
            return ['ok' => false, 'error' => 'You are not assigned to this course/subject'];
        }
        if ($testName === '' || $totalMarks <= 0 || $passingMarks < 0 || $passingMarks > $totalMarks) {
            return ['ok' => false, 'error' => 'Invalid test data'];
        }

        $exists = DB::table('subject')->where('id', $subjectId)
            ->where('course_name', $this->courseName($courseId))
            ->exists();
        if (! $exists) {
            return ['ok' => false, 'error' => 'Invalid course/subject'];
        }

        try {
            $id = DB::table('class_tests')->insertGetId([
                'test_name' => $testName,
                'course_id' => $courseId,
                'subject_id' => $subjectId,
                'test_date' => $testDate,
                'total_marks' => $totalMarks,
                'passing_marks' => $passingMarks,
                'created_by_role' => 'teacher',
                'created_by' => $email,
            ]);

            return ['ok' => true, 'id' => $id];
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), '1062')) {
                return ['ok' => false, 'error' => 'A test with this name and date already exists.'];
            }

            return ['ok' => false, 'error' => 'Could not save test'];
        }
    }

    public function getTestRow(int $testId): ?object
    {
        return DB::table('class_tests as ct')
            ->leftJoin('course_details as cd', 'cd.id', '=', 'ct.course_id')
            ->leftJoin('subject as s', 's.id', '=', 'ct.subject_id')
            ->where('ct.id', $testId)
            ->select('ct.*', 'cd.course_name', 's.subject_name')
            ->first();
    }

    public function studentsForCourse(int $courseId, ?string $sessionName = null, ?string $batch = null): array
    {
        $cn = $this->courseName($courseId);
        if (! $cn) {
            return [];
        }

        $q = DB::table('stud_details')
            ->where('course_name', $cn)
            ->orderBy('name');

        if ($sessionName) {
            $q->where('session_name', $sessionName);
        }
        if ($batch) {
            $q->where('batch', $batch);
        }

        return $q->get(['id', 'name', 'uid', 'batch'])->map(fn ($r) => [
            'student_id' => (int) $r->id,
            'name' => $r->name,
            'uid' => $r->uid,
            'batch' => $r->batch,
        ])->all();
    }

    public function studentsMarks(int $testId, string $email, ?string $sessionName, ?string $batch): array
    {
        $test = $this->getTestRow($testId);
        if (! $test) {
            return ['ok' => false, 'error' => 'Test not found'];
        }
        if (! $this->teacherAssigned($email, (int) $test->course_id, (int) $test->subject_id)) {
            return ['ok' => false, 'error' => 'Forbidden'];
        }

        $students = $this->studentsForCourse((int) $test->course_id, $sessionName, $batch ?: null);
        $existing = DB::table('test_results')->where('test_id', $testId)->pluck('marks_obtained', 'student_id');

        foreach ($students as &$s) {
            $s['marks_obtained'] = isset($existing[$s['student_id']]) ? (float) $existing[$s['student_id']] : null;
        }

        return [
            'ok' => true,
            'test' => [
                'id' => (int) $test->id,
                'test_name' => $test->test_name,
                'test_date' => $test->test_date,
                'total_marks' => (float) $test->total_marks,
                'passing_marks' => (float) $test->passing_marks,
                'course_name' => $test->course_name,
                'subject_name' => $test->subject_name,
            ],
            'students' => $students,
        ];
    }

    public function saveMarks(int $testId, string $email, array $marksByStudent): array
    {
        $test = $this->getTestRow($testId);
        if (! $test) {
            return ['ok' => false, 'error' => 'Test not found'];
        }
        if (! $this->teacherAssigned($email, (int) $test->course_id, (int) $test->subject_id)) {
            return ['ok' => false, 'error' => 'Forbidden'];
        }

        $total = (float) $test->total_marks;
        $saved = 0;

        foreach ($marksByStudent as $sid => $val) {
            $sid = (int) $sid;
            if ($sid <= 0 || $val === '' || $val === null) {
                continue;
            }
            $m = (float) $val;
            if ($m < 0 || $m > $total) {
                return ['ok' => false, 'error' => "Marks must be between 0 and {$total}"];
            }
            DB::table('test_results')->updateOrInsert(
                ['test_id' => $testId, 'student_id' => $sid],
                ['marks_obtained' => $m]
            );
            $saved++;
        }

        return ['ok' => true, 'saved' => $saved];
    }
}
