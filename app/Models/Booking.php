<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $primaryKey = 'booking_id';

    protected $fillable = [
        'user_id',
        'laboratory_id',
        'purpose',
        'number_of_persons',
        'start_time',
        'end_time',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];

    // A booking belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // A booking belongs to a laboratory
    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class, 'laboratory_id', 'laboratory_id');
    }

    // A booking has one approval
    public function approval()
    {
        return $this->hasOne(Approval::class, 'booking_id', 'booking_id');
    }

    // A booking can request many equipment items (intermediate model)
    public function bookingEquipment()
    {
        return $this->hasMany(BookingEquipment::class, 'booking_id', 'booking_id');
    }

    // A booking has many equipment (direct many-to-many relationship)
    public function equipment()
    {
        return $this->belongsToMany(Equipment::class, 'booking_equipment', 'booking_id', 'equipment_id')
                    ->withPivot('quantity_requested')
                    ->withTimestamps();
    }

    // A booking can trigger many notifications
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'booking_id', 'booking_id');
    }

    // A booking can have many equipment logs
    public function equipmentLogs()
    {
        return $this->hasMany(EquipmentLog::class, 'booking_id', 'booking_id');
    }

    // Query scopes for booking status
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
}