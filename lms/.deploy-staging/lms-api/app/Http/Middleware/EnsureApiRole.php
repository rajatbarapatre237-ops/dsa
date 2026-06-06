<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->attributes->get('api_user');

        if (($user['role'] ?? '') !== $role) {
            return response()->json(['status' => 'error', 'message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
