@extends('layouts.app')

@section('title', 'Semua Materi - ' . $course->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-0">📎 Semua Materi - {{ $course->name }}</h4>
        <small class="text-muted">{{ $course->class_name }} | {{ $course->semester }}</small>
    </div>
    <a href="{{ route('courses.show', $course) }}" class="btn btn-secondary btn-sm">← Kembali</a>
</div>

@forelse($meetings as $meeting)
    @if($meeting->materials->count() > 0)
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">
                📅 Pertemuan {{ $meeting->meeting_number }} — {{ $meeting->title }}
            </h6>
            <small>{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y') }}</small>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Judul Materi</th>
                        <th class="text-center">Tipe</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($meeting->materials as $material)
                    <tr>
                        <td class="fw-bold">{{ $material->title }}</td>
                        <td class="text-center">
                            @if($material->type === 'file')
                                <span class="badge bg-primary">📄 File</span>
                            @else
                                <span class="badge bg-success">🔗 Link</span>
                            @endif
                        </td>
                        <td>
                            @if($material->type === 'file')
                                <a href="{{ route('materials.download', [$course, $meeting, $material]) }}"
                                   class="btn btn-sm btn-primary">⬇️ Download</a>
                            @else
                                <a href="{{ $material->link_url }}" target="_blank"
                                   class="btn btn-sm btn-success">🔗 Buka Link</a>
                            @endif
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

@php $totalMaterials = $meetings->sum(fn($m) => $m->materials->count()); @endphp
@if($totalMaterials === 0)
    <div class="alert alert-info">Belum ada materi di kelas ini.</div>
@endif
@endsection