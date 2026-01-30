<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the role that owns the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get all bookings for the user.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Check if user is admin.
     * Returns true for super admin (role name 'admin') and custom admin roles (role name starts with 'admin_').
     */
    public function isAdmin(): bool
    {
        if (!$this->role) {
            return false;
        }
        
        return $this->role->name === 'admin' || str_starts_with($this->role->name, 'admin_');
    }

    /**
     * Check if user is customer.
     */
    public function isCustomer(): bool
    {
        return $this->role && $this->role->name === 'customer';
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        if (!$this->role) {
            return false;
        }

        // Super admin (role name 'admin') has all permissions
        if ($this->role->name === 'admin') {
            return true;
        }

        // Load permissions if not already loaded for custom admin roles
        if (!$this->role->relationLoaded('permissions')) {
            $this->role->load('permissions');
        }

        // Custom admin roles need explicit permission checks
        return $this->role->hasPermission($permissionSlug);
    }
}
