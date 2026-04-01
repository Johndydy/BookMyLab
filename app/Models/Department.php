<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $primaryKey = 'department_id';
    public $incrementing = true;

    protected $fillable = [
        'name',
        'building',
    ];

    public function laboratories()
    {
        return $this->hasMany(Laboratory::class, 'department_id', 'department_id');
    }
}
