<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'name',
        'weight',
        'is_attendance',
        'attendance_formula',
        'is_assignment_based',
        'is_fixed',
    ];

    protected $casts = [
        'is_attendance' => 'boolean',
    ];

    // Relasi: GradeComponent milik Course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Relasi: GradeComponent punya banyak Grade
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    // Hitung nilai absensi otomatis untuk mahasiswa tertentu
    public function calculateAttendanceScore(Student $student, Course $course)
    {
        $totalMeetings = $course->meetings()->count();
        if ($totalMeetings === 0) return 0;

        $query = \App\Models\Attendance::where('student_id', $student->id)
                    ->whereHas('meeting', function($q) use ($course) {
                        $q->where('course_id', $course->id);
                    });

        // Gunakan formula dari setting kelas
        $formula = $course->attendance_formula ?? 'hadir_only';

        if ($formula === 'include_izin_sakit') {
            $present = (clone $query)->whereIn('status', ['hadir', 'izin', 'sakit'])->count();
        } else {
            $present = (clone $query)->where('status', 'hadir')->count();
        }

        return round(($present / $totalMeetings) * 100, 2);
    }
    // Hitung nilai tugas otomatis = rata-rata score submission mahasiswa
    public function calculateAssignmentScore(Student $student, Course $course)
    {
        // Total tugas yang diberikan dosen di kelas ini
        $totalAssignments = \App\Models\Assignment::whereHas('meeting', function($q) use ($course) {
            $q->where('course_id', $course->id);
        })->count();

        if ($totalAssignments === 0) return null;

        // Jumlah nilai dari tugas yang dikumpulkan saja
        $totalScore = \App\Models\Submission::where('student_nim', $student->nim)
                    ->whereHas('assignment.meeting', function($q) use ($course) {
                        $q->where('course_id', $course->id);
                    })
                    ->whereNotNull('score')
                    ->sum('score');

        // Dibagi total tugas dosen (tugas tidak kumpul = 0)
        return round($totalScore / $totalAssignments, 2);
    }
}