@extends('layouts.app')
@section('title', 'Backup Data')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 style="font-family:'Sora',sans-serif;font-weight:700;margin:0;">💾 Backup Data</h4>
        <p style="color:var(--ld-slate);font-size:0.85rem;margin:0;">Download semua data kelas dalam format ZIP</p>
    </div>
    <a href="{{ route('backup.download-all') }}" class="btn btn-primary">⬇️ Download Semua Kelas</a>
</div>

{{-- Storage --}}
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div style="font-weight:600;margin-bottom:6px;">💾 Storage Terpakai</div>
                <div class="progress mb-1" style="height:8px;">
                    <div class="progress-bar"
                         style="width:{{ auth()->user()->storage_percentage }}%;
                                background:{{ auth()->user()->storage_percentage>=90?'#DC2626':(auth()->user()->storage_percentage>=70?'#D97706':'#16A34A') }};">
                    </div>
                </div>
                <small style="color:var(--ld-slate);">
                    {{ auth()->user()->storage_used_readable }} / {{ auth()->user()->storage_limit_readable }}
                    ({{ auth()->user()->storage_percentage }}%)
                </small>
            </div>
            <div class="col-md-6 mt-3 mt-md-0">
                <div class="alert alert-info py-2 small mb-0">
                    📦 Isi ZIP: Rekap Nilai, Absensi, Tugas, Mahasiswa (Excel) + file Materi + Submission
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Backup per Kelas --}}
<div class="card">
    <div class="card-header bg-dark text-white" style="font-weight:600;">📚 Backup per Kelas</div>
    <div class="card-body p-0">
        @if($courses->isEmpty())
            <div class="text-center py-4" style="color:var(--ld-slate);"><small>Belum ada kelas.</small></div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="backupTable">
                <thead style="background:var(--ld-sky);">
                    <tr>
                        <th>Nama Matkul</th>
                        <th>Kelas</th>
                        <th>Semester</th>
                        <th class="text-center">Mahasiswa</th>
                        <th class="text-center">Pertemuan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($courses as $course)
                    <tr>
                        <td style="font-weight:600;">{{ $course->name }}</td>
                        <td>{{ $course->class_name }}</td>
                        <td style="color:var(--ld-slate);font-size:0.85rem;">{{ $course->semester }}</td>
                        <td class="text-center">
                            <span style="font-size:0.75rem;background:var(--ld-sky);color:var(--ld-blue);padding:2px 8px;border-radius:20px;font-weight:600;">
                                {{ $course->students->count() }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span style="font-size:0.75rem;background:var(--ld-sky);color:var(--ld-blue);padding:2px 8px;border-radius:20px;font-weight:600;">
                                {{ $course->meetings_count }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('backup.download-course', $course) }}"
                               class="btn btn-sm btn-success">⬇️ ZIP</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#backupTable').DataTable({
        language:{ search:"🔍 Cari:", lengthMenu:"Tampilkan _MENU_ data", info:"_START_-_END_ dari _TOTAL_", paginate:{first:"«",last:"»",next:"›",previous:"‹"} },
        columnDefs:[{ orderable:false, targets:-1 }],
        pageLength:10,
    });
});
</script>
@endpush