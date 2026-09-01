@extends('layouts.app')

@section('title', 'Rekap Nilai')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">📊 Rekap Nilai</h4>
        <small class="text-muted">Pilih kelas untuk mengelola nilai mahasiswa</small>
    </div>
    <a href="{{ route('courses.create') }}" class="btn btn-primary">+ Tambah Kelas</a>
</div>

@if($courses->isEmpty())
    <div class="alert alert-info">
        Belum ada kelas. <a href="{{ route('courses.create') }}">Tambah kelas pertama!</a>
    </div>
@else
    <div class="row">
        @foreach($courses as $course)
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold">{{ $course->name }}</h5>
                    <p class="text-muted mb-1"><small>{{ $course->code }} | {{ $course->class_name }}</small></p>
                    <p class="text-muted mb-2"><small>{{ $course->semester }}</small></p>
                    <span class="badge bg-primary">
                        {{ $course->gradeComponents->count() }} Komponen Nilai
                    </span>
                </div>
                <div class="card-footer">
                    <a href="{{ route('grades.course', $course) }}" class="btn btn-success btn-sm w-100">
                        📊 Kelola Nilai
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection