<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Rps;
use Illuminate\Support\Facades\Storage;

class RpsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Halaman daftar RPS per kelas
    public function index(Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $rpsList = $course->rps()->latest()->get();
        return view('rps.index', compact('course', 'rpsList'));
    }

    // Upload RPS baru
    public function store(Request $request, Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $request->validate([
            'title'    => 'required|string|max:255',
            'rps_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $file         = $request->file('rps_file');
        $originalName = $file->getClientOriginalName();
        $filePath     = $file->store('rps', 'public');
        // Hitung storage
        auth()->user()->addStorageUsed($file->getSize());

        $course->rps()->create([
            'title'         => $request->title,
            'file_path'     => $filePath,
            'original_name' => $originalName,
        ]);

        return redirect()->route('rps.index', $course)
                        ->with('success', 'RPS berhasil diupload!');
    }

    // Download RPS
    public function download(Course $course, Rps $rps)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $filePath = storage_path('app/public/' . $rps->file_path);

        if (!file_exists($filePath)) {
            return back()->with('error', 'File tidak ditemukan!');
        }

        return response()->download($filePath, $rps->original_name);
    }

    // Hapus RPS
    public function destroy(Course $course, Rps $rps)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        Storage::disk('public')->delete($rps->file_path);
        $fileSize = Storage::disk('public')->exists($rps->file_path)
                   ? Storage::disk('public')->size($rps->file_path) : 0;
        auth()->user()->reduceStorageUsed($fileSize);
        $rps->delete();

        return redirect()->route('rps.index', $course)
                        ->with('success', 'RPS berhasil dihapus!');
    }
}