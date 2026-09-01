<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\GradeComponent;
use App\Models\Grade;
use App\Exports\GradeExport;
use Maatwebsite\Excel\Facades\Excel;

class GradeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Halaman pilih kelas untuk rekap nilai
    public function index()
    {
        $courses = Course::where('user_id', auth()->id())
                        ->with('gradeComponents', 'students')
                        ->latest()->get();
        return view('grades.index', compact('courses'));
    }

    // Halaman rekap nilai per kelas
    public function course(Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $components = $course->gradeComponents()->with('grades')->get();
        $students   = $course->students()->orderBy('nim')->get();

        $totalWeight = $components->sum('weight');
        $finalGrades = [];

        foreach ($students as $student) {
            $totalScore = 0;
            $scores = [];
            // Hitung absensi
            $totalMeetings = $course->meetings()->count();
            $hadirCount    = \App\Models\Attendance::where('student_id', $student->id)
                                ->whereHas('meeting', function($q) use ($course) {
                                    $q->where('course_id', $course->id);
                                })->where('status', 'hadir')->count();
            $absenPct = $totalMeetings > 0 ? round(($hadirCount / $totalMeetings) * 100, 1) : 0;

            // Hitung tugas
            $totalAssignments = \App\Models\Assignment::whereHas('meeting', function($q) use ($course) {
                                    $q->where('course_id', $course->id);
                                })->count();
            $tugasKumpul = \App\Models\Submission::where('student_nim', $student->nim)
                                ->whereHas('assignment.meeting', function($q) use ($course) {
                                    $q->where('course_id', $course->id);
                                })->count();

            foreach ($components as $component) {
                // Jika komponen terhubung absensi, hitung otomatis
                if ($component->is_attendance) {
                    $score = $component->calculateAttendanceScore($student, $course);
                } elseif ($component->is_assignment_based) {
                    $score = $component->calculateAssignmentScore($student, $course);
                } else {
                    $grade = $component->grades
                                        ->where('student_nim', $student->nim)
                                        ->first();
                    $score = $grade ? $grade->score : null;
                }

                $scores[$component->id] = $score;

                if ($totalWeight > 0 && $score !== null) {
                    $totalScore += $score * ($component->weight / 100);
                }
            }

            $finalGrades[$student->nim] = [
                'name'        => $student->name,
                'email'       => $student->email,
                'scores'      => $scores,
                'final'       => round($totalScore, 2),
                'letter' => $this->getLetterGrade($totalScore, $course),
                'absen_pct'   => $absenPct,          // tambah ini
                'absen_warn'  => $absenPct < 75,     // tambah ini
                'tugas_kumpul'=> $tugasKumpul,       // tambah ini
                'tugas_total' => $totalAssignments,  // tambah ini
            ];
        }

        return view('grades.course', compact(
            'course', 'components', 'students', 'finalGrades', 'totalWeight'
        ));
    }

    // Simpan komponen nilai baru
    public function storeComponent(Request $request, Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $request->validate([
            'name'               => 'required|string|max:255',
            'weight'             => 'required|numeric|min:1|max:100',
            'is_attendance'      => 'nullable|boolean',
            'attendance_formula' => 'nullable|in:hadir_only,include_izin_sakit',
        ]);

        $course->gradeComponents()->create([
            'name'               => $request->name,
            'weight'             => $request->weight,
            'is_attendance'      => $request->boolean('is_attendance'),
            'attendance_formula' => $request->input('attendance_formula', 'hadir_only'),
            'is_assignment_based' => $request->boolean('is_assignment_based'),
        ]);

        return redirect()->route('grades.components', $course)
                        ->with('success', 'Komponen nilai berhasil ditambahkan!');
    }

    // Hapus komponen nilai
    public function destroyComponent(Course $course, GradeComponent $component)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $component->delete();
        return redirect()->route('grades.course', $course)
                        ->with('success', 'Komponen nilai berhasil dihapus!');
    }

    // Simpan / update nilai mahasiswa
    public function storeGrade(Request $request, Course $course, GradeComponent $component)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $request->validate([
            'student_nim' => 'required|string',
            'score'       => 'required|numeric|min:0|max:100',
        ]);

        // Ambil nama dari daftar mahasiswa
        $student = $course->students()->where('nim', $request->student_nim)->first();
        if (!$student) {
            return back()->withErrors(['student_nim' => 'Mahasiswa tidak ditemukan!']);
        }

        Grade::updateOrCreate(
            [
                'grade_component_id' => $component->id,
                'student_nim'        => $request->student_nim,
            ],
            [
                'student_name' => $student->name,
                'score'        => $request->score,
            ]
        );

        return redirect()->route('grades.course', $course)
                        ->with('success', 'Nilai ' . $student->name . ' berhasil disimpan!');
    }

    // Hapus nilai mahasiswa
    public function destroyGrade(Course $course, GradeComponent $component, Grade $grade)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $grade->delete();
        return redirect()->route('grades.course', $course)
                        ->with('success', 'Nilai berhasil dihapus!');
    }

    // Export rekap nilai ke CSV
    public function export(Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $components = $course->gradeComponents()->with('grades')->get();
        $students   = $course->students()->orderBy('nim')->get();
        $totalWeight = $components->sum('weight');

        $rows = [];

        // Header CSV
        $header = ['NIM', 'Nama Mahasiswa'];
        foreach ($components as $component) {
            $header[] = $component->name . ' (' . $component->weight . '%)';
        }
        $header[] = 'Nilai Akhir';
        $header[] = 'Nilai Huruf';
        $rows[] = $header;

        // Data per mahasiswa
        foreach ($students as $student) {
            $row = [$student->nim, $student->name];
            $totalScore = 0;

            foreach ($components as $component) {
                if ($component->is_attendance) {
                    $score = $component->calculateAttendanceScore($student, $course);
                } else {
                    $grade = $component->grades->where('student_nim', $student->nim)->first();
                    $score = $grade ? $grade->score : 0;
                }
                $row[] = $score;
                if ($totalWeight > 0) {
                    $totalScore += $score * ($component->weight / 100);
                }
            }

            $finalScore = round($totalScore, 2);
            $row[] = $finalScore;
            $row[] = $this->getLetterGrade($finalScore, $course);
            $rows[] = $row;
        }

        // Generate CSV
        $filename = 'Rekap_Nilai_' . str_replace(' ', '_', $course->name) . '_' .
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

    // Halaman kelola komponen nilai
    public function components(Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $components = $course->gradeComponents()->with('grades')->get();
        $students   = $course->students()->count();
        $totalWeight = $components->sum('weight');

        return view('grades.components', compact('course', 'components', 'totalWeight', 'students'));
    }

    // Halaman detail nilai per mahasiswa
    public function studentDetail(Course $course, $nim)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        // Ambil data mahasiswa di kelas ini
        $student = $course->students()->where('nim', $nim)->firstOrFail();

        // Ambil komponen & nilai mahasiswa ini
        $components = $course->gradeComponents()->with(['grades' => function($q) use ($nim) {
            $q->where('student_nim', $nim);
        }])->get();

        // Hitung nilai akhir
        $totalWeight = $components->sum('weight');
        $totalScore  = 0;
        $totalScore = 0;
        foreach ($components as $component) {
            if ($component->is_attendance) {
                $score = $component->calculateAttendanceScore($student, $course);
            } else {
                $grade = $component->grades->first();
                $score = $grade ? $grade->score : null;
            }

            if ($score !== null && $totalWeight > 0) {
                $totalScore += $score * ($component->weight / 100);
            }
        }
        $finalScore  = round($totalScore, 2);
        $letterGrade = $this->getLetterGrade($finalScore, $course);
        $finalScore   = round($totalScore, 2);
        $letterGrade  = $this->getLetterGrade($finalScore);

        // Cari kelas lain yang diikuti mahasiswa dengan NIM sama
        $otherCourses = \App\Models\Student::where('nim', $nim)
                        ->where('course_id', '!=', $course->id)
                        ->with('course')
                        ->get();

        // Ambil semua tugas yang dikumpulkan mahasiswa ini di kelas ini
        $submissions = \App\Models\Submission::where('student_nim', $nim)
                        ->whereHas('assignment.meeting', function($q) use ($course) {
                            $q->where('course_id', $course->id);
                        })
                        ->with('assignment.meeting')
                        ->latest()
                        ->get();

        return view('grades.student-detail', compact(
            'course', 'student', 'components',
            'finalScore', 'letterGrade', 'totalWeight',
            'otherCourses', 'submissions'
        ));
    }

    // Update nilai mahasiswa dari halaman detail
    public function updateGrade(Request $request, Course $course, GradeComponent $component, $nim)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
        ]);

        $student = $course->students()->where('nim', $nim)->firstOrFail();

        Grade::updateOrCreate(
            [
                'grade_component_id' => $component->id,
                'student_nim'        => $nim,
            ],
            [
                'student_name' => $student->name,
                'score'        => $request->score,
            ]
        );

        return redirect()->route('grades.student-detail', [$course, $nim])
                        ->with('success', 'Nilai berhasil diupdate!');
    }
    // Import nilai via CSV
    public function import(Request $request, Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file   = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');

        $imported   = 0;
        $skipped    = 0;
        $errors     = [];
        $row        = 0;
        $header     = null;

        $components = $course->gradeComponents()->get()->keyBy('name');

        while (($line = fgetcsv($handle, 1000, ',')) !== false) {
            $row++;

            // Baris 1 = header, simpan
            if ($row === 1) {
                $header = $line;
                continue;
            }

            // Skip baris kosong
            if (empty(array_filter($line))) continue;

            $nim  = trim($line[0] ?? '');
            $nama = trim($line[1] ?? '');

            if (empty($nim)) {
                $skipped++;
                continue;
            }

            // Cek mahasiswa terdaftar
            $student = $course->students()->where('nim', $nim)->first();
            if (!$student) {
                $errors[] = "Baris $row: NIM $nim tidak terdaftar.";
                $skipped++;
                continue;
            }

            // Kolom komponen mulai dari index 2
            for ($i = 2; $i < count($header); $i++) {
                $compName = trim($header[$i] ?? '');
                $score    = trim($line[$i] ?? '');

                if (empty($compName) || $score === '') continue;

                $component = $components->get($compName);
                if (!$component) {
                    if (!in_array("Komponen '$compName' tidak ditemukan.", $errors)) {
                        $errors[] = "Komponen '$compName' tidak ditemukan.";
                    }
                    continue;
                }

                if (!is_numeric($score) || $score < 0 || $score > 100) {
                    $errors[] = "Baris $row NIM $nim: Nilai '$score' tidak valid.";
                    continue;
                }

                Grade::updateOrCreate(
                    [
                        'grade_component_id' => $component->id,
                        'student_nim'        => $nim,
                    ],
                    [
                        'student_name' => $student->name,
                        'score'        => (float) $score,
                    ]
                );

                $imported++;
            }
        }

        fclose($handle);

        $message = "Import selesai: $imported nilai berhasil diimport.";
        if ($skipped > 0) $message .= " $skipped baris dilewati.";
        if (!empty($errors)) $message .= ' | ' . implode(' | ', $errors);

        return redirect()->route('grades.course', $course)
                        ->with('success', $message);
    }

    // Halaman cetak rekap nilai (print langsung)
    public function printGrades(Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $components  = $course->gradeComponents()->with('grades')->get();
        $students    = $course->students()->orderBy('nim')->get();
        $totalWeight = $components->sum('weight');
        $finalGrades = $this->buildFinalGrades($components, $students, $totalWeight, $course);

        return view('grades.print', compact(
            'course', 'components', 'students', 'finalGrades', 'totalWeight'
        ));
    }

    

    // Helper: build final grades (dipakai oleh print & pdf)
    private function buildFinalGrades($components, $students, $totalWeight, $course)
    {
        $finalGrades = [];

        foreach ($students as $student) {
            $totalScore = 0;
            $scores     = [];

            foreach ($components as $component) {
                if ($component->is_attendance) {
                    $score = $component->calculateAttendanceScore($student, $course);
                } elseif ($component->is_assignment_based) {
                    $score = $component->calculateAssignmentScore($student, $course);
                } else {
                    $grade = $component->grades
                                       ->where('student_nim', $student->nim)
                                       ->first();
                    $score = $grade ? $grade->score : null;
                }

                $scores[$component->id] = $score;

                if ($totalWeight > 0 && $score !== null) {
                    $totalScore += $score * ($component->weight / 100);
                }
            }

            $finalGrades[$student->nim] = [
                'name'   => $student->name,
                'scores' => $scores,
                'final'  => round($totalScore, 2),
                'letter' => $this->getLetterGrade($totalScore, $course),
            ];
        }

        return $finalGrades;
    }

    public function exportExcel(Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $components  = $course->gradeComponents()->with('grades')->get();
        $students    = $course->students()->orderBy('nim')->get();
        $totalWeight = $components->sum('weight');
        $finalGrades = $this->buildFinalGrades($components, $students, $totalWeight, $course);

        $filename = 'Rekap_Nilai_' . str_replace(' ', '_', $course->name) . '_' .
            str_replace(' ', '_', $course->class_name) . '_' .
            str_replace([' ', '/'], ['_', '-'], $course->semester) . '.xlsx';

        return Excel::download(new GradeExport($course, $components, $finalGrades, auth()->user()->full_name), $filename);
    }
    // Download template CSV import nilai
    public function downloadTemplate(Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $students   = $course->students()->orderBy('nim')->get();
        $components = $course->gradeComponents()
                            ->where('is_attendance', false)
                            ->where('is_assignment_based', false)
                            ->get();

        $rows = [];

        // Header
        $header = ['nim', 'nama'];
        foreach ($components as $component) {
            $header[] = $component->name;
        }
        $rows[] = $header;

        // Data mahasiswa
        foreach ($students as $student) {
            $row = [$student->nim, $student->name];
            foreach ($components as $component) {
                $row[] = ''; // kosong untuk diisi dosen
            }
            $rows[] = $row;
        }

        $filename = 'Template_Nilai_' . str_replace([' ', '/'], '_', $course->name) . '_' .
                    str_replace([' ', '/'], '_', $course->class_name) . '.csv';

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
    // Simpan setting absensi per kelas
    public function saveAttendanceSettings(Request $request, Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $request->validate([
            'attendance_formula'   => 'required|in:hadir_only,include_izin_sakit',
            'attendance_threshold' => 'required|numeric|min:1|max:100',
        ]);

        $course->update([
            'attendance_formula'   => $request->attendance_formula,
            'attendance_threshold' => $request->attendance_threshold,
        ]);

        return redirect()->route('grades.components', $course)
                        ->with('success', 'Setting absensi berhasil disimpan!');
    }
    // Konversi nilai angka ke huruf
    private function getLetterGrade($score, $course = null)
    {
        if ($course) {
            $configs = $course->gradeLetterConfigs()->orderBy('order')->get();
            if ($configs->isNotEmpty()) {
                foreach ($configs as $config) {
                    if ($score >= $config->min_score && $score <= $config->max_score) {
                        return $config->letter;
                    }
                }
                return 'E';
            }
        }
        
        // Fallback default
        if ($score >= 85) return 'A';
        if ($score >= 75) return 'B';
        if ($score >= 65) return 'C';
        if ($score >= 55) return 'D';
        return 'E';
    }
    // Update bobot komponen tetap
    public function updateComponent(Request $request, Course $course, GradeComponent $component)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $request->validate([
            'weight' => 'required|numeric|min:1|max:100',
        ]);

        $component->update(['weight' => $request->weight]);

        return redirect()   ->route('grades.components', $course)
                            ->with('success', 'Bobot ' . $component->name . ' berhasil diupdate!');
    }
    // Tampilkan konfigurasi nilai huruf
    public function gradeLetters(Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);
        $configs = $course->gradeLetterConfigs()->orderBy('order')->get();
        return view('grades.grade-letters', compact('course', 'configs'));
    }

    // Simpan konfigurasi nilai huruf
    public function saveGradeLetters(Request $request, Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $request->validate([
            'letters'           => 'required|array|min:1',
            'letters.*.letter'  => 'required|string|max:5',
            'letters.*.min'     => 'required|numeric|min:0|max:100',
            'letters.*.max'     => 'required|numeric|min:0|max:100',
        ]);

        // Hapus konfigurasi lama
        $course->gradeLetterConfigs()->delete();

        // Simpan konfigurasi baru
        foreach ($request->letters as $order => $item) {
            if (empty($item['letter'])) continue;
            $course->gradeLetterConfigs()->create([
                'letter'    => strtoupper($item['letter']),
                'min_score' => $item['min'],
                'max_score' => $item['max'],
                'order'     => $order + 1,
            ]);
        }

        return redirect()->route('grades.grade-letters', $course)
                        ->with('success', 'Konfigurasi nilai huruf berhasil disimpan!');
    }
}