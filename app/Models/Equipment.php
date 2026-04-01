<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $primaryKey = 'equipment_id';
    public $incrementing = true;

    protected $fillable = [
        'laboratory_id',
        'name',
        'quantity',
        'condition',
    ];

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class, 'laboratory_id', 'laboratory_id');
    }

    public function bookings()
    {
        return $this->hasMany(BookingEquipment::class, 'equipment_id', 'equipment_id');
    }

    public function logs()
    {
        return $this->hasMany(EquipmentLog::class, 'equipment_id', 'equipment_id');
    }

    public function scopeGood($query)
    {
        return $query->where('condition', 'good');
    }

    public function scopeDamaged($query)
    {
        return $query->where('condition', 'damaged');
    }

    public function scopeUnderRepair($query)
    {
        return $query->where('condition', 'under repair');
    }
}
