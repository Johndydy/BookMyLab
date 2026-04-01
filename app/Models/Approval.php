<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    use HasFactory;

    protected $primaryKey = 'approval_id';
    public $incrementing = true;

    protected $fillable = [
        'booking_id',
        'admin_id',
        'decision',
        'remarks',
        'decided_at',
    ];

    protected $dates = [
        'decided_at',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id', 'user_id');
    }

    public function scopeApproved($query)
    {
        return $query->where('decision', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('decision', 'rejected');
    }
}
