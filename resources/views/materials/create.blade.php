@extends('layouts.app')

@section('title', 'Tambah Materi')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">➕ Tambah Materi - Pertemuan {{ $meeting->meeting_number }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('materials.store', [$course, $meeting]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Judul Materi</label>
                        <input type="text" name="title" class="form-control"
                               value="{{ old('title') }}" placeholder="contoh: Slide Pengenalan HTML" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipe Materi</label>
                        <select name="type" class="form-select" id="typeSelect" onchange="toggleType()">
                            <option value="file" {{ old('type') == 'file' ? 'selected' : '' }}>📄 Upload File (PDF/PPT)</option>
                            <option value="link" {{ old('type') == 'link' ? 'selected' : '' }}>🔗 Link (Google Drive/YouTube)</option>
                        </select>
                    </div>

                    {{-- Input File --}}
                    <div class="mb-3" id="fileInput">
                        <label class="form-label fw-bold">Upload File</label>
                        <input type="file" name="file_path" class="form-control"
                               accept=".pdf,.ppt,.pptx,.doc,.docx">
                        <small class="text-muted">Format: PDF, PPT, PPTX, DOC, DOCX. Maksimal 10MB.</small>
                    </div>

                    {{-- Input Link --}}
                    <div class="mb-3 d-none" id="linkInput">
                        <label class="form-label fw-bold">URL Link</label>
                        <input type="url" name="link_url" class="form-control"
                               value="{{ old('link_url') }}" placeholder="https://drive.google.com/...">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan Materi</button>
                        <a href="{{ route('meetings.show', [$course, $meeting]) }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleType() {
    const type = document.getElementById('typeSelect').value;
    document.getElementById('fileInput').classList.toggle('d-none', type !== 'file');
    document.getElementById('linkInput').classList.toggle('d-none', type !== 'link');
}
// Jalankan saat halaman load
toggleType();
</script>
@endsection