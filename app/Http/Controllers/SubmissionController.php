<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Meeting;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    // Form upload tugas mahasiswa
    public function create(Course $course, Meeting $meeting, Assignment $assignment)
    {
        if (session('student_course_id') !== $course->id) {
            return redirect()->route('student.index');
        }
        
        if (!session('student_nim')) {
            return redirect()->route('student.verify-nim', $course->id);
        }

        return view('submissions.create', compact('course', 'meeting', 'assignment'));
    }

    // Simpan tugas mahasiswa
    public function store(Request $request, Course $course, Meeting $meeting, Assignment $assignment)
    {
        if (session('student_course_id') !== $course->id) {
            return redirect()->route('student.index');
        }

        if (!session('student_nim')) {
            return redirect()->route('student.verify-nim', $course->id);
        }

        $request->validate([
            'file_path' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar|max:10240',
            'note'      => 'nullable|string',
        ]);

        $filePath = $request->file('file_path')->store('submissions', 'public');

        $assignment->submissions()->create([
            'student_name' => session('student_name'),
            'student_nim'  => session('student_nim'),
            'file_path'    => $filePath,
            'note'         => $request->note,
        ]);

        return redirect()->route('student.meeting', [$course, $meeting])
                        ->with('success', 'Tugas berhasil dikumpulkan!');
    }

    // Dosen download 1 tugas mahasiswa
    public function download(Course $course, Meeting $meeting, Assignment $assignment, Submission $submission)
    {
        if ($course->user_id !== auth()->id()) abort(403);
        return Storage::disk('public')->download($submission->file_path);
    }

    // Dosen download semua tugas dalam 1 ZIP
    public function downloadAll(Course $course, Meeting $meeting, Assignment $assignment)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $submissions = $assignment->submissions;

        if ($submissions->isEmpty()) {
            return back()->with('error', 'Belum ada submission untuk didownload.');
        }

        $zipName = 'Tugas_' . $meeting->meeting_number . '_' .
                   str_replace(' ', '_', $course->name) . '_' .
                   str_replace(' ', '_', $course->class_name) . '.zip';

        $tempPath = storage_path('app/temp/');
        if (!file_exists($tempPath)) {
            mkdir($tempPath, 0755, true);
        }

        $zipPath = $tempPath . $zipName;

        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        foreach ($submissions as $submission) {
            $filePath = storage_path('app/public/' . $submission->file_path);
            if (file_exists($filePath)) {
                $ext = pathinfo($filePath, PATHINFO_EXTENSION);
                $fileName = $submission->student_nim . '_' .
                           str_replace(' ', '_', $submission->student_name) . '.' . $ext;
                $zip->addFile($filePath, $fileName);
            }
        }

        $zip->close();

        return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
    }

    // Hapus 1 submission
    public function destroy(Course $course, Meeting $meeting, Assignment $assignment, Submission $submission)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        if ($submission->file_path) {
            Storage::disk('public')->delete($submission->file_path);
        }

        $submission->delete();
        return redirect()->route('assignments.show', [$course, $meeting, $assignment])
                        ->with('success', 'Submission berhasil dihapus!');
    }
    // Update nilai submission via AJAX
    public function updateScore(Request $request, Submission $submission)
    {
        $request->validate(['score' => 'nullable|numeric|min:0|max:100']);

        $submission->update(['score' => $request->score ?: null]);

        return response()->json(['success' => true]);
    }
}