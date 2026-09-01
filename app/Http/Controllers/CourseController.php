<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Tampilkan semua kelas dosen
    public function index()
    {
        $courses = Course::where('user_id', auth()->id())
                        ->latest()->get();
        return view('courses.index', compact('courses'));
    }

    // Form tambah kelas baru
    public function create()
    {
        return view('courses.create');
    }

    // Simpan kelas baru
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'code'       => 'required|string|max:50',
            'class_name' => 'required|string|max:100',
            'semester'   => 'required|string|max:100',
            'description'=> 'nullable|string',
        ]);

        $course = Course::create([
            'user_id'    => auth()->id(),
            'name'       => $request->name,
            'code'       => $request->code,
            'class_name' => $request->class_name,
            'semester'   => $request->semester,
            'description'=> $request->description,
            'access_key' => strtoupper(Str::random(8)), // Generate key otomatis
        ]);

         // Buat komponen nilai tetap otomatis
        $course->createDefaultComponents();
        $course->createDefaultGradeLetters(); // tambah ini

        return redirect()->route('courses.index')
                        ->with('success', 'Kelas berhasil ditambahkan!');

    }

    // Tampilkan detail kelas
    public function show(Course $course)
    {
        // Pastikan hanya dosen pemilik yang bisa akses
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }

        $meetings = $course->meetings()->orderBy('meeting_number')->get();
        return view('courses.show', compact('course', 'meetings'));
    }

    // Form edit kelas
    public function edit(Course $course)
    {
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }
        return view('courses.edit', compact('course'));
    }

    // Update kelas
    public function update(Request $request, Course $course)
    {
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name'       => 'required|string|max:255',
            'code'       => 'required|string|max:50',
            'class_name' => 'required|string|max:100',
            'semester'   => 'required|string|max:100',
            'description'=> 'nullable|string',
        ]);

        $course->update($request->only([
            'name', 'code', 'class_name', 'semester', 'description'
        ]));

        return redirect()->route('courses.show', $course)
                        ->with('success', 'Kelas berhasil diupdate!');
    }

    // Regenerate access key
    public function regenerateKey(Course $course)
    {
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }

        $course->update([
            'access_key' => strtoupper(Str::random(8))
        ]);

        return redirect()->route('courses.show', $course)
                        ->with('success', 'Access key berhasil diperbarui!');
    }

    // Hapus kelas
    public function destroy(Course $course)
    {
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }

        $course->delete();
        return redirect()->route('courses.index')
                        ->with('success', 'Kelas berhasil dihapus!');
    }
    // Duplikasi kelas
    public function duplicate(Request $request, Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $request->validate([
            'new_semester'   => 'required|string|max:100',
            'new_class_name' => 'required|string|max:100',
        ]);

        // Buat kelas baru
        $newCourse = Course::create([
            'user_id'     => auth()->id(),
            'name'        => $course->name,
            'code'        => $course->code,
            'class_name'  => $request->new_class_name,
            'semester'    => $request->new_semester,
            'description' => $course->description,
            'access_key'  => strtoupper(\Illuminate\Support\Str::random(8)),
        ]);

        // Salin daftar mahasiswa
        foreach ($course->students as $student) {
            $newCourse->students()->create([
                'nim'   => $student->nim,
                'name'  => $student->name,
                'email' => $student->email,
            ]);
        }

        // Buat komponen nilai tetap otomatis
        $newCourse->createDefaultComponents();

        return redirect()->route('courses.show', $newCourse)
                        ->with('success', 'Kelas berhasil diduplikasi! ' . $course->students->count() . ' mahasiswa disalin.');
    }
    // Rekap semua tugas per kelas
    public function allAssignments(Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $meetings = $course->meetings()
                          ->with(['assignments.submissions', 'materials'])
                          ->orderBy('meeting_number')
                          ->get();

        return view('courses.all-assignments', compact('course', 'meetings'));
    }

    // Rekap semua materi per kelas
    public function allMaterials(Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $meetings = $course->meetings()
                          ->with('materials')
                          ->orderBy('meeting_number')
                          ->get();

        return view('courses.all-materials', compact('course', 'meetings'));
    }
}