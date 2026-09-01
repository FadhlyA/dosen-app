@extends('layouts.app')
@section('title', $course->name)
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 style="font-family:'Sora',sans-serif;font-weight:700;margin:0;">{{ $course->name }}</h4>
        <p style="color:var(--ld-slate);font-size:0.85rem;margin:0;">{{ $course->code }} · {{ $course->class_name }} · {{ $course->semester }}</p>
    </div>
    <a href="{{ route('courses.index') }}" class="btn btn-sm btn-secondary">← Kembali</a>
</div>

<div class="row g-3 mb-4">
    {{-- Access Key --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center p-4">
                <p style="font-size:0.78rem;color:var(--ld-slate);margin-bottom:8px;">Key Mahasiswa</p>
                <div style="font-family:'JetBrains Mono',monospace;font-size:1.6rem;font-weight:600;color:var(--ld-blue);background:var(--ld-sky);padding:12px 20px;border-radius:12px;letter-spacing:4px;display:inline-block;margin-bottom:1rem;">
                    {{ $course->access_key }}
                </div>
                <form action="{{ route('courses.regenerate-key', $course) }}" method="POST"
                      onsubmit="return confirm('Generate key baru? Key lama tidak berlaku!')">
                    @csrf
                    <button class="btn btn-sm btn-danger w-100 mb-2">🔄 Generate Key Baru</button>
                </form>
                <button class="btn btn-sm w-100" style="background:var(--ld-sky);color:var(--ld-blue);"
                        data-bs-toggle="modal" data-bs-target="#duplicateModal">
                    📋 Duplikasi Kelas
                </button>
            </div>
        </div>
    </div>

    {{-- Info --}}
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6"><small style="color:var(--ld-slate);">Mata Kuliah</small><div style="font-weight:600;">{{ $course->name }}</div></div>
                    <div class="col-6"><small style="color:var(--ld-slate);">Kode</small><div style="font-weight:600;">{{ $course->code }}</div></div>
                    <div class="col-6"><small style="color:var(--ld-slate);">Kelas</small><div style="font-weight:600;">{{ $course->class_name }}</div></div>
                    <div class="col-6"><small style="color:var(--ld-slate);">Semester</small><div style="font-weight:600;">{{ $course->semester }}</div></div>
                    @if($course->description)
                    <div class="col-12"><small style="color:var(--ld-slate);">Deskripsi</small><div>{{ $course->description }}</div></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="row g-2 mb-4">
    @php
    $actions = [
        ['icon'=>'👥','label'=>'Mahasiswa','route'=>route('students.index',$course),'color'=>'#16A34A'],
        ['icon'=>'📊','label'=>'Nilai','route'=>route('grades.course',$course),'color'=>'#2563EB'],
        ['icon'=>'📋','label'=>'Absensi','route'=>route('attendances.recap',$course),'color'=>'#0891B2'],
        ['icon'=>'📝','label'=>'Rekap Tugas','route'=>route('assignments.recap',$course),'color'=>'#D97706'],
        ['icon'=>'📎','label'=>'Materi','route'=>route('courses.all-materials',$course),'color'=>'#7C3AED'],
        ['icon'=>'📋','label'=>'Semua Tugas','route'=>route('courses.all-assignments',$course),'color'=>'#DC2626'],
        ['icon'=>'📄','label'=>'RPS','route'=>route('rps.index',$course),'color'=>'#64748B'],
        ['icon'=>'✏️','label'=>'Edit','route'=>route('courses.edit',$course),'color'=>'#0F172A'],
    ];
    @endphp
    @foreach($actions as $a)
    <div class="col-6 col-md-3 col-lg-auto flex-lg-fill">
        <a href="{{ $a['route'] }}" class="text-decoration-none">
            <div class="card text-center p-3" style="border-top:3px solid {{ $a['color'] }};transition:transform 0.15s;"
                 onmouseover="this.style.transform='translateY(-2px)'"
                 onmouseout="this.style.transform=''">
                <div style="font-size:1.4rem;">{{ $a['icon'] }}</div>
                <div style="font-size:0.72rem;font-weight:600;color:{{ $a['color'] }};margin-top:4px;">{{ $a['label'] }}</div>
            </div>
        </a>
    </div>
    @endforeach
</div>

{{-- Daftar Pertemuan --}}
<div class="card">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <span style="font-weight:600;">📅 Daftar Pertemuan</span>
        <div class="d-flex gap-2">
            <form action="{{ route('meetings.auto-status', $course) }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-sm" style="background:rgba(255,255,255,0.12);color:#fff;font-size:0.78rem;">🔄 Auto Status</button>
            </form>
            <button class="btn btn-sm" style="background:rgba(255,255,255,0.12);color:#fff;font-size:0.78rem;"
                    data-bs-toggle="modal" data-bs-target="#generateModal">⚡ Generate</button>
            <a href="{{ route('meetings.create', $course) }}" class="btn btn-sm" style="background:rgba(255,255,255,0.12);color:#fff;font-size:0.78rem;">+ Tambah</a>
        </div>
    </div>
    <div class="card-body p-0">
        @if($meetings->isEmpty())
            <div class="text-center py-5" style="color:var(--ld-slate);">
                <div style="font-size:2rem;">📅</div>
                <p class="mt-2 mb-0">Belum ada pertemuan.</p>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="tableMeetings">
                <thead style="background:var(--ld-sky);">
                    <tr>
                        <th>Ke-</th>
                        <th>Judul</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($meetings as $meeting)
                    <tr>
                        <td style="font-weight:700;color:var(--ld-blue);font-family:'JetBrains Mono',monospace;">{{ $meeting->meeting_number }}</td>
                        <td style="font-weight:600;">{{ $meeting->title }}</td>
                        <td style="color:var(--ld-slate);font-size:0.85rem;">{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y') }}</td>
                        <td>
                            @if($meeting->status === 'done')
                                <span style="font-size:0.75rem;background:#F0FDF4;color:#16A34A;padding:3px 10px;border-radius:20px;font-weight:600;">✅ Selesai</span>
                            @else
                                <span style="font-size:0.75rem;background:var(--ld-sky);color:var(--ld-blue);padding:3px 10px;border-radius:20px;font-weight:600;">📅 Upcoming</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('meetings.show', [$course, $meeting]) }}" class="btn btn-sm btn-primary">Detail</a>
                                <a href="{{ route('attendances.index', [$course, $meeting]) }}" class="btn btn-sm" style="background:#ECFEFF;color:#0891B2;">Absen</a>
                                <form action="{{ route('meetings.destroy', [$course, $meeting]) }}" method="POST"
                                      onsubmit="return confirm('Hapus pertemuan ini?')">
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

{{-- Modal Generate --}}
<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">⚡ Generate Pertemuan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('meetings.generate', $course) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Pertemuan Pertama</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah Pertemuan</label>
                        <input type="number" name="total_meetings" class="form-control" min="1" max="20" value="16" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">⚡ Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Duplikasi --}}
<div class="modal fade" id="duplicateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">📋 Duplikasi Kelas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('courses.duplicate', $course) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kelas Baru</label>
                        <input type="text" name="new_class_name" class="form-control" value="{{ $course->class_name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Semester Baru</label>
                        <input type="text" name="new_semester" class="form-control" placeholder="Ganjil 2025/2026" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">📋 Duplikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#tableMeetings').DataTable({
        language:{ search:"🔍 Cari:", lengthMenu:"Tampilkan _MENU_ data", info:"_START_-_END_ dari _TOTAL_", paginate:{first:"«",last:"»",next:"›",previous:"‹"} },
        columnDefs:[{ orderable:false, targets:-1 }],
        order:[[0,'asc']], pageLength:16,
    });
});
</script>
@endpush