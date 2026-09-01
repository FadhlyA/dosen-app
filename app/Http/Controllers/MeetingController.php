<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Meeting;

class MeetingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Form tambah pertemuan
    public function create(Course $course)
    {
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }

        // Hitung pertemuan berikutnya otomatis
        $nextNumber = $course->meetings()->max('meeting_number') + 1;
        return view('meetings.create', compact('course', 'nextNumber'));
    }

    // Simpan pertemuan baru
    public function store(Request $request, Course $course)
    {
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'meeting_date'   => 'required|date',
            'meeting_number' => 'required|integer',
            'status'         => 'required|in:upcoming,done',
        ]);

        $course->meetings()->create($request->only([
            'title', 'description', 'meeting_date', 'meeting_number', 'status'
        ]));

        return redirect()->route('courses.show', $course)
                        ->with('success', 'Pertemuan berhasil ditambahkan!');
    }

    // Tampilkan detail pertemuan
    public function show(Course $course, Meeting $meeting)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $materials = $meeting->materials()->get();

        $prevMeeting = $course->meetings()
                        ->where('meeting_number', '<', $meeting->meeting_number)
                        ->orderBy('meeting_number', 'desc')->first();
        $nextMeeting = $course->meetings()
                        ->where('meeting_number', '>', $meeting->meeting_number)
                        ->orderBy('meeting_number', 'asc')->first();

        return view('meetings.show', compact('course', 'meeting', 'materials', 'prevMeeting', 'nextMeeting'));
    }

    // Form edit pertemuan
    public function edit(Course $course, Meeting $meeting)
    {
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }
        return view('meetings.edit', compact('course', 'meeting'));
    }

    // Update pertemuan
    public function update(Request $request, Course $course, Meeting $meeting)
    {
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'meeting_date' => 'required|date',
            'status'       => 'required|in:upcoming,done',
        ]);

        $meeting->update($request->only([
            'title', 'description', 'meeting_date', 'status'
        ]));

        return redirect()->route('meetings.show', [$course, $meeting])
                        ->with('success', 'Pertemuan berhasil diupdate!');
    }

    // Hapus pertemuan
    public function destroy(Course $course, Meeting $meeting)
    {
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }

        $meeting->delete();
        return redirect()->route('courses.show', $course)
                        ->with('success', 'Pertemuan berhasil dihapus!');
    }
    // Simpan note pertemuan
    public function updateNote(Request $request, Course $course, Meeting $meeting)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $request->validate([
            'note_before' => 'nullable|string',
            'note_after'  => 'nullable|string',
        ]);

        $meeting->update([
            'note_before' => $request->note_before,
            'note_after'  => $request->note_after,
        ]);

        return redirect()->route('meetings.show', [$course, $meeting])
                        ->with('success', 'Note berhasil disimpan!');
    }
    // Generate pertemuan otomatis
    public function generate(Request $request, Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $request->validate([
            'start_date'   => 'required|date',
            'total_meetings' => 'required|integer|min:1|max:20',
        ]);

        $startDate     = \Carbon\Carbon::parse($request->start_date);
        $totalMeetings = $request->total_meetings;
        $lastNumber    = $course->meetings()->max('meeting_number') ?? 0;

        for ($i = 1; $i <= $totalMeetings; $i++) {
            $meetingNumber = $lastNumber + $i;
            $date          = $startDate->copy()->addWeeks($i - 1);

            $course->meetings()->create([
                'meeting_number' => $meetingNumber,
                'title'          => 'Pertemuan ' . $meetingNumber,
                'description'    => null,
                'meeting_date'   => $date->format('Y-m-d'),
                'status'         => 'upcoming',
            ]);
        }

        return redirect()->route('courses.show', $course)
                        ->with('success', $totalMeetings . ' pertemuan berhasil digenerate!');
    }
    // Update status pertemuan otomatis berdasarkan tanggal
    public function autoUpdateStatus(Course $course)
    {
        if ($course->user_id !== auth()->id()) abort(403);

        $today    = \Carbon\Carbon::today();
        $updated  = 0;

        $meetings = $course->meetings()->get();

        foreach ($meetings as $meeting) {
            $meetingDate = \Carbon\Carbon::parse($meeting->meeting_date);

            if ($meetingDate->lt($today) && $meeting->status === 'upcoming') {
                $meeting->update(['status' => 'done']);
                $updated++;
            }
        }

        return redirect()->route('courses.show', $course)
                        ->with('success', "Status diperbarui: $updated pertemuan ditandai Selesai!");
    }
}