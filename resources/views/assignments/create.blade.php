@extends('layouts.app')

@section('title', 'Tambah Tugas')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">➕ Tambah Tugas - Pertemuan {{ $meeting->meeting_number }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('assignments.store', [$course, $meeting]) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Judul Tugas</label>
                        <input type="text" name="title" class="form-control"
                               value="{{ old('title') }}" placeholder="contoh: Tugas 1 - Membuat Form HTML" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Instruksi / Deskripsi</label>
                        <textarea name="description" class="form-control" rows="5"
                                  placeholder="Jelaskan instruksi tugas secara detail...">{{ old('description') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deadline</label>
                        <input type="date" name="due_date" class="form-control"
                               value="{{ old('due_date') }}" required>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan Tugas</button>
                        <a href="{{ route('meetings.show', [$course, $meeting]) }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection