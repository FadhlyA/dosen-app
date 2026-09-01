@extends('layouts.app')

@section('title', 'Edit Pertemuan')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning">
                <h5 class="mb-0">✏️ Edit Pertemuan - {{ $course->name }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('meetings.update', [$course, $meeting]) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pertemuan Ke-</label>
                        <input type="number" name="meeting_number" class="form-control"
                               value="{{ old('meeting_number', $meeting->meeting_number) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Judul Pertemuan</label>
                        <input type="text" name="title" class="form-control"
                               value="{{ old('title', $meeting->title) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi / Catatan</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description', $meeting->description) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal Pertemuan</label>
                        <input type="date" name="meeting_date" class="form-control"
                               value="{{ old('meeting_date', $meeting->meeting_date) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <select name="status" class="form-select">
                            <option value="upcoming" {{ $meeting->status == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                            <option value="done" {{ $meeting->status == 'done' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">Update</button>
                        <a href="{{ route('meetings.show', [$course, $meeting]) }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection