<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        
        if (auth()->user()->hasPermissionTo($permission)) {
            return $next($request);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to access this resource'
            ], 403);
        }
        
    }
}
