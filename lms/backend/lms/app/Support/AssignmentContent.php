<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AssignmentContent
{
    public static function formatRow(object $row): array
    {
        $data = (array) $row;
        if (($row->type ?? '') === 'link') {
            $data['link_url'] = $row->document;
        }
        $data['content_kind'] = $row->content_kind ?? 'assignment';
        $data['subject_id'] = isset($row->subject_id) ? (int) $row->subject_id : null;
        $data['subject_name'] = $row->subject_name ?? null;

        return $data;
    }

    public static function subjectsForCourse(?string $courseName, ?string $teacherEmail = null): Collection
    {
        if (! $courseName) {
            return collect();
        }

        $query = DB::table('courses_subjects as cs')
            ->join('subject as s', function ($join) {
                $join->on('cs.course_name', '=', 's.course_name')
                    ->on('cs.subject_name', '=', 's.subject_name');
            })
            ->where('cs.course_name', $courseName)
            ->when($teacherEmail, fn ($q) => $q->where('cs.teacher_email', $teacherEmail));

        $rows = $query
            ->orderBy('s.subject_name')
            ->get(['s.id', 's.subject_name']);

        if ($rows->isEmpty() && $teacherEmail) {
            return DB::table('subject as s')
                ->where('s.course_name', $courseName)
                ->orderBy('s.subject_name')
                ->get(['s.id', 's.subject_name']);
        }

        return $rows->unique('id')->values();
    }

    public static function subjectsForBatch(?string $batchName, ?string $teacherEmail = null): Collection
    {
        if (! $batchName) {
            return collect();
        }

        $course = DB::table('batches')->where('name', $batchName)->value('course');

        return self::subjectsForCourse($course ? (string) $course : null, $teacherEmail);
    }

    public static function resolveSubject(?int $subjectId, ?string $subjectName): array
    {
        if ($subjectId) {
            $name = DB::table('subject')->where('id', $subjectId)->value('subject_name');

            return [
                'subject_id' => $subjectId,
                'subject_name' => $name ? (string) $name : ($subjectName ?: null),
            ];
        }

        return [
            'subject_id' => null,
            'subject_name' => $subjectName ?: null,
        ];
    }
}
