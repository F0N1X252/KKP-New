<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class AdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user has admin permissions
        if ($this->hasAdminAccess($user)) {
            return $next($request);
        }

        // If user doesn't have admin access, redirect to user dashboard
        return redirect()->route('user.dashboard')
            ->with('error', 'You do not have permission to access the admin panel.');
    }

    /**
     * Check if user has admin access
     */
    protected function hasAdminAccess($user)
    {
        // Load user roles if not loaded
        $user->load('roles.permissions');

        // Check for dashboard access
        if (Gate::allows('dashboard_access')) {
            return true;
        }

        // Check for any admin permissions
        $adminPermissions = [
            'user_management_access',
            'ticket_access',
            'comment_access', 
            'category_access',
            'priority_access',
            'status_access',
            'permission_access',
            'role_access',
            'audit_log_access'
        ];

        foreach ($adminPermissions as $permission) {
            if (Gate::allows($permission)) {
                return true;
            }
        }

        return false;
    }
}