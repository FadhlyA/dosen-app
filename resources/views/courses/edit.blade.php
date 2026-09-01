@extends('layouts.app')

@section('title', 'Edit Kelas')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning">
                <h5 class="mb-0">✏️ Edit Kelas</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('courses.update', $course) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Mata Kuliah</label>
                        <input type="text" name="name" class="form-control" 
                               value="{{ old('name', $course->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kode Mata Kuliah</label>
                        <input type="text" name="code" class="form-control" 
                               value="{{ old('code', $course->code) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Kelas</label>
                        <input type="text" name="class_name" class="form-control" 
                               value="{{ old('class_name', $course->class_name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Semester</label>
                        <input type="text" name="semester" class="form-control" 
                               value="{{ old('semester', $course->semester) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $course->description) }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">Update Kelas</button>
                        <a href="{{ route('courses.show', $course) }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection