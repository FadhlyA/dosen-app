<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'student_name',
        'student_nim',
        'file_path',
        'note',
        'score',
    ];

    // Relasi: Submission milik Assignment
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }
}