<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade_component_id',
        'student_name',
        'student_nim',
        'score',
    ];

    // Relasi: Grade milik GradeComponent
    public function gradeComponent()
    {
        return $this->belongsTo(GradeComponent::class);
    }
}