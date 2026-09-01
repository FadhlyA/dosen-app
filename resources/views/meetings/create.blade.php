@extends('layouts.app')

@section('title', 'Tambah Pertemuan')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">➕ Tambah Pertemuan - {{ $course->name }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('meetings.store', $course) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pertemuan Ke-</label>
                        <input type="number" name="meeting_number" class="form-control"
                               value="{{ old('meeting_number', $nextNumber) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Judul Pertemuan</label>
                        <input type="text" name="title" class="form-control"
                               value="{{ old('title') }}" placeholder="contoh: Pengenalan HTML" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi / Catatan</label>
                        <textarea name="description" class="form-control" rows="4"
                                  placeholder="Apa yang akan dibahas di pertemuan ini...">{{ old('description') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal Pertemuan</label>
                        <input type="date" name="meeting_date" class="form-control"
                               value="{{ old('meeting_date') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <select name="status" class="form-select">
                            <option value="upcoming" {{ old('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                            <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('courses.show', $course) }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection