@extends('layouts.app')
@section('title', 'Pertemuan ' . $meeting->meeting_number)
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h4 style="font-family:'Sora',sans-serif;font-weight:700;margin:0;">
            Pertemuan {{ $meeting->meeting_number }} — {{ $meeting->title }}
        </h4>
        <p style="color:var(--ld-slate);font-size:0.85rem;margin:0;">
            {{ $course->name }} · {{ $course->class_name }}
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        @if($prevMeeting)
            <a href="{{ route('meetings.show', [$course, $prevMeeting]) }}"
               class="btn btn-sm btn-secondary">← P{{ $prevMeeting->meeting_number }}</a>
        @endif
        @if($nextMeeting)
            <a href="{{ route('meetings.show', [$course, $nextMeeting]) }}"
               class="btn btn-sm btn-secondary">P{{ $nextMeeting->meeting_number }} →</a>
        @endif
        <a href="{{ route('courses.show', $course) }}" class="btn btn-sm btn-secondary">↑ Kelas</a>
    </div>
</div>

<div class="row g-3 mb-3">
    {{-- Info Pertemuan --}}
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header bg-primary text-white" style="font-weight:600;">📋 Info Pertemuan</div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <small style="color:var(--ld-slate);">Tanggal</small>
                        <div style="font-weight:600;">{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y') }}</div>
                    </div>
                    <div class="col-6">
                        <small style="color:var(--ld-slate);">Status</small>
                        <div>
                            @if($meeting->status === 'done')
                                <span style="font-size:0.78rem;background:#F0FDF4;color:#16A34A;padding:3px 12px;border-radius:20px;font-weight:600;">✅ Selesai</span>
                            @else
                                <span style="font-size:0.78rem;background:var(--ld-sky);color:var(--ld-blue);padding:3px 12px;border-radius:20px;font-weight:600;">📅 Upcoming</span>
                            @endif
                        </div>
                    </div>
                    @if($meeting->description)
                    <div class="col-12">
                        <small style="color:var(--ld-slate);">Deskripsi</small>
                        <div>{{ $meeting->description }}</div>
                    </div>
                    @endif
                </div>

                <hr style="border-color:var(--ld-border);">

                <h6 style="font-family:'Sora',sans-serif;font-weight:600;font-size:0.85rem;margin-bottom:12px;">📝 Catatan Dosen</h6>
                <form action="{{ route('meetings.note', [$course, $meeting]) }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label" style="font-size:0.78rem;color:var(--ld-slate);">Sebelum Pertemuan</label>
                        <textarea name="note_before" class="form-control form-control-sm" rows="2"
                                  placeholder="Rencana, persiapan, target materi...">{{ $meeting->note_before }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:0.78rem;color:var(--ld-slate);">Sesudah Pertemuan</label>
                        <textarea name="note_after" class="form-control form-control-sm" rows="2"
                                  placeholder="Evaluasi, catatan, yang belum tersampaikan...">{{ $meeting->note_after }}</textarea>
                    </div>
                    <button class="btn btn-sm btn-primary">💾 Simpan Catatan</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Aksi --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header bg-dark text-white" style="font-weight:600;">⚡ Aksi Cepat</div>
            <div class="card-body d-flex flex-column gap-2">
                <a href="{{ route('attendances.index', [$course, $meeting]) }}"
                   class="btn btn-sm" style="background:#ECFEFF;color:#0891B2;text-align:left;">
                    📋 Input Absensi
                </a>
                <a href="{{ route('qr.show', [$course, $meeting]) }}"
                   class="btn btn-sm" style="background:#F5F3FF;color:#7C3AED;text-align:left;">
                    📱 QR Code Absensi
                </a>
                <a href="{{ route('materials.create', [$course, $meeting]) }}"
                   class="btn btn-sm" style="background:var(--ld-sky);color:var(--ld-blue);text-align:left;">
                    📎 Tambah Materi
                </a>
                <a href="{{ route('assignments.create', [$course, $meeting]) }}"
                   class="btn btn-sm" style="background:#FFFBEB;color:#D97706;text-align:left;">
                    📋 Tambah Tugas
                </a>
                <hr style="border-color:var(--ld-border);margin:4px 0;">
                <a href="{{ route('meetings.edit', [$course, $meeting]) }}"
                   class="btn btn-sm btn-secondary" style="text-align:left;">✏️ Edit Pertemuan</a>
                <form action="{{ route('meetings.destroy', [$course, $meeting]) }}" method="POST"
                      onsubmit="return confirm('Hapus pertemuan ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger w-100" style="text-align:left;">🗑️ Hapus Pertemuan</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Materi --}}
<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center"
         style="background:var(--ld-sky);border-bottom:1px solid var(--ld-border);">
        <span style="font-weight:600;color:var(--ld-ink);">📎 Materi Pertemuan</span>
        <a href="{{ route('materials.create', [$course, $meeting]) }}"
           class="btn btn-sm btn-primary">+ Tambah</a>
    </div>
    <div class="card-body">
        @if($materials->isEmpty())
            <div class="text-center py-3" style="color:var(--ld-slate);">
                <small>Belum ada materi.</small>
            </div>
        @else
            <div class="row g-2">
                @foreach($materials as $material)
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3 p-3 rounded"
                         style="background:var(--ld-sky);border:1px solid var(--ld-border);">
                        <div style="font-size:1.5rem;flex:none;">
                            {{ $material->type === 'file' ? '📄' : '🔗' }}
                        </div>
                        <div class="flex-fill" style="min-width:0;">
                            <div style="font-weight:600;font-size:0.85rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                {{ $material->title }}
                            </div>
                            <span style="font-size:0.7rem;padding:2px 8px;border-radius:20px;font-weight:600;
                                background:{{ $material->type==='file'?'#EFF6FF':'#F0FDF4' }};
                                color:{{ $material->type==='file'?'#2563EB':'#16A34A' }};">
                                {{ $material->type === 'file' ? 'File' : 'Link' }}
                            </span>
                        </div>
                        <div class="d-flex gap-1 flex-none">
                            @if($material->type === 'file')
                                <a href="{{ route('materials.download', [$course, $meeting, $material]) }}"
                                   class="btn btn-sm btn-primary">⬇️</a>
                            @else
                                <a href="{{ $material->link_url }}" target="_blank"
                                   class="btn btn-sm" style="background:#F0FDF4;color:#16A34A;">🔗</a>
                            @endif
                            <form action="{{ route('materials.destroy', [$course, $meeting, $material]) }}"
                                  method="POST" onsubmit="return confirm('Hapus materi ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">🗑️</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- Tugas --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center"
         style="background:var(--ld-sky);border-bottom:1px solid var(--ld-border);">
        <span style="font-weight:600;color:var(--ld-ink);">📋 Tugas Pertemuan</span>
        <a href="{{ route('assignments.create', [$course, $meeting]) }}"
           class="btn btn-sm btn-primary">+ Tambah</a>
    </div>
    <div class="card-body p-0">
        @php $assignments = $meeting->assignments; @endphp
        @if($assignments->isEmpty())
            <div class="text-center py-4" style="color:var(--ld-slate);">
                <small>Belum ada tugas.</small>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background:var(--ld-sky);">
                        <tr>
                            <th>Judul Tugas</th>
                            <th>Deadline</th>
                            <th class="text-center">Pengumpulan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assignments as $assignment)
                        <tr>
                            <td style="font-weight:600;">{{ $assignment->title }}</td>
                            <td style="color:var(--ld-slate);font-size:0.85rem;">
                                {{ \Carbon\Carbon::parse($assignment->due_date)->format('d M Y') }}
                            </td>
                            <td class="text-center">
                                <span style="font-size:0.78rem;background:var(--ld-sky);color:var(--ld-blue);padding:3px 10px;border-radius:20px;font-weight:600;">
                                    {{ $assignment->submissions->where('file_path','!=',null)->count() }} file
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('assignments.show', [$course, $meeting, $assignment]) }}"
                                       class="btn btn-sm btn-primary">Detail</a>
                                    <form action="{{ route('assignments.destroy', [$course, $meeting, $assignment]) }}"
                                          method="POST" onsubmit="return confirm('Hapus tugas ini?')">
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
@endsection