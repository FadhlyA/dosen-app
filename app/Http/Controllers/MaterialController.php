<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Meeting;
use App\Models\Material;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(Course $course, Meeting $meeting)
    {
        if ($course->user_id !== auth()->id()) abort(403);
        return view('materials.create', compact('course', 'meeting'));
    }

    public function store(Request $request, Course $course, Meeting $meeting)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $request->validate([
            'title'     => 'required|string|max:255',
            'type'      => 'required|in:file,link',
            'file_path' => 'required_if:type,file|file|mimes:pdf,ppt,pptx,doc,docx,html,htm|max:10240',
            'link_url'  => 'required_if:type,link|nullable|url',
        ]);

        $filePath = null;
        if ($request->type === 'file' && $request->hasFile('file_path')) {
            // Hitung storage
            $fileSize = $request->file('file_path')->getSize();
            auth()->user()->addStorageUsed($fileSize);

            // Warning jika melebihi limit
            if (auth()->user()->isStorageFull()) {
                session()->flash('warning', 'Peringatan: Storage Anda sudah melebihi batas 200MB!');
            }
        }

        $meeting->materials()->create([
            'title'     => $request->title,
            'type'      => $request->type,
            'file_path' => $filePath,
            'link_url'  => $request->link_url,
        ]);

        return redirect()->route('meetings.show', [$course, $meeting])
                        ->with('success', 'Materi berhasil ditambahkan!');
    }

    // Download materi — HTML wajib force download
    public function download(Course $course, Meeting $meeting, Material $material)
    {
        if ($material->type !== 'file') abort(404);

        $filePath = storage_path('app/public/' . $material->file_path);
        if (!file_exists($filePath)) abort(404);

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        // HTML & HTM wajib force download, tidak boleh dibuka langsung di browser
        if (in_array($extension, ['html', 'htm'])) {
            return response()->download($filePath);
        }

        // File lain bisa dibuka langsung di browser
        return response()->file($filePath);
    }

    public function destroy(Course $course, Meeting $meeting, Material $material)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
            // Kurangi storage
            $fileSize = Storage::disk('public')->size($material->file_path);
            auth()->user()->reduceStorageUsed($fileSize);
        }

        $material->delete();
        return redirect()->route('meetings.show', [$course, $meeting])
                        ->with('success', 'Materi berhasil dihapus!');
    }
}