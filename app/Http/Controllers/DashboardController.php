<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Meeting;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        $courses = Course::where('user_id', auth()->id())
                        ->withCount('meetings')
                        ->latest()
                        ->get();

        $courseIds = $courses->pluck('id');

        // Statistik utama
        $totalCourses    = $courses->count();
        $totalStudents   = Student::whereIn('course_id', $courseIds)->count();
        $totalMeetings   = Meeting::whereIn('course_id', $courseIds)->count();
        $totalAssignments = Assignment::whereHas('meeting', function($q) use ($courseIds) {
            $q->whereIn('course_id', $courseIds);
        })->count();
        $totalSubmissions = Submission::whereHas('assignment.meeting', function($q) use ($courseIds) {
            $q->whereIn('course_id', $courseIds);
        })->count();

        // Mahasiswa dengan kehadiran < 75%
        $lowAttendance = 0;
        foreach ($courses as $course) {
            $students      = $course->students;
            $totalMeetingsCourse = $course->meetings()->count();

            foreach ($students as $student) {
                if ($totalMeetingsCourse === 0) continue;
                $hadir = Attendance::where('student_id', $student->id)
                                  ->whereHas('meeting', function($q) use ($course) {
                                      $q->where('course_id', $course->id);
                                  })
                                  ->where('status', 'hadir')
                                  ->count();
                $pct = ($hadir / $totalMeetingsCourse) * 100;
                if ($pct < $course->attendance_threshold) $lowAttendance++;
            }
        }

        // Data grafik nilai rata-rata per kelas
        $gradeChartData = [];
        foreach ($courses as $course) {
            $components  = $course->gradeComponents()->with('grades')->get();
            $students    = $course->students;
            $totalWeight = $components->sum('weight');

            if ($students->isEmpty() || $components->isEmpty()) continue;

            $totalFinal = 0;
            $count      = 0;

            foreach ($students as $student) {
                $score = 0;
                foreach ($components as $component) {
                    $grade = $component->grades->where('student_nim', $student->nim)->first();
                    if ($grade && $totalWeight > 0) {
                        $score += $grade->score * ($component->weight / 100);
                    }
                }
                if ($score > 0) {
                    $totalFinal += $score;
                    $count++;
                }
            }

            if ($count > 0) {
                $gradeChartData[] = [
                    'label' => $course->name . ' (' . $course->class_name . ')',
                    'avg'   => round($totalFinal / $count, 1),
                ];
            }
        }

        // Data grafik status absensi keseluruhan
        $hadirTotal = Attendance::whereHas('meeting', function($q) use ($courseIds) {
            $q->whereIn('course_id', $courseIds);
        })->where('status', 'hadir')->count();

        $izinTotal = Attendance::whereHas('meeting', function($q) use ($courseIds) {
            $q->whereIn('course_id', $courseIds);
        })->where('status', 'izin')->count();

        $sakitTotal = Attendance::whereHas('meeting', function($q) use ($courseIds) {
            $q->whereIn('course_id', $courseIds);
        })->where('status', 'sakit')->count();

        $alphaTotal = Attendance::whereHas('meeting', function($q) use ($courseIds) {
            $q->whereIn('course_id', $courseIds);
        })->where('status', 'alpha')->count();

        $attendanceChartData = [
            'hadir' => $hadirTotal,
            'izin'  => $izinTotal,
            'sakit' => $sakitTotal,
            'alpha' => $alphaTotal,
        ];

        return view('dashboard', compact(
            'courses',
            'totalCourses',
            'totalStudents',
            'totalMeetings',
            'totalAssignments',
            'totalSubmissions',
            'lowAttendance',
            'gradeChartData',
            'attendanceChartData'
        ));
    }
}