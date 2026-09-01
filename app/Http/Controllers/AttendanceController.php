<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Meeting;
use App\Models\Student;
use App\Models\Attendance;
use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Halaman input absensi per pertemuan
    public function index(Course $course, Meeting $meeting)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $students    = $course->students()->orderBy('nim')->get();
        $attendances = $meeting->attendances()->with('student')->get()
                               ->keyBy('student_id');

        return view('attendances.index', compact('course', 'meeting', 'students', 'attendances'));
    }

    // Simpan absensi semua mahasiswa sekaligus
    public function store(Request $request, Course $course, Meeting $meeting)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $students = $course->students()->get();

        foreach ($students as $student) {
            $status = $request->input('status_' . $student->id, 'alpha');
            $note   = $request->input('note_' . $student->id, null);

            Attendance::updateOrCreate(
                [
                    'meeting_id' => $meeting->id,
                    'student_id' => $student->id,
                ],
                [
                    'status' => $status,
                    'note'   => $note,
                ]
            );
        }

        return redirect()->route('attendances.index', [$course, $meeting])
                        ->with('success', 'Absensi berhasil disimpan!');
    }

    // Set semua mahasiswa hadir sekaligus
    public function allPresent(Course $course, Meeting $meeting)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $students = $course->students()->get();

        foreach ($students as $student) {
            Attendance::updateOrCreate(
                [
                    'meeting_id' => $meeting->id,
                    'student_id' => $student->id,
                ],
                [
                    'status' => 'hadir',
                    'note'   => null,
                ]
            );
        }

        return redirect()->route('attendances.index', [$course, $meeting])
                        ->with('success', 'Semua mahasiswa ditandai Hadir!');
    }

    // Rekap absensi per kelas
    public function recap(Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $students  = $course->students()->orderBy('nim')->get();
        $meetings  = $course->meetings()->orderBy('meeting_number')->get();
        $totalMeetings = $meetings->count();

        $recap = [];

        foreach ($students as $student) {
            $hadir = 0;
            $izin  = 0;
            $sakit = 0;
            $alpha = 0;
            $detail = [];

            foreach ($meetings as $meeting) {
                $attendance = Attendance::where('meeting_id', $meeting->id)
                                       ->where('student_id', $student->id)
                                       ->first();

                $status = $attendance ? $attendance->status : null;
                $detail[$meeting->id] = $status;

                if ($status === 'hadir') $hadir++;
                elseif ($status === 'izin') $izin++;
                elseif ($status === 'sakit') $sakit++;
                elseif ($status === 'alpha') $alpha++;
            }

            // Hitung persentase berdasarkan formula kelas
            $formula = $course->attendance_formula ?? 'hadir_only';
            $effectiveHadir = $formula === 'include_izin_sakit'
                             ? $hadir + $izin + $sakit
                             : $hadir;

            $percentage = $totalMeetings > 0
                         ? round(($effectiveHadir / $totalMeetings) * 100, 1)
                         : 0;

            $recap[$student->id] = [
                'nim'        => $student->nim,
                'name'       => $student->name,
                'hadir'      => $hadir,
                'izin'       => $izin,
                'sakit'      => $sakit,
                'alpha'      => $alpha,
                'percentage' => $percentage,
                'warning'    => $percentage < $course->attendance_threshold,
                'detail'     => $detail,
            ];
        }

        return view('attendances.recap', compact(
            'course', 'meetings', 'students', 'recap', 'totalMeetings'
        ));
    }

    // Export rekap absensi ke CSV
    public function export(Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $students      = $course->students()->orderBy('nim')->get();
        $meetings      = $course->meetings()->orderBy('meeting_number')->get();
        $totalMeetings = $meetings->count();

        $rows   = [];
        $header = ['NIM', 'Nama'];

        foreach ($meetings as $meeting) {
            $header[] = 'P' . $meeting->meeting_number;
        }

        $header[] = 'Hadir';
        $header[] = 'Izin';
        $header[] = 'Sakit';
        $header[] = 'Alpha';
        $header[] = '% Kehadiran';
        $header[] = 'Keterangan';
        $rows[]   = $header;

        foreach ($students as $student) {
            $row   = [$student->nim, $student->name];
            $hadir = $izin = $sakit = $alpha = 0;

            foreach ($meetings as $meeting) {
                $attendance = Attendance::where('meeting_id', $meeting->id)
                                       ->where('student_id', $student->id)
                                       ->first();
                $status = $attendance ? strtoupper(substr($attendance->status, 0, 1)) : '-';
                $row[]  = $status;

                if ($attendance) {
                    if ($attendance->status === 'hadir') $hadir++;
                    elseif ($attendance->status === 'izin') $izin++;
                    elseif ($attendance->status === 'sakit') $sakit++;
                    elseif ($attendance->status === 'alpha') $alpha++;
                }
            }

            $formula = $course->attendance_formula ?? 'hadir_only';
            $effectiveHadir = $formula === 'include_izin_sakit'
                             ? $hadir + $izin + $sakit
                             : $hadir;

            $percentage = $totalMeetings > 0
                         ? round(($effectiveHadir / $totalMeetings) * 100, 1)
                         : 0;

            $row[] = $hadir;
            $row[] = $izin;
            $row[] = $sakit;
            $row[] = $alpha;
            $row[] = $percentage . '%';
            $row[] = $percentage < $course->attendance_threshold ? 'TIDAK MEMENUHI' : 'MEMENUHI';
            $rows[] = $row;
        }

        $filename = 'Absensi_' . str_replace(' ', '_', $course->name) . '_' .
                    str_replace(' ', '_', $course->class_name) . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($rows) {
            $file = fopen('php://output', 'w');
            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    // Cetak daftar hadir per pertemuan
    public function print(Course $course, Meeting $meeting)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $students    = $course->students()->orderBy('nim')->get();
        $attendances = $meeting->attendances()->with('student')->get()
                               ->keyBy('student_id');

        return view('attendances.print', compact('course', 'meeting', 'students', 'attendances'));
    }

    // Cetak rekap absensi per kelas
    public function printRecap(Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $students      = $course->students()->orderBy('nim')->get();
        $meetings      = $course->meetings()->orderBy('meeting_number')->get();
        $totalMeetings = $meetings->count();

        $recap = [];
        foreach ($students as $student) {
            $hadir = $izin = $sakit = $alpha = 0;
            $detail = [];

            foreach ($meetings as $meeting) {
                $attendance = Attendance::where('meeting_id', $meeting->id)
                                       ->where('student_id', $student->id)
                                       ->first();
                $status = $attendance ? $attendance->status : null;
                $detail[$meeting->id] = $status;

                if ($status === 'hadir') $hadir++;
                elseif ($status === 'izin') $izin++;
                elseif ($status === 'sakit') $sakit++;
                elseif ($status === 'alpha') $alpha++;
            }

            // Hitung persentase berdasarkan formula kelas
            $formula = $course->attendance_formula ?? 'hadir_only';
            $effectiveHadir = $formula === 'include_izin_sakit'
                             ? $hadir + $izin + $sakit
                             : $hadir;

            $percentage = $totalMeetings > 0
                         ? round(($effectiveHadir / $totalMeetings) * 100, 1)
                         : 0;

            $recap[$student->id] = [
                'nim'        => $student->nim,
                'name'       => $student->name,
                'hadir'      => $hadir,
                'izin'       => $izin,
                'sakit'      => $sakit,
                'alpha'      => $alpha,
                'percentage' => $percentage,
                'warning'    => $percentage < $course->attendance_threshold,
                'detail'     => $detail,
            ];
        }

        return view('attendances.print-recap', compact(
            'course', 'meetings', 'students', 'recap', 'totalMeetings'
        ));
    }
    public function exportExcel(Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $students      = $course->students()->orderBy('nim')->get();
        $meetings      = $course->meetings()->orderBy('meeting_number')->get();
        $totalMeetings = $meetings->count();

        $recap = [];
        foreach ($students as $student) {
            $hadir = $izin = $sakit = $alpha = 0;
            $detail = [];

            foreach ($meetings as $meeting) {
                $attendance = Attendance::where('meeting_id', $meeting->id)
                                       ->where('student_id', $student->id)
                                       ->first();
                $status = $attendance ? $attendance->status : null;
                $detail[$meeting->id] = $status;

                if ($status === 'hadir') $hadir++;
                elseif ($status === 'izin') $izin++;
                elseif ($status === 'sakit') $sakit++;
                elseif ($status === 'alpha') $alpha++;
            }

            // Hitung persentase berdasarkan formula kelas
            $formula = $course->attendance_formula ?? 'hadir_only';
            $effectiveHadir = $formula === 'include_izin_sakit'
                             ? $hadir + $izin + $sakit
                             : $hadir;

            $percentage = $totalMeetings > 0
                         ? round(($effectiveHadir / $totalMeetings) * 100, 1)
                         : 0;

            $recap[] = [
                'nim'        => $student->nim,
                'name'       => $student->name,
                'hadir'      => $hadir,
                'izin'       => $izin,
                'sakit'      => $sakit,
                'alpha'      => $alpha,
                'percentage' => $percentage,
                'warning' => $percentage < $course->attendance_threshold,
                'detail'     => $detail,
            ];
        }

        $filename = 'Rekap_Absensi_' . str_replace(' ', '_', $course->name) . '_' .
                    str_replace(' ', '_', $course->class_name) . '_' .
                    str_replace([' ', '/'], ['_', '-'], $course->semester) . '.xlsx';

        return Excel::download(new AttendanceExport($course, $meetings, $recap, auth()->user()->full_name), $filename);
    }
}