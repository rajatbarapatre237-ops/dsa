<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ApiTokenService
{
    private const PREFIX = 'lms_api_token:';

    /** @param array{role: string, id: string|int, email?: string, sid?: string} $payload */
    public function issue(array $payload, int $days = 30): string
    {
        $token = Str::random(64);
        Cache::put(self::PREFIX.$token, $payload, now()->addDays($days));

        return $token;
    }

    public function resolve(?string $token): ?array
    {
        if (! $token) {
            return null;
        }

        return Cache::get(self::PREFIX.$token);
    }

    public function revoke(?string $token): void
    {
        if ($token) {
            Cache::forget(self::PREFIX.$token);
        }
    }
}
