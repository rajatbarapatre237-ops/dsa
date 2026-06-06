<?php

namespace App\Http\Middleware;

use App\Services\ApiTokenService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiToken
{
    public function __construct(private ApiTokenService $tokens) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        $payload = $this->tokens->resolve($token);

        if (! $payload) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $request->attributes->set('api_user', $payload);

        return $next($request);
    }
}
