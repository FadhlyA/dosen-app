<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'nim',
        'name',
        'email',
    ];

    // Relasi: Student milik Course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    // Relasi: Student punya banyak Attendance
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}