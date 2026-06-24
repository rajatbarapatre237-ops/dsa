<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class AssignmentFiles
{
    public const ASSIGNMENTS_STORAGE_PATH = 'assignments';

    public const NOTES_STORAGE_PATH = 'notes';

    /** @deprecated Use ASSIGNMENTS_STORAGE_PATH */
    public const PUBLIC_STORAGE_PATH = self::ASSIGNMENTS_STORAGE_PATH;

    public static function storageDirForKind(string $contentKind): string
    {
        return AssignmentContent::normalizeKind($contentKind) === 'note'
            ? self::NOTES_STORAGE_PATH
            : self::ASSIGNMENTS_STORAGE_PATH;
    }

    public static function makeFilename(string $documentName, string $originalName, int $sequence = 1): string
    {
        $ext = pathinfo($originalName, PATHINFO_EXTENSION) ?: 'jpg';
        $base = preg_replace('/[^a-zA-Z0-9_-]/', '_', $documentName).'_'.date('d-m-Y');
        if ($sequence > 1) {
            $base .= '_'.$sequence;
        }

        return $base.'.'.$ext;
    }

    public static function storeBinary(string $contentKind, string $filename, string $binary): string
    {
        $dir = self::storageDirForKind($contentKind);
        $stored = $dir.'/'.basename($filename);
        Storage::disk('public')->put($stored, $binary);

        return $stored;
    }

    public static function deleteStoredFile(?string $document): void
    {
        if (AssignmentDocuments::isBundle($document)) {
            AssignmentDocuments::deleteAll($document);

            return;
        }

        $document = ltrim(str_replace('\\', '/', (string) $document), '/');
        if ($document === '') {
            return;
        }

        Storage::disk('public')->delete($document);

        if (! str_contains($document, '/')) {
            Storage::disk('public')->delete(self::ASSIGNMENTS_STORAGE_PATH.'/'.$document);
            Storage::disk('public')->delete(self::NOTES_STORAGE_PATH.'/'.$document);
        }
    }

    public static function publicUrl(string $filename): string
    {
        $name = basename(ltrim(str_replace('\\', '/', $filename), '/'));

        return rtrim((string) config('app.url'), '/')
            .'/assignments/files/'.rawurlencode($name);
    }

    public static function publicUrlById(int $id): string
    {
        return rtrim((string) config('app.url'), '/').'/assignments/download/'.$id;
    }

    public static function resolvePath(string $filename): ?string
    {
        $filename = ltrim(str_replace('\\', '/', $filename), '/');
        $basename = basename($filename);

        $candidates = [];

        if (str_contains($filename, '/')) {
            $candidates[] = storage_path('app/public/'.$filename);
            $candidates[] = public_path('storage/'.$filename);
        }

        foreach ([self::ASSIGNMENTS_STORAGE_PATH, self::NOTES_STORAGE_PATH] as $dir) {
            $candidates[] = storage_path('app/public/'.$dir.'/'.$basename);
            $candidates[] = public_path('storage/'.$dir.'/'.$basename);
            $candidates[] = storage_path('app/public/'.$dir.'/'.$filename);
            $candidates[] = public_path('storage/'.$dir.'/'.$filename);
        }

        $candidates[] = base_path('../../www/admin/documents/'.$basename);
        $candidates[] = base_path('../../www/teacher/documents/'.$basename);

        foreach ($candidates as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    public static function mimeType(string $path): string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($ext) {
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'heic' => 'image/heic',
            'heif' => 'image/heif',
            'txt' => 'text/plain',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            default => 'application/octet-stream',
        };
    }
}
