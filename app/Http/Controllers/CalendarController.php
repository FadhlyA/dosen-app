<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Meeting;

class CalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $courses = Course::where('user_id', auth()->id())->get();
        return view('calendar.index', compact('courses'));
    }

    // Return events dalam format JSON untuk FullCalendar
    public function events(Request $request)
    {
        $courses  = Course::where('user_id', auth()->id())->with('meetings')->get();

        // Warna per kelas
        $colors = [
            '#0d6efd', '#198754', '#dc3545', '#ffc107',
            '#0dcaf0', '#6f42c1', '#fd7e14', '#20c997',
            '#d63384', '#6610f2',
        ];

        $events = [];
        $colorIndex = 0;

        foreach ($courses as $course) {
            $color = $colors[$colorIndex % count($colors)];
            $colorIndex++;

            foreach ($course->meetings as $meeting) {
                $events[] = [
                    'id'              => $meeting->id,
                    'title'           => $course->name . ' - ' . $meeting->title,
                    'start'           => $meeting->meeting_date,
                    'backgroundColor' => $meeting->status === 'done' ? '#6c757d' : $color,
                    'borderColor'     => $meeting->status === 'done' ? '#6c757d' : $color,
                    'textColor'       => '#ffffff',
                    'extendedProps'   => [
                        'course'      => $course->name,
                        'class_name'  => $course->class_name,
                        'meeting_no'  => $meeting->meeting_number,
                        'status'      => $meeting->status,
                        'description' => $meeting->description ?? '-',
                        'url'         => route('meetings.show', [$course, $meeting]),
                    ]
                ];
            }

            // Tambahkan deadline tugas
            foreach ($course->meetings as $meeting) {
                foreach ($meeting->assignments as $assignment) {
                    $events[] = [
                        'id'              => 'assignment_' . $assignment->id,
                        'title'           => '📋 ' . $assignment->title,
                        'start'           => $assignment->due_date,
                        'backgroundColor' => '#dc3545',
                        'borderColor'     => '#dc3545',
                        'textColor'       => '#ffffff',
                        'extendedProps'   => [
                            'course'     => $course->name,
                            'class_name' => $course->class_name,
                            'type'       => 'assignment',
                            'status'     => 'deadline',
                            'url'        => route('assignments.show', [$course, $meeting, $assignment]),
                        ]
                    ];
                }
            }
        }

        return response()->json($events);
    }
}