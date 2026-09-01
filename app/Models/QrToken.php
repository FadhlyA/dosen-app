<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrToken extends Model
{
    protected $fillable = [
        'meeting_id',
        'token',
        'scan_count',
        'max_scan',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    // Cek apakah token masih valid
    public function isValid()
    {
        return $this->expires_at->isFuture() && $this->scan_count < $this->max_scan;
    }
}