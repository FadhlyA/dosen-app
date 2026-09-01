<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Meeting;
use App\Models\Student;

class StudentController extends Controller
{
    // Halaman input key untuk mahasiswa
    public function index()
    {
        return view('student.index');
    }

    // Proses verifikasi key
    public function verify(Request $request)
    {
        $request->validate([
            'access_key' => 'required|string',
        ]);

        $course = Course::where('access_key', strtoupper($request->access_key))->first();

        if (!$course) {
            return back()->withErrors([
                'access_key' => 'Key tidak valid. Silakan cek kembali.'
            ]);
        }

        session(['student_course_id' => $course->id]);

        return redirect()->route('student.verify-nim', $course->id);
    }

    // Halaman input NIM setelah key benar
    public function verifyNimForm(Course $course)
    {
        if (session('student_course_id') !== $course->id) {
            return redirect()->route('student.index');
        }

        return view('student.verify-nim', compact('course'));
    }

    // Proses verifikasi NIM
    public function verifyNim(Request $request, Course $course)
    {
        if (session('student_course_id') !== $course->id) {
            return redirect()->route('student.index');
        }

        $request->validate([
            'nim' => 'required|string',
        ]);

        $student = $course->students()->where('nim', trim($request->nim))->first();

        if (!$student) {
            return back()->withErrors([
                'nim' => 'NIM tidak terdaftar di kelas ini. Hubungi dosen Anda.'
            ]);
        }

        session([
            'student_course_id' => $course->id,
            'student_nim'       => $student->nim,
            'student_name'      => $student->name,
        ]);

        return redirect()->route('student.course', $course->id);
    }

    // Halaman kelas untuk mahasiswa
    public function course(Course $course)
    {
        if (session('student_course_id') !== $course->id) {
            return redirect()->route('student.index')
                           ->withErrors(['access_key' => 'Silakan masukkan key terlebih dahulu.']);
        }

        if (!session('student_nim')) {
            return redirect()->route('student.verify-nim', $course->id);
        }

        $meetings = $course->meetings()->orderBy('meeting_number')->get();
        return view('student.course', compact('course', 'meetings'));
    }

    // Halaman detail pertemuan untuk mahasiswa
    public function meeting(Course $course, Meeting $meeting)
    {
        if (session('student_course_id') !== $course->id) {
            return redirect()->route('student.index');
        }

        if (!session('student_nim')) {
            return redirect()->route('student.verify-nim', $course->id);
        }

        $materials = $meeting->materials()->get();
        return view('student.meeting', compact('course', 'meeting', 'materials'));
    }
    // Halaman rekap absensi mahasiswa di portal
    public function attendance(Course $course)
    {
        if (session('student_course_id') !== $course->id) {
            return redirect()->route('student.index');
        }

        if (!session('student_nim')) {
            return redirect()->route('student.verify-nim', $course->id);
        }

        $nim     = session('student_nim');
        $student = $course->students()->where('nim', $nim)->firstOrFail();
        $meetings = $course->meetings()->orderBy('meeting_number')->get();
        $totalMeetings = $meetings->count();

        $hadir = $izin = $sakit = $alpha = 0;
        $detail = [];

        foreach ($meetings as $meeting) {
            $attendance = \App\Models\Attendance::where('meeting_id', $meeting->id)
                                               ->where('student_id', $student->id)
                                               ->first();
            $status = $attendance ? $attendance->status : null;
            $detail[$meeting->id] = [
                'status' => $status,
                'note'   => $attendance ? $attendance->note : null,
            ];

            if ($status === 'hadir') $hadir++;
            elseif ($status === 'izin') $izin++;
            elseif ($status === 'sakit') $sakit++;
            elseif ($status === 'alpha') $alpha++;
        }

        $percentage = $totalMeetings > 0
                     ? round(($hadir / $totalMeetings) * 100, 1)
                        : 0;
        $threshold = $course->attendance_threshold ?? 75;

        return view('student.attendance', compact(
            'course', 'student', 'meetings',
            'detail', 'hadir', 'izin', 'sakit', 'alpha',
            'percentage', 'totalMeetings', 'threshold'
        ));
    }
}