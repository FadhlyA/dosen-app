<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'title',
        'type',
        'file_path',
        'link_url',
    ];

    // Relasi: Material dimiliki oleh Meeting
    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }
}