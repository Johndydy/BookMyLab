<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    protected $primaryKey = 'approval_id';

    protected $fillable = [
        'booking_id',
        'admin_id',
        'decision',
        'remarks',
        'decided_at',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    // An approval belongs to a booking
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    // An approval belongs to the admin who decided it
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id', 'user_id');
    }
}