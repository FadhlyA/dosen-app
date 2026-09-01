<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeLetterConfig extends Model
{
    protected $fillable = [
        'course_id',
        'letter',
        'min_score',
        'max_score',
        'order',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}