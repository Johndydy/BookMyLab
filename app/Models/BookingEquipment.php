<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingEquipment extends Model
{
    protected $primaryKey = 'bookingequipment_id';

    protected $fillable = [
        'booking_id',
        'equipment_id',
        'quantity_requested',
    ];

    // Belongs to a booking
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    // Belongs to an equipment item
    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'equipment_id');
    }
}