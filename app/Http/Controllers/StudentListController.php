<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Student;
use App\Exports\StudentExport;
use Maatwebsite\Excel\Facades\Excel;

class StudentListController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $students = $course->students()->orderBy('nim')->get();
        return view('students.index', compact('course', 'students'));
    }

    public function store(Request $request, Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $request->validate([
            'nim'   => 'required|string|max:50',
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $exists = $course->students()->where('nim', $request->nim)->exists();
        if ($exists) {
            return back()->withErrors(['nim' => 'NIM sudah terdaftar di kelas ini!']);
        }

        $course->students()->create($request->only(['nim', 'name', 'email']));

        return redirect()->route('students.index', $course)
                        ->with('success', 'Mahasiswa berhasil ditambahkan!');
    }

    public function update(Request $request, Course $course, Student $student)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $request->validate([
            'nim'   => 'required|string|max:50',
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $exists = $course->students()
                        ->where('nim', $request->nim)
                        ->where('id', '!=', $student->id)
                        ->exists();
        if ($exists) {
            return back()->withErrors(['nim' => 'NIM sudah terdaftar di kelas ini!']);
        }

        $student->update($request->only(['nim', 'name', 'email']));

        return redirect()->route('students.index', $course)
                        ->with('success', 'Data mahasiswa berhasil diupdate!');
    }

    public function destroy(Course $course, Student $student)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $student->delete();
        return redirect()->route('students.index', $course)
                        ->with('success', 'Mahasiswa berhasil dihapus!');
    }

    public function import(Request $request, Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file   = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');

        $imported = 0;
        $skipped  = 0;
        $row      = 0;

        while (($line = fgetcsv($handle, 1000, ',')) !== false) {
            $row++;

            // Skip header
            if ($row === 1) continue;

            // Skip baris kosong
            if (empty(array_filter($line))) continue;

            $nim   = trim($line[0] ?? '');
            $name  = trim($line[1] ?? '');
            $email = trim($line[2] ?? '') ?: null;

            if (empty($nim) || empty($name)) {
                $skipped++;
                continue;
            }

            Student::updateOrCreate(
                ['course_id' => $course->id, 'nim' => $nim],
                ['name' => $name, 'email' => $email]
            );

            $imported++;
        }
        fclose($handle);
        $message = "Import selesai: $imported mahasiswa berhasil diimport.";
        if ($skipped > 0) $message .= " $skipped baris dilewati.";

        return redirect()->route('students.index', $course)
                        ->with('success', $message);
    }
    public function exportExcel(Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $students = $course->students()->orderBy('nim')->get();

        $filename = 'Daftar_Mahasiswa_' . str_replace(' ', '_', $course->name) . '_' .
                    str_replace(' ', '_', $course->class_name) . '_' .
                    str_replace([' ', '/'], ['_', '-'], $course->semester) . '.xlsx';

        return Excel::download(new StudentExport($course, $students, auth()->user()->full_name), $filename);
    }
}