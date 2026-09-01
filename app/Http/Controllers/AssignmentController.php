<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Meeting;
use App\Models\Assignment;
use App\Exports\AssignmentExport;
use Maatwebsite\Excel\Facades\Excel;

class AssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Form tambah tugas
    public function create(Course $course, Meeting $meeting)
    {
        if ($course->user_id !== auth()->id()) abort(403);
        return view('assignments.create', compact('course', 'meeting'));
    }

    // Simpan tugas baru
    public function store(Request $request, Course $course, Meeting $meeting)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'required|date',
        ]);

        $meeting->assignments()->create($request->only([
            'title', 'description', 'due_date'
        ]));

        return redirect()->route('meetings.show', [$course, $meeting])
                        ->with('success', 'Tugas berhasil ditambahkan!');
    }

    // Detail tugas + daftar submission mahasiswa
    public function show(Course $course, Meeting $meeting, Assignment $assignment)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $students    = $course->students()->orderBy('nim')->get();
        $submissions = $assignment->submissions()->get()->keyBy('student_nim');

        return view('assignments.show', compact('course', 'meeting', 'assignment', 'students', 'submissions'));
    }

    // Hapus tugas
    public function destroy(Course $course, Meeting $meeting, Assignment $assignment)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $assignment->delete();
        return redirect()->route('meetings.show', [$course, $meeting])
                        ->with('success', 'Tugas berhasil dihapus!');
    }
    // Rekap tugas semua mahasiswa per kelas
    public function recap(Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $students    = $course->students()->orderBy('nim')->get();
        $assignments = \App\Models\Assignment::whereHas('meeting', function($q) use ($course) {
            $q->where('course_id', $course->id);
        })->with('meeting')->orderBy('id')->get();

        $recap = [];
        foreach ($students as $student) {
            $submissions = [];
            $totalKumpul = 0;

            foreach ($assignments as $assignment) {
                $submission = \App\Models\Submission::where('assignment_id', $assignment->id)
                                                   ->where('student_nim', $student->nim)
                                                   ->first();
                $submissions[$assignment->id] = $submission ? true : false;
                if ($submission) $totalKumpul++;
            }

            $recap[$student->id] = [
                'nim'         => $student->nim,
                'name'        => $student->name,
                'submissions' => $submissions,
                'total'       => $totalKumpul,
                'total_all'   => $assignments->count(),
                'warning'     => $totalKumpul < $assignments->count(),
            ];
        }

        return view('assignments.recap', compact('course', 'students', 'assignments', 'recap'));
    }

    // Cetak rekap tugas
    public function printRecap(Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $students    = $course->students()->orderBy('nim')->get();
        $assignments = \App\Models\Assignment::whereHas('meeting', function($q) use ($course) {
            $q->where('course_id', $course->id);
        })->with('meeting')->orderBy('id')->get();

        $recap = [];
        foreach ($students as $student) {
            $submissions = [];
            $totalKumpul = 0;

            foreach ($assignments as $assignment) {
                $submission = \App\Models\Submission::where('assignment_id', $assignment->id)
                                                   ->where('student_nim', $student->nim)
                                                   ->first();
                $submissions[$assignment->id] = $submission ? true : false;
                if ($submission) $totalKumpul++;
            }

            $recap[$student->id] = [
                'nim'         => $student->nim,
                'name'        => $student->name,
                'submissions' => $submissions,
                'total'       => $totalKumpul,
                'total_all'   => $assignments->count(),
                'warning'     => $totalKumpul < $assignments->count(),
            ];
        }

        return view('assignments.print-recap', compact('course', 'students', 'assignments', 'recap'));
    }
    public function exportExcel(Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $students    = $course->students()->orderBy('nim')->get();
        $assignments = \App\Models\Assignment::whereHas('meeting', function($q) use ($course) {
            $q->where('course_id', $course->id);
        })->with('meeting')->orderBy('id')->get();

        $recap = [];
        foreach ($students as $student) {
            $submissions = [];
            $totalKumpul = 0;

            foreach ($assignments as $assignment) {
                $submission = \App\Models\Submission::where('assignment_id', $assignment->id)
                                                   ->where('student_nim', $student->nim)
                                                   ->first();
                $submissions[$assignment->id] = $submission ? true : false;
                if ($submission) $totalKumpul++;
            }

            $recap[] = [
                'nim'         => $student->nim,
                'name'        => $student->name,
                'submissions' => $submissions,
                'total'       => $totalKumpul,
                'total_all'   => $assignments->count(),
                'warning'     => $totalKumpul < $assignments->count(),
            ];
        }

        $filename = 'Rekap_Tugas_' . str_replace(' ', '_', $course->name) . '_' .
            str_replace(' ', '_', $course->class_name) . '_' .
            str_replace([' ', '/'], ['_', '-'], $course->semester) . '.xlsx';

        return Excel::download(new AssignmentExport($course, $assignments, $recap, auth()->user()->full_name), $filename);
    }
    // Update nilai tugas langsung dari tabel (tanpa submission)
    public function updateStudentScore(Request $request, Course $course, Meeting $meeting, Assignment $assignment)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $request->validate([
            'student_nim' => 'required|string',
            'score'       => 'nullable|numeric|min:0|max:100',
        ]);

        $student = $course->students()->where('nim', $request->student_nim)->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Mahasiswa tidak ditemukan']);
        }

        // Cari submission yang ada atau buat entry nilai saja
        $submission = $assignment->submissions()->where('student_nim', $request->student_nim)->first();

        if ($submission) {
            $submission->update(['score' => $request->score]);
        } else {
            // Buat submission tanpa file untuk input nilai langsung
            $assignment->submissions()->create([
                'student_name' => $student->name,
                'student_nim'  => $student->nim,
                'file_path'    => null,
                'note'         => 'Input nilai langsung oleh dosen',
                'score'        => $request->score,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Nilai berhasil disimpan!']);
    }
}