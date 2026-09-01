<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'title',
        'description',
        'due_date',
    ];

    // Relasi: Assignment milik Meeting
    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    // Relasi: Assignment punya banyak Submission
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}