<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentLog extends Model
{
    protected $primaryKey = 'equipmentlog_id';

    protected $fillable = [
        'booking_id',
        'equipment_id',
        'quantity_borrowed',
        'borrowed_at',
        'returned_at',
        'condition_after',
    ];

    protected $casts = [
        'borrowed_at'  => 'datetime',
        'returned_at'  => 'datetime',
    ];

    // An equipment log belongs to a booking
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    // An equipment log belongs to an equipment item
    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'equipment_id');
    }
}