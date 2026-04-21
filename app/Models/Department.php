<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $primaryKey = 'department_id';

    protected $fillable = [
        'name',
        'building',
    ];

    // A department has many laboratories
    public function laboratories()
    {
        return $this->hasMany(Laboratory::class, 'department_id', 'department_id');
    }
}