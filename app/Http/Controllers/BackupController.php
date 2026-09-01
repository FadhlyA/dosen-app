<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Exports\GradeExport;
use App\Exports\AttendanceExport;
use App\Exports\AssignmentExport;
use App\Exports\StudentExport;
use App\Models\Attendance;
use App\Models\Submission;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BackupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Halaman backup
    public function index()
    {
        $courses = Course::where('user_id', auth()->id())->latest()->get();
        return view('backup.index', compact('courses'));
    }

    // Backup semua kelas
    public function downloadAll()
    {
        $courses = Course::where('user_id', auth()->id())->get();
        return $this->generateBackup($courses, 'Semua_Kelas');
    }

    // Backup per kelas
    public function downloadCourse(Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);
        return $this->generateBackup(collect([$course]), $course->name . '_' . $course->class_name);
    }

    // Generate ZIP backup
    private function generateBackup($courses, $label)
    {
        $dosenName  = auth()->user()->full_name;
        $date       = now()->format('Y-m-d');
        $tempPath   = storage_path('app/temp/backup/');
        $zipName    = 'Backup_' . str_replace([' ', '/'], '_', $dosenName) . '_' . $date . '.zip';
        $zipPath    = $tempPath . $zipName;

        if (!file_exists($tempPath)) mkdir($tempPath, 0755, true);

        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($courses as $course) {
            $folderName = str_replace([' ', '/'], '_', $course->name) . '_' .
                          str_replace([' ', '/'], '_', $course->class_name) . '_' .
                          str_replace([' ', '/'], '_', $course->semester);

            // Generate Excel files
            $this->addExcelToZip($zip, $course, $folderName, $dosenName);

            // Tambahkan file materi
            $this->addMaterialsToZip($zip, $course, $folderName);

            // Tambahkan file submission
            $this->addSubmissionsToZip($zip, $course, $folderName);
        }

        $zip->close();

        return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
    }

    private function addExcelToZip(ZipArchive $zip, Course $course, $folderName, $dosenName)
    {
        // Rekap Nilai
        $components  = $course->gradeComponents()->with('grades')->get();
        $students    = $course->students()->orderBy('nim')->get();
        $totalWeight = $components->sum('weight');

        $gradeController = new GradeController();
        $finalGrades = $this->buildFinalGrades($components, $students, $totalWeight, $course);

        $gradeFile = storage_path('app/temp/backup/grade_' . $course->id . '.xlsx');
        Excel::store(new GradeExport($course, $components, $finalGrades, $dosenName), 'temp/backup/grade_' . $course->id . '.xlsx');
        if (file_exists($gradeFile)) {
            $zip->addFile($gradeFile, $folderName . '/Rekap_Nilai.xlsx');
        }

        // Rekap Absensi
        $meetings      = $course->meetings()->orderBy('meeting_number')->get();
        $totalMeetings = $meetings->count();
        $recap         = $this->buildAttendanceRecap($students, $meetings, $totalMeetings);

        $absenFile = storage_path('app/temp/backup/absen_' . $course->id . '.xlsx');
        Excel::store(new AttendanceExport($course, $meetings, $recap, $dosenName), 'temp/backup/absen_' . $course->id . '.xlsx');
        if (file_exists($absenFile)) {
            $zip->addFile($absenFile, $folderName . '/Rekap_Absensi.xlsx');
        }

        // Rekap Tugas
        $assignments = \App\Models\Assignment::whereHas('meeting', function($q) use ($course) {
            $q->where('course_id', $course->id);
        })->with('meeting')->orderBy('id')->get();
        $tugasRecap = $this->buildAssignmentRecap($students, $assignments);

        $tugasFile = storage_path('app/temp/backup/tugas_' . $course->id . '.xlsx');
        Excel::store(new AssignmentExport($course, $assignments, $tugasRecap, $dosenName), 'temp/backup/tugas_' . $course->id . '.xlsx');
        if (file_exists($tugasFile)) {
            $zip->addFile($tugasFile, $folderName . '/Rekap_Tugas.xlsx');
        }

        // Daftar Mahasiswa
        $mhsFile = storage_path('app/temp/backup/mhs_' . $course->id . '.xlsx');
        Excel::store(new StudentExport($course, $students, $dosenName), 'temp/backup/mhs_' . $course->id . '.xlsx');
        if (file_exists($mhsFile)) {
            $zip->addFile($mhsFile, $folderName . '/Daftar_Mahasiswa.xlsx');
        }
    }

    private function addMaterialsToZip(ZipArchive $zip, Course $course, $folderName)
    {
        $meetings = $course->meetings()->with('materials')->get();
        foreach ($meetings as $meeting) {
            foreach ($meeting->materials as $material) {
                if ($material->type === 'file' && $material->file_path) {
                    $filePath = storage_path('app/public/' . $material->file_path);
                    if (file_exists($filePath)) {
                        $zip->addFile($filePath, $folderName . '/Materi/' . basename($filePath));
                    }
                }
            }
        }
    }

    private function addSubmissionsToZip(ZipArchive $zip, Course $course, $folderName)
    {
        $assignments = \App\Models\Assignment::whereHas('meeting', function($q) use ($course) {
            $q->where('course_id', $course->id);
        })->with('submissions')->get();

        foreach ($assignments as $assignment) {
            foreach ($assignment->submissions as $submission) {
                if ($submission->file_path) {
                    $filePath = storage_path('app/public/' . $submission->file_path);
                    if (file_exists($filePath)) {
                        $subFolder = $folderName . '/Submissions/' . str_replace([' ', '/'], '_', $assignment->title) . '/';
                        $zip->addFile($filePath, $subFolder . $submission->student_nim . '_' . basename($filePath));
                    }
                }
            }
        }
    }

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
                    $grade = $component->grades->where('student_nim', $student->nim)->first();
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
                'letter' => $this->getLetterGrade($totalScore),
            ];
        }
        return $finalGrades;
    }

    private function buildAttendanceRecap($students, $meetings, $totalMeetings)
    {
        $recap = [];
        foreach ($students as $student) {
            $hadir = $izin = $sakit = $alpha = 0;
            $detail = [];
            foreach ($meetings as $meeting) {
                $attendance = Attendance::where('meeting_id', $meeting->id)
                                       ->where('student_id', $student->id)->first();
                $status = $attendance ? $attendance->status : null;
                $detail[$meeting->id] = $status;
                if ($status === 'hadir') $hadir++;
                elseif ($status === 'izin') $izin++;
                elseif ($status === 'sakit') $sakit++;
                elseif ($status === 'alpha') $alpha++;
            }
            $percentage = $totalMeetings > 0 ? round(($hadir / $totalMeetings) * 100, 1) : 0;
            $recap[] = [
                'nim' => $student->nim, 'name' => $student->name,
                'hadir' => $hadir, 'izin' => $izin, 'sakit' => $sakit, 'alpha' => $alpha,
                'percentage' => $percentage, 'warning' => $percentage < 75, 'detail' => $detail,
            ];
        }
        return $recap;
    }

    private function buildAssignmentRecap($students, $assignments)
    {
        $recap = [];
        foreach ($students as $student) {
            $submissions = [];
            $totalKumpul = 0;
            foreach ($assignments as $assignment) {
                $submission = Submission::where('assignment_id', $assignment->id)
                                       ->where('student_nim', $student->nim)->first();
                $submissions[$assignment->id] = $submission ? true : false;
                if ($submission) $totalKumpul++;
            }
            $recap[] = [
                'nim' => $student->nim, 'name' => $student->name,
                'submissions' => $submissions, 'total' => $totalKumpul,
                'total_all' => $assignments->count(), 'warning' => $totalKumpul < $assignments->count(),
            ];
        }
        return $recap;
    }

    private function getLetterGrade($score)
    {
        if ($score >= 85) return 'A';
        if ($score >= 75) return 'B';
        if ($score >= 65) return 'C';
        if ($score >= 55) return 'D';
        return 'E';
    }
}