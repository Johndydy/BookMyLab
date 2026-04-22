<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $primaryKey = 'equipment_id';

    protected $fillable = [
        'laboratory_id',
        'name',
        'quantity',
        'condition',
    ];

    // Equipment belongs to a laboratory
    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class, 'laboratory_id', 'laboratory_id');
    }

    // Equipment can appear in many booking_equipment records
    public function bookingEquipment()
    {
        return $this->hasMany(BookingEquipment::class, 'equipment_id', 'equipment_id');
    }

    // Equipment can appear in many equipment logs
    public function equipmentLogs()
    {
        return $this->hasMany(EquipmentLog::class, 'equipment_id', 'equipment_id');
    }

    // Query scopes for equipment condition
    public function scopeDamaged($query)
    {
        return $query->where('condition', 'damaged');
    }

    public function scopeUnderRepair($query)
    {
        return $query->where('condition', 'under repair');
    }
}