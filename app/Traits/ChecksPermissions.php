<?php

namespace App\Traits;

trait ChecksPermissions
{
    /**
     * Check if the authenticated user has a specific permission.
     * Super admins (role name 'admin') have all permissions.
     *
     * @param string $permissionSlug
     * @return bool
     */
    protected function hasPermission(string $permissionSlug): bool
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }

        // Load role relationship if not already loaded
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }

        // Ensure role exists
        if (!$user->role) {
            return false;
        }

        return $user->hasPermission($permissionSlug);
    }

    /**
     * Authorize a permission or abort with 403.
     *
     * @param string $permissionSlug
     * @param string|null $message
     * @return void
     */
    protected function authorizePermission(string $permissionSlug, ?string $message = null): void
    {
        $user = auth()->user();
        
        if (!$this->hasPermission($permissionSlug)) {
            // Provide more helpful error message
            $roleName = $user && $user->role ? $user->role->name : 'No role';
            $errorMessage = $message ?? "You do not have permission to perform this action. Required permission: {$permissionSlug}. Your role: {$roleName}";
            abort(403, $errorMessage);
        }
    }
}
