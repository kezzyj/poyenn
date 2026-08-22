<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth('admin')->check() || !auth('admin')->user()->isSuperAdmin()) {
            abort(403, 'Only super admins can access this page.');
        }

        return $next($request);
    }
}