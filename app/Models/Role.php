<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get all users with this role.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all permissions for this role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    /**
     * Check if role has a specific permission.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        // Super admin role has all permissions
        if ($this->name === 'admin') {
            return true;
        }
        
        // If permissions are already loaded, check the collection (faster)
        if ($this->relationLoaded('permissions')) {
            return $this->permissions->where('slug', $permissionSlug)->isNotEmpty();
        }
        
        // Otherwise, query the database
        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }
}
