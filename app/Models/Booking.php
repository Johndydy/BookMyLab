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

    // A booking can request many equipment items
    public function bookingEquipment()
    {
        return $this->hasMany(BookingEquipment::class, 'booking_id', 'booking_id');
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
}