<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Meeting;
use App\Models\QrToken;
use App\Models\Attendance;
use Illuminate\Support\Str;

class QrAttendanceController extends Controller
{
    // Halaman QR untuk dosen (generate & tampilkan QR)
    public function show(Course $course, Meeting $meeting)
    {
        if ($course->user_id !== auth()->id()) abort(403);
        return view('qr.show', compact('course', 'meeting'));
    }

    // Generate token baru (dipanggil via AJAX tiap 1 menit)
    public function generate(Course $course, Meeting $meeting)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        // Hapus token lama untuk pertemuan ini
        QrToken::where('meeting_id', $meeting->id)->delete();

        // Buat token baru
        $token = QrToken::create([
            'meeting_id' => $meeting->id,
            'token'      => Str::random(32),
            'scan_count' => 0,
            'max_scan'   => 2,
            'expires_at' => now()->addMinute(),
        ]);

        return response()->json([
            'token'      => $token->token,
            'expires_at' => $token->expires_at->timestamp,
            'qr_url'     => route('qr.scan', $token->token),
        ]);
    }

    // Halaman scan QR untuk mahasiswa
    public function scanPage($token)
    {
        $qrToken = QrToken::where('token', $token)->first();

        if (!$qrToken || !$qrToken->isValid()) {
            return view('qr.invalid');
        }

        $meeting = $qrToken->meeting;
        $course  = $meeting->course;

        // Cek session mahasiswa
        if (session('student_course_id') !== $course->id || !session('student_nim')) {
            // Simpan token di session lalu redirect ke verifikasi
            session(['pending_qr_token' => $token]);
            session(['student_course_id' => $course->id]);
            return redirect()->route('student.verify-nim', $course->id);
        }

        return view('qr.scan', compact('qrToken', 'meeting', 'course'));
    }

    // Proses absensi via QR
    public function process(Request $request, $token)
    {
        $qrToken = QrToken::where('token', $token)->first();

        if (!$qrToken || !$qrToken->isValid()) {
            return response()->json(['success' => false, 'message' => 'QR Code tidak valid atau sudah kadaluarsa!']);
        }

        $meeting = $qrToken->meeting;
        $course  = $meeting->course;

        // Cek session mahasiswa
        if (session('student_course_id') !== $course->id || !session('student_nim')) {
            return response()->json(['success' => false, 'message' => 'Silakan login terlebih dahulu!']);
        }

        $nim     = session('student_nim');
        $student = $course->students()->where('nim', $nim)->first();

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'NIM tidak terdaftar di kelas ini!']);
        }

        // Cek sudah absen belum
        $existing = Attendance::where('meeting_id', $meeting->id)
                              ->where('student_id', $student->id)
                              ->first();

        if ($existing && $existing->status === 'hadir') {
            return response()->json(['success' => false, 'message' => 'Anda sudah tercatat hadir!']);
        }

        // Catat absensi
        Attendance::updateOrCreate(
            ['meeting_id' => $meeting->id, 'student_id' => $student->id],
            ['status' => 'hadir', 'note' => 'Via QR Code']
        );

        // Tambah scan count
        $qrToken->increment('scan_count');

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil! ' . session('student_name') . ' tercatat Hadir.',
        ]);
    }
}