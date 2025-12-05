<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;
use App\Models\Group;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentGroup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // This middleware is now optional since we're not using group-based routing
        // It can be used in the future if needed for group-specific features
        return $next($request);
    }
}
