@extends('layouts.app')

@section('title', 'Semua Tugas - ' . $course->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-0">📋 Semua Tugas - {{ $course->name }}</h4>
        <small class="text-muted">{{ $course->class_name }} | {{ $course->semester }}</small>
    </div>
    <a href="{{ route('courses.show', $course) }}" class="btn btn-secondary btn-sm">← Kembali</a>
</div>

@forelse($meetings as $meeting)
    @if($meeting->assignments->count() > 0)
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">
                📅 Pertemuan {{ $meeting->meeting_number }} — {{ $meeting->title }}
            </h6>
            <small>{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y') }}</small>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Judul Tugas</th>
                        <th>Deadline</th>
                        <th class="text-center">Pengumpulan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($meeting->assignments as $assignment)
                    <tr>
                        <td class="fw-bold">{{ $assignment->title }}</td>
                        <td>{{ \Carbon\Carbon::parse($assignment->due_date)->format('d M Y') }}</td>
                        <td class="text-center">
                            <span class="badge bg-primary">
                                {{ $assignment->submissions->count() }} file
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('assignments.show', [$course, $meeting, $assignment]) }}"
                               class="btn btn-sm btn-info">👁️ Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
@empty
    <div class="alert alert-info">Belum ada pertemuan.</div>
@endforelse

@php $totalAssignments = $meetings->sum(fn($m) => $m->assignments->count()); @endphp
@if($totalAssignments === 0)
    <div class="alert alert-info">Belum ada tugas di kelas ini.</div>
@endif
@endsection