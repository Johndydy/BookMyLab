<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laboratory extends Model
{
    use HasFactory;

    protected $primaryKey = 'laboratory_id';
    public $incrementing = true;

    protected $fillable = [
        'department_id',
        'name',
        'location',
        'capacity',
        'status',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    public function equipment()
    {
        return $this->hasMany(Equipment::class, 'laboratory_id', 'laboratory_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'laboratory_id', 'laboratory_id');
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::class, 'laboratory_id', 'laboratory_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }
}
