<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfDashboardAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->get('dashboard_authenticated')) {
            return redirect()->route('dashboard.home');
        }

        return $next($request);
    }
}
