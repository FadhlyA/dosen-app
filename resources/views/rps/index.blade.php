@extends('layouts.app')
@section('title', 'RPS - ' . $course->name)
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 style="font-family:'Sora',sans-serif;font-weight:700;margin:0;">📄 RPS</h4>
        <p style="color:var(--ld-slate);font-size:0.85rem;margin:0;">{{ $course->name }} · {{ $course->class_name }}</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
            ⬆️ Upload RPS
        </button>
        <a href="{{ route('courses.show', $course) }}" class="btn btn-sm btn-secondary">← Kembali</a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <span style="font-weight:600;">📂 Daftar File RPS</span>
        <span style="font-size:0.75rem;opacity:0.7;">{{ $rpsList->count() }} file</span>
    </div>
    <div class="card-body p-0">
        @if($rpsList->isEmpty())
            <div class="text-center py-5" style="color:var(--ld-slate);">
                <div style="font-size:2.5rem;">📄</div>
                <p class="mt-2 mb-3">Belum ada RPS.</p>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    ⬆️ Upload RPS
                </button>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="rpsTable">
                <thead style="background:var(--ld-sky);">
                    <tr>
                        <th>Judul</th>
                        <th>File</th>
                        <th>Diupload</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rpsList as $rps)
                    <tr>
                        <td style="font-weight:600;">{{ $rps->title }}</td>
                        <td>
                            <span style="font-size:0.72rem;background:var(--ld-sky);color:var(--ld-blue);padding:2px 8px;border-radius:20px;font-weight:600;">
                                {{ strtoupper(pathinfo($rps->original_name, PATHINFO_EXTENSION)) }}
                            </span>
                            <span style="font-size:0.82rem;color:var(--ld-slate);margin-left:4px;">{{ $rps->original_name }}</span>
                        </td>
                        <td style="color:var(--ld-slate);font-size:0.85rem;">{{ $rps->created_at->format('d M Y') }}</td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('rps.download', [$course, $rps]) }}"
                                   class="btn btn-sm btn-primary">⬇️</a>
                                <form action="{{ route('rps.destroy', [$course, $rps]) }}" method="POST"
                                      onsubmit="return confirm('Hapus RPS ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

{{-- Modal Upload --}}
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">⬆️ Upload RPS</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('rps.store', $course) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul RPS</label>
                        <input type="text" name="title" class="form-control"
                               placeholder="contoh: RPS Pemrograman Web Genap 2024/2025"
                               value="{{ old('title') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File RPS</label>
                        <input type="file" name="rps_file" class="form-control"
                               accept=".pdf,.doc,.docx" required>
                        <small style="color:var(--ld-slate);">Format: PDF, DOC, DOCX. Maks 10MB.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">⬆️ Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#rpsTable').DataTable({
        language:{ search:"🔍 Cari:", lengthMenu:"Tampilkan _MENU_ data", info:"_START_-_END_ dari _TOTAL_", paginate:{first:"«",last:"»",next:"›",previous:"‹"} },
        columnDefs:[{ orderable:false, targets:-1 }],
        pageLength:10,
    });
});
</script>
@endpush