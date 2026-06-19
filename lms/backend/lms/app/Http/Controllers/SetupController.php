<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class SetupController extends Controller
{
    public function __invoke(Request $request): JsonResponse|Response
    {
        if ($this->isLocked() && ! $this->canForce($request)) {
            return $this->respond($request, [
                'status' => 'already_setup',
                'message' => 'Setup already completed. Delete storage/app/.setup-complete to run again, or use ?force=1&token=YOUR_SETUP_TOKEN.',
            ], 403);
        }

        try {
            $result = (require base_path('bootstrap/deploy-setup.php'))();
        } catch (\Throwable $e) {
            return $this->respond($request, [
                'status' => 'error',
                'message' => $e->getMessage(),
                'steps' => [],
            ], 500);
        }

        if (($result['status'] ?? '') === 'success') {
            File::ensureDirectoryExists(storage_path('app'));
            File::put(storage_path('app/.setup-complete'), now()->toIso8601String());
        }

        return $this->respond($request, $result, $this->httpStatusFor($result));
    }

    private function isLocked(): bool
    {
        return File::exists(storage_path('app/.setup-complete'));
    }

    private function canForce(Request $request): bool
    {
        if (! $request->boolean('force')) {
            return false;
        }

        $token = (string) env('SETUP_TOKEN', '');

        return $token !== '' && hash_equals($token, (string) $request->query('token', ''));
    }

    private function httpStatusFor(array $result): int
    {
        return match ($result['status'] ?? 'error') {
            'success' => 200,
            'partial' => 207,
            default => 500,
        };
    }

    private function respond(Request $request, array $payload, int $status): JsonResponse|Response
    {
        if ($request->expectsJson() || $request->query('format') === 'json') {
            return response()->json($payload, $status, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        $stepsHtml = collect($payload['steps'] ?? [])
            ->map(function (array $step): string {
                $status = e($step['status'] ?? 'unknown');
                $name = e($step['step'] ?? 'step');
                $detail = e($step['detail'] ?? '');

                return "<tr><td><code>{$name}</code></td><td><strong>{$status}</strong></td><td>{$detail}</td></tr>";
            })
            ->implode('');

        $title = e($payload['message'] ?? 'Setup');
        $statusLabel = e($payload['status'] ?? 'unknown');
        $body = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>LMS Setup</title>
  <style>
    body { font-family: system-ui, sans-serif; margin: 2rem; background: #f8fafc; color: #0f172a; }
    main { max-width: 960px; margin: 0 auto; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; }
    h1 { margin-top: 0; }
    .status { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 999px; background: #e2e8f0; }
    table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    th, td { border-bottom: 1px solid #e2e8f0; padding: 0.75rem; text-align: left; vertical-align: top; }
    code { font-size: 0.9rem; }
    .hint { margin-top: 1rem; color: #475569; }
  </style>
</head>
<body>
  <main>
    <h1>LMS Setup</h1>
    <p><span class="status">{$statusLabel}</span></p>
    <p>{$title}</p>
    <table>
      <thead><tr><th>Step</th><th>Status</th><th>Detail</th></tr></thead>
      <tbody>{$stepsHtml}</tbody>
    </table>
    <p class="hint">JSON output: append <code>?format=json</code>. After a successful setup, edit <code>.env</code> (database, APP_URL) and run setup again only if needed.</p>
  </main>
</body>
</html>
HTML;

        return response($body, $status)->header('Content-Type', 'text/html; charset=utf-8');
    }
}
