<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rps extends Model
{
    use HasFactory;

    protected $table = 'rps';

    protected $fillable = [
        'course_id',
        'title',
        'file_path',
        'original_name',
    ];

    // Relasi: RPS milik Course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}