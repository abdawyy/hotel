<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Load role relationship if not already loaded
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }

        // Check if user is admin (super admin or custom admin role)
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access. Admin privileges required.');
        }

        // For custom admin roles, also check if they have dashboard.view permission
        // Super admin (role name 'admin') has all permissions by default
        if ($user->role->name !== 'admin' && !$user->hasPermission('dashboard.view')) {
            abort(403, 'Unauthorized access. Dashboard access permission required.');
        }

        return $next($request);
    }
}
