<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
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

    $steps = [];

    if (! File::exists(base_path('.env')) && File::exists(base_path('.env.example'))) {
        try {
            File::copy(base_path('.env.example'), base_path('.env'));
            $steps[] = [
                'step' => 'copy .env.example → .env',
                'status' => 'ok',
                'detail' => 'Created .env — update DB credentials before relying on migrations.',
            ];
            Artisan::call('config:clear');
        } catch (\Throwable $e) {
            $steps[] = [
                'step' => 'copy .env.example → .env',
                'status' => 'error',
                'detail' => $e->getMessage(),
            ];
        }
    }

    $steps[] = $ensureWritable(base_path('storage'));
    $steps[] = $ensureWritable(base_path('bootstrap/cache'));

    if (blank(config('app.key'))) {
        $steps[] = $runArtisan('key:generate', ['--force' => true]);
    } else {
        $steps[] = [
            'step' => 'php artisan key:generate',
            'status' => 'skipped',
            'detail' => 'APP_KEY already set',
        ];
    }

    try {
        DB::connection()->getPdo();
        $steps[] = $runArtisan('migrate', ['--force' => true]);
        $steps[] = $runArtisan('db:seed', [
            '--class' => 'Database\\Seeders\\CmsPageSeeder',
            '--force' => true,
        ]);
    } catch (\Throwable $e) {
        $steps[] = [
            'step' => 'database migrate/seed',
            'status' => 'skipped',
            'detail' => 'Database not ready: '.$e->getMessage(),
        ];
    }

    $steps[] = $runArtisan('route:clear');
    $steps[] = $runArtisan('config:cache');
    $steps[] = $runArtisan('route:cache');
    $steps[] = $runArtisan('storage:link');

    $errors = collect($steps)->where('status', 'error')->count();
    $skipped = collect($steps)->where('status', 'skipped')->count();

    $status = 'success';
    if ($errors > 0) {
        $status = 'error';
    } elseif ($skipped > 0) {
        $status = 'partial';
    }

    return [
        'status' => $status,
        'message' => match ($status) {
            'success' => 'Deploy setup completed',
            'partial' => 'Setup completed with skipped steps — check database settings in .env',
            default => 'Setup finished with errors',
        },
        'steps' => $steps,
    ];
};
