<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'meeting_number',
        'title',
        'description',
        'meeting_date',
        'status',
        'note_before',
        'note_after',
    ];

    // Relasi: Meeting dimiliki oleh Course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Relasi: Meeting punya banyak Material
    public function materials()
    {
        return $this->hasMany(Material::class);
    }
    // Relasi: Meeting punya banyak Assignment
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
    // Relasi: Meeting punya banyak Attendance
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}