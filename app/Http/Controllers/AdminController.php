<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->is_admin) {
                abort(403, 'Akses ditolak.');
            }
            return $next($request);
        });
    }

    // Dashboard admin
    public function index()
    {
        $totalDosen    = User::where('is_admin', false)->count();
        $totalStorage  = User::where('is_admin', false)->sum('storage_used');
        $recentDosen   = User::where('is_admin', false)->latest()->take(5)->get();

        return view('admin.index', compact('totalDosen', 'totalStorage', 'recentDosen'));
    }

    // Daftar semua dosen
    public function dosens()
    {
        $dosens = User::where('is_admin', false)->latest()->get();
        return view('admin.dosens', compact('dosens'));
    }

    // Detail dosen
    public function dosenDetail(User $user)
    {
        if ($user->is_admin) abort(403);

        $courses       = $user->courses()->withCount('meetings', 'students')->get();
        $totalCourses  = $courses->count();
        $totalStudents = $courses->sum('students_count');

        return view('admin.dosen-detail', compact('user', 'courses', 'totalCourses', 'totalStudents'));
    }

    // Hapus akun dosen
    public function dosenDestroy(User $user)
    {
        if ($user->is_admin) abort(403);

        $user->delete();
        return redirect()->route('admin.dosens')
                        ->with('success', 'Akun dosen berhasil dihapus!');
    }

    // Update storage limit dosen
    public function updateStorage(Request $request, User $user)
    {
        if ($user->is_admin) abort(403);

        $request->validate([
            'storage_limit' => 'required|integer|min:104857600', // min 100MB
        ]);

        $user->update(['storage_limit' => $request->storage_limit]);

        return redirect()->route('admin.dosen-detail', $user)
                        ->with('success', 'Storage limit berhasil diupdate!');
    }
}