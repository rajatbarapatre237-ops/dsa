<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class AssignmentFiles
{
    public static function resolvePath(string $filename): ?string
    {
        $filename = ltrim($filename, '/');
        $storagePath = storage_path('app/public/assignments/'.$filename);
        if (is_file($storagePath)) {
            return $storagePath;
        }

        $legacyPaths = [
            base_path('../../www/admin/documents/'.$filename),
            base_path('../../www/teacher/documents/'.$filename),
        ];

        foreach ($legacyPaths as $path) {
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
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            default => 'application/octet-stream',
        };
    }
}
