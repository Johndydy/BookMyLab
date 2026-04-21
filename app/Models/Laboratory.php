<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laboratory extends Model
{
    protected $primaryKey = 'laboratory_id';

    protected $fillable = [
        'department_id',
        'name',
        'location',
        'capacity',
        'status',
    ];

    // A laboratory belongs to a department
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    // A laboratory has many equipment items
    public function equipment()
    {
        return $this->hasMany(Equipment::class, 'laboratory_id', 'laboratory_id');
    }

    // A laboratory has many bookings
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'laboratory_id', 'laboratory_id');
    }

    // A laboratory has many maintenance logs
    public function maintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::class, 'laboratory_id', 'laboratory_id');
    }
}