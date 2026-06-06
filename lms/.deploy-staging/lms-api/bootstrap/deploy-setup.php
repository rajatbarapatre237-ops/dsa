<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

return function (): array {
    $chmodRecursive = function (string $path, int $mode) use (&$chmodRecursive): void {
        chmod($path, $mode);
        if (! is_dir($path)) {
            return;
        }
        foreach (scandir($path) ?: [] as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $chmodRecursive($path.DIRECTORY_SEPARATOR.$item, $mode);
        }
    };

    $ensureWritable = function (string $path) use ($chmodRecursive): array {
        $label = 'chmod '.str_replace(base_path().'/', '', $path);
        if (! File::exists($path)) {
            return ['step' => $label, 'status' => 'skipped', 'detail' => 'Path not found'];
        }
        try {
            $chmodRecursive($path, 0775);

            return ['step' => $label, 'status' => 'ok', 'detail' => 'Permissions set to 775'];
        } catch (\Throwable $e) {
            return ['step' => $label, 'status' => 'error', 'detail' => $e->getMessage()];
        }
    };

    $runArtisan = function (string $command, array $parameters = []): array {
        try {
            Artisan::call($command, $parameters);

            return [
                'step' => 'php artisan '.$command.($parameters ? ' '.json_encode($parameters) : ''),
                'status' => 'ok',
                'detail' => trim(Artisan::output()) ?: 'Done',
            ];
        } catch (\Throwable $e) {
            return [
                'step' => 'php artisan '.$command,
                'status' => 'error',
                'detail' => $e->getMessage(),
            ];
        }
    };

    $steps = [
        $ensureWritable(base_path('storage')),
        $ensureWritable(base_path('bootstrap/cache')),
    ];

    if (blank(config('app.key'))) {
        $steps[] = $runArtisan('key:generate', ['--force' => true]);
    }

    $steps[] = $runArtisan('config:cache');
    $steps[] = $runArtisan('route:cache');
    $steps[] = $runArtisan('storage:link');

    return [
        'status' => 'success',
        'message' => 'Deploy setup completed',
        'steps' => $steps,
    ];
};
