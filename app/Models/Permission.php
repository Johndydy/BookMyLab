<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $primaryKey = 'permission_id';

    protected $fillable = [
        'name',
        'description',
    ];

    // A permission belongs to many roles via role_permissions pivot table
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id');
    }
}