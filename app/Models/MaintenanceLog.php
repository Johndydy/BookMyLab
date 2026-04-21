<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model
{
    protected $primaryKey = 'log_id';

    protected $fillable = [
        'laboratory_id',
        'admin_id',
        'reason',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
    ];

    // A maintenance log belongs to a laboratory
    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class, 'laboratory_id', 'laboratory_id');
    }

    // A maintenance log belongs to the admin who created it
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id', 'user_id');
    }
}