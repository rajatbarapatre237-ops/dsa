<?php

namespace App\Support;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AssignmentContent
{
    private static ?bool $hasKindColumn = null;

    private static ?bool $hasSubjectIdColumn = null;

    private static ?bool $hasSubjectNameColumn = null;

    public static function hasKindColumn(): bool
    {
        return self::$hasKindColumn ??= Schema::hasTable('assignement')
            && Schema::hasColumn('assignement', 'content_kind');
    }

    public static function hasSubjectIdColumn(): bool
    {
        return self::$hasSubjectIdColumn ??= Schema::hasTable('assignement')
            && Schema::hasColumn('assignement', 'subject_id');
    }

    public static function hasSubjectNameColumn(): bool
    {
        return self::$hasSubjectNameColumn ??= Schema::hasTable('assignement')
            && Schema::hasColumn('assignement', 'subject_name');
    }

    public static function normalizeKind(?string $kind): string
    {
        return $kind === 'note' ? 'note' : 'assignment';
    }

    public static function inferKindFromDocument(?string $document): string
    {
        $path = ltrim(str_replace('\\', '/', (string) $document), '/');

        return str_starts_with($path, AssignmentFiles::NOTES_STORAGE_PATH.'/') ? 'note' : 'assignment';
    }

    public static function resolveRowKind(object $row): string
    {
        if (self::hasKindColumn() && isset($row->content_kind) && $row->content_kind !== '') {
            return self::normalizeKind((string) $row->content_kind);
        }

        return self::inferKindFromDocument($row->document ?? null);
    }

    public static function applyKindFilter(Builder $query, ?string $kind): Builder
    {
        if (! in_array($kind, ['assignment', 'note'], true)) {
            return $query;
        }

        if (self::hasKindColumn()) {
            return $query->where('content_kind', $kind);
        }

        if ($kind === 'note') {
            return $query->where(function (Builder $q) {
                $q->where('document', 'like', AssignmentFiles::NOTES_STORAGE_PATH.'/%')
                    ->orWhere('document', 'like', '%/'.AssignmentFiles::NOTES_STORAGE_PATH.'/%');
            });
        }

        return $query->where(function (Builder $q) {
            $q->where('document', 'not like', AssignmentFiles::NOTES_STORAGE_PATH.'/%')
                ->where('document', 'not like', '%/'.AssignmentFiles::NOTES_STORAGE_PATH.'/%');
        });
    }

    public static function accessibleBatchNames(): Collection
    {
        return DB::table('batches')
            ->where('status', 1)
            ->orderBy('name')
            ->pluck('name');
    }

    public static function buildInsertData(array $core, string $contentKind, array $subject): array
    {
        $data = $core;

        if (self::hasKindColumn()) {
            $data['content_kind'] = self::normalizeKind($contentKind);
        }
        if (self::hasSubjectIdColumn()) {
            $data['subject_id'] = $subject['subject_id'];
        }
        if (self::hasSubjectNameColumn()) {
            $data['subject_name'] = $subject['subject_name'];
        }

        return $data;
    }

    public static function buildUpdateData(array $updates, ?array $subject = null): array
    {
        if ($subject !== null) {
            if (self::hasSubjectIdColumn()) {
                $updates['subject_id'] = $subject['subject_id'];
            }
            if (self::hasSubjectNameColumn()) {
                $updates['subject_name'] = $subject['subject_name'];
            }
        }

        return $updates;
    }

    public static function subjectsWithCountsForBatch(string $batch, string $kind): Collection
    {
        if ($batch === '') {
            return collect();
        }

        $query = DB::table('assignement')
            ->where('batch_name', $batch)
            ->where('status', 1);

        self::applyKindFilter($query, $kind);

        if (! self::hasSubjectIdColumn() && ! self::hasSubjectNameColumn()) {
            $total = (clone $query)->count();
            if ($total <= 0) {
                return collect();
            }

            return collect([(object) [
                'subject_id' => null,
                'subject_name' => 'General',
                'item_count' => $total,
            ]]);
        }

        return (clone $query)
            ->selectRaw('subject_id, subject_name, COUNT(*) as item_count')
            ->groupBy('subject_id', 'subject_name')
            ->orderBy('subject_name')
            ->get();
    }

    public static function formatRow(object $row): array
    {
        $data = (array) $row;
        if (($row->type ?? '') === 'link') {
            $data['link_url'] = $row->document;
        }

        $files = ($row->type ?? '') === 'file'
            ? AssignmentDocuments::decode($row->document ?? null)
            : [];

        $data['file_count'] = count($files);
        $data['files'] = array_map(
            fn (array $file, int $index) => [
                'index' => $index,
                'name' => $file['name'],
                'path' => $file['path'],
            ],
            $files,
            array_keys($files),
        );

        if (count($files) === 1) {
            $data['document'] = $files[0]['path'];
            $data['file_name'] = $files[0]['name'];
        } elseif (count($files) > 1) {
            $data['document'] = $files[0]['path'];
            $data['file_name'] = $files[0]['name'];
        }

        $data['content_kind'] = self::resolveRowKind($row);
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
