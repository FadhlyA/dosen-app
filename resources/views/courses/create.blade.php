@extends('layouts.app')

@section('title', 'Tambah Kelas')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">➕ Tambah Kelas Baru</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('courses.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Mata Kuliah</label>
                        <input type="text" name="name" class="form-control" 
                               value="{{ old('name') }}" placeholder="contoh: Pemrograman Web" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kode Mata Kuliah</label>
                        <input type="text" name="code" class="form-control" 
                               value="{{ old('code') }}" placeholder="contoh: TI301" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Kelas</label>
                        <input type="text" name="class_name" class="form-control" 
                               value="{{ old('class_name') }}" placeholder="contoh: TI-3A" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Semester</label>
                        <input type="text" name="semester" class="form-control" 
                               value="{{ old('semester') }}" placeholder="contoh: Ganjil 2024/2025" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi <span class="text-muted">(opsional)</span></label>
                        <textarea name="description" class="form-control" rows="3" 
                                  placeholder="Deskripsi singkat mata kuliah...">{{ old('description') }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan Kelas</button>
                        <a href="{{ route('courses.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection