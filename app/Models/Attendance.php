<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'student_id',
        'status',
        'note',
    ];

    // Relasi: Attendance milik Meeting
    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    // Relasi: Attendance milik Student
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}