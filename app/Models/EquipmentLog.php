<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentLog extends Model
{
    use HasFactory;

    protected $primaryKey = 'equipmentlog_id';
    public $incrementing = true;
    protected $table = 'equipment_logs';

    protected $fillable = [
        'booking_id',
        'equipment_id',
        'quantity_borrowed',
        'borrowed_at',
        'returned_at',
        'condition_after',
    ];

    protected $dates = [
        'borrowed_at',
        'returned_at',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'equipment_id');
    }

    public function scopeNotReturned($query)
    {
        return $query->whereNull('returned_at');
    }

    public function scopeReturned($query)
    {
        return $query->whereNotNull('returned_at');
    }
}
