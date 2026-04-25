<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'school_email',
        'school_id_number',
        'password',
        'google_id',
        'google_token',
        'avatar',
        'phone_number',
        'student_id',
        'department_name',
        'course',
        'year_level',
        'bio',
        'profile_completed_at',
    ];

    protected $hidden = [
        'password',
    ];

    // A user can make many bookings
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id', 'user_id');
    }

    // A user can have many notifications
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id', 'user_id');
    }

    // A user can have many maintenance logs (as admin)
    public function maintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::class, 'admin_id', 'user_id');
    }

    // A user can decide on many approvals (as admin)
    public function approvals()
    {
        return $this->hasMany(Approval::class, 'admin_id', 'user_id');
    }

    // A user belongs to many roles via user_roles pivot table
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    // Helper: check if user has a specific role by name
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    // Helper: assign a role to this user
    public function assignRole(Role $role): void
    {
        if (!$this->hasRole($role->name)) {
            $this->roles()->attach($role->role_id);
        }
    }

    // Helper: remove a role from this user
    public function removeRole(Role $role): void
    {
        if ($this->hasRole($role->name)) {
            $this->roles()->detach($role->role_id);
        }
    }

    // Helper: check if user is admin
    public function isAdmin(): bool
    {
        return $this->hasRole('administrator');
    }

    // Helper: check if user has a specific permission
    public function hasPermission(string $permissionName): bool
    {
        return $this->roles
            ->flatMap->permissions
            ->pluck('name')
            ->contains($permissionName);
    }

    // Helper: get all permissions from user's roles
    public function getAllPermissions()
    {
        return $this->roles
            ->flatMap->permissions
            ->unique('permission_id')
            ->values();
    }

    // Helper: get full name
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}