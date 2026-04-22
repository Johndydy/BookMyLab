<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $primaryKey = 'role_id';

    protected $fillable = [
        'name',
        'description',
    ];

    // A role belongs to many users via user_roles pivot table
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id');
    }

    // A role belongs to many permissions via role_permissions pivot table
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id');
    }

    // Helper: give a permission to this role
    public function givePermission(Permission $permission): void
    {
        if (!$this->permissions()->where('permission_id', $permission->permission_id)->exists()) {
            $this->permissions()->attach($permission->permission_id);
        }
    }

    // Helper: revoke a permission from this role
    public function revokePermission(Permission $permission): void
    {
        $this->permissions()->detach($permission->permission_id);
    }
}