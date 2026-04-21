<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'first_name',
        'last_name',
        'school_email',
        'school_id_number',
        'password',
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

    // Helper: get full name
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}