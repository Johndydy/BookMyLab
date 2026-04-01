<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $primaryKey = 'booking_id';
    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'laboratory_id',
        'purpose',
        'start_time',
        'end_time',
        'status',
    ];

    protected $dates = [
        'start_time',
        'end_time',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class, 'laboratory_id', 'laboratory_id');
    }

    public function equipment()
    {
        return $this->hasMany(BookingEquipment::class, 'booking_id', 'booking_id');
    }

    public function approval()
    {
        return $this->hasOne(Approval::class, 'booking_id', 'booking_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'booking_id', 'booking_id');
    }

    public function equipmentLogs()
    {
        return $this->hasMany(EquipmentLog::class, 'booking_id', 'booking_id');
    }

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
