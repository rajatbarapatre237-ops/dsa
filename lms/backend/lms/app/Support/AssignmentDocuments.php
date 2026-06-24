<?php

namespace App\Support;

class AssignmentDocuments
{
    public static function isBundle(?string $document): bool
    {
        $document = trim((string) $document);

        return str_starts_with($document, '{"v":1,"files":');
    }

    /**
     * @return array<int, array{path: string, name: string}>
     */
    public static function decode(?string $document): array
    {
        $document = trim((string) $document);
        if ($document === '') {
            return [];
        }

        if (self::isBundle($document)) {
            $data = json_decode($document, true);
            if (! is_array($data['files'] ?? null)) {
                return [];
            }

            return array_values(array_filter(array_map(function ($entry) {
                if (! is_array($entry)) {
                    return null;
                }
                $path = ltrim(str_replace('\\', '/', (string) ($entry['p'] ?? $entry['path'] ?? '')), '/');
                if ($path === '') {
                    return null;
                }

                return [
                    'path' => $path,
                    'name' => trim((string) ($entry['n'] ?? $entry['name'] ?? '')) ?: basename($path),
                ];
            }, $data['files'])));
        }

        return [[
            'path' => ltrim(str_replace('\\', '/', $document), '/'),
            'name' => basename($document),
        ]];
    }

    /**
     * @param  array<int, array{path: string, name: string}>  $files
     */
    public static function encode(array $files): string
    {
        $normalized = array_values(array_map(function (array $file) {
            return [
                'p' => ltrim(str_replace('\\', '/', (string) ($file['path'] ?? '')), '/'),
                'n' => trim((string) ($file['name'] ?? '')) ?: basename((string) ($file['path'] ?? '')),
            ];
        }, $files));

        if (count($normalized) === 1) {
            return $normalized[0]['p'];
        }

        return json_encode(['v' => 1, 'files' => $normalized], JSON_UNESCAPED_SLASHES);
    }

    public static function fileCount(?string $document): int
    {
        return count(self::decode($document));
    }

    public static function resolvePath(?string $document, int $index = 0): ?string
    {
        $files = self::decode($document);
        $entry = $files[$index] ?? null;
        if (! $entry) {
            return null;
        }

        return AssignmentFiles::resolvePath($entry['path']);
    }

    public static function resolveName(?string $document, int $index = 0): string
    {
        $files = self::decode($document);
        $entry = $files[$index] ?? null;
        if (! $entry) {
            return 'file';
        }

        return $entry['name'];
    }

    public static function append(?string $document, array $entry): string
    {
        $path = ltrim(str_replace('\\', '/', (string) ($entry['path'] ?? '')), '/');
        if ($path === '') {
            return trim((string) $document);
        }

        $files = self::decode($document);
        $files[] = [
            'path' => $path,
            'name' => trim((string) ($entry['name'] ?? '')) ?: basename($path),
        ];

        return self::encode($files);
    }

    public static function deleteAll(?string $document): void
    {
        foreach (self::decode($document) as $file) {
            AssignmentFiles::deleteStoredFile($file['path']);
        }
    }
}
