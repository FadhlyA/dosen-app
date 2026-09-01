@extends('layouts.app')
@section('title', 'Detail Tugas')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h4 style="font-family:'Sora',sans-serif;font-weight:700;margin:0;">📋 {{ $assignment->title }}</h4>
        <p style="color:var(--ld-slate);font-size:0.85rem;margin:0;">
            {{ $course->name }} · Pertemuan {{ $meeting->meeting_number }}
        </p>
    </div>
    <a href="{{ route('meetings.show', [$course, $meeting]) }}" class="btn btn-sm btn-secondary">← Kembali</a>
</div>

<div class="row g-3 mb-4">
    {{-- Info Tugas --}}
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <small style="color:var(--ld-slate);">Mata Kuliah</small>
                        <div style="font-weight:600;">{{ $course->name }}</div>
                    </div>
                    <div class="col-6">
                        <small style="color:var(--ld-slate);">Pertemuan</small>
                        <div style="font-weight:600;">{{ $meeting->meeting_number }} — {{ $meeting->title }}</div>
                    </div>
                    <div class="col-6">
                        <small style="color:var(--ld-slate);">Deadline</small>
                        <div style="font-weight:600;color:#DC2626;">
                            {{ \Carbon\Carbon::parse($assignment->due_date)->format('d M Y') }}
                        </div>
                    </div>
                    <div class="col-6">
                        <small style="color:var(--ld-slate);">Total File Masuk</small>
                        <div style="font-weight:600;">
                            {{ $submissions->filter(fn($s) => $s->file_path)->count() }} file
                        </div>
                    </div>
                    @if($assignment->description)
                    <div class="col-12">
                        <small style="color:var(--ld-slate);">Instruksi</small>
                        <div style="font-size:0.9rem;line-height:1.6;">{{ $assignment->description }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Aksi --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body d-flex flex-column gap-2 justify-content-center">
                <div class="text-center mb-2">
                    <div style="font-size:2.5rem;font-family:'Sora',sans-serif;font-weight:700;color:var(--ld-blue);">
                        {{ $submissions->filter(fn($s) => $s->file_path)->count() }}
                    </div>
                    <small style="color:var(--ld-slate);">dari {{ $students->count() }} mahasiswa</small>
                </div>
                @if($submissions->filter(fn($s) => $s->file_path)->count() > 0)
                <a href="{{ route('submissions.download-all', [$course, $meeting, $assignment]) }}"
                   class="btn btn-success btn-sm">⬇️ Download Semua (ZIP)</a>
                @endif
                <form action="{{ route('assignments.destroy', [$course, $meeting, $assignment]) }}"
                      method="POST" onsubmit="return confirm('Hapus tugas ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm w-100">🗑️ Hapus Tugas</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Mahasiswa --}}
<div class="card">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <span style="font-weight:600;">📥 Daftar Mahasiswa & Nilai Tugas</span>
        <div class="d-flex align-items-center gap-3">
            <small style="opacity:0.6;font-size:0.72rem;">
                ✅ Ada file &nbsp;📝 Nilai manual &nbsp;❌ Belum
            </small>
            <button type="button" class="btn btn-success btn-sm fw-bold" onclick="saveAllScores()">
                💾 Simpan Semua Nilai
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        @if($students->isEmpty())
            <div class="text-center py-4" style="color:var(--ld-slate);">
                <small>Belum ada mahasiswa.</small>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="assignmentTable">
                <thead style="background:var(--ld-sky);">
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">File</th>
                        <th class="text-center">Catatan</th>
                        <th class="text-center" width="120">Nilai</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    @php
                        $submission = $submissions->get($student->nim);
                        $hasFile    = $submission && $submission->file_path;
                        $hasManual  = $submission && !$submission->file_path && $submission->score !== null;
                    @endphp
                    <tr>
                        <td style="font-family:'JetBrains Mono',monospace;font-size:0.82rem;">{{ $student->nim }}</td>
                        <td style="font-weight:600;">{{ $student->name }}</td>
                        <td class="text-center">
                            @if($hasFile)
                                <span style="font-size:0.72rem;background:#F0FDF4;color:#16A34A;padding:3px 10px;border-radius:20px;font-weight:600;">✅ Ada File</span>
                            @elseif($hasManual)
                                <span style="font-size:0.72rem;background:#EFF6FF;color:#2563EB;padding:3px 10px;border-radius:20px;font-weight:600;">📝 Nilai Manual</span>
                            @else
                                <span style="font-size:0.72rem;background:#FEF2F2;color:#DC2626;padding:3px 10px;border-radius:20px;font-weight:600;">❌ Belum</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($hasFile)
                                <a href="{{ route('submissions.download', [$course, $meeting, $assignment, $submission]) }}"
                                   class="btn btn-sm btn-primary">⬇️</a>
                            @else
                                <span style="color:var(--ld-slate);">-</span>
                            @endif
                        </td>
                        <td class="text-center" style="font-size:0.78rem;color:var(--ld-slate);">
                            {{ $submission && $submission->note ? $submission->note : '-' }}
                        </td>
                        <td class="text-center">
                            <input type="number"
                                   class="form-control form-control-sm score-input"
                                   id="score_{{ $student->nim }}"
                                   data-nim="{{ $student->nim }}"
                                   value="{{ $submission ? $submission->score : '' }}"
                                   min="0" max="100" step="0.01"
                                   style="width:75px;margin:0 auto;text-align:center;"
                                   placeholder="0-100">
                        </td>
                        <td class="text-center">
                            @if($submission)
                                <form action="{{ route('submissions.destroy', [$course, $meeting, $assignment, $submission]) }}"
                                      method="POST" onsubmit="return confirm('Hapus submission ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">🗑️</button>
                                </form>
                            @else
                                <span style="color:var(--ld-slate);">-</span>
                            @endif
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
    $('#assignmentTable').DataTable({
        language:{ search:"🔍 Cari:", lengthMenu:"Tampilkan _MENU_ data", info:"_START_-_END_ dari _TOTAL_", paginate:{first:"«",last:"»",next:"›",previous:"‹"} },
        columnDefs:[{ orderable:false, targets:[2,3,4,5,6] }],
        pageLength:25,
    });
});

const saveUrl   = "{{ route('assignments.student-score', [$course, $meeting, $assignment]) }}";
const csrfToken = "{{ csrf_token() }}";

async function saveAllScores() {
    const inputs = document.querySelectorAll('.score-input');
    let saved = 0, skipped = 0;

    Swal.fire({ title:'Menyimpan...', allowOutsideClick:false, didOpen:()=>Swal.showLoading() });

    for (const input of inputs) {
        const nim   = input.dataset.nim;
        const score = input.value;
        if (score === '') { skipped++; continue; }

        const res = await fetch(saveUrl, {
            method:'POST',
            headers:{ 'X-CSRF-TOKEN':csrfToken, 'Content-Type':'application/json' },
            body: JSON.stringify({ student_nim:nim, score:score })
        });
        const data = await res.json();
        if (data.success) saved++;
    }

    Swal.fire({
        title:'Tersimpan!',
        text: saved + ' nilai disimpan.' + (skipped > 0 ? ' ' + skipped + ' dilewati (kosong).' : ''),
        icon:'success', timer:2000, showConfirmButton:false
    }).then(() => location.reload());
}
</script>
@endpush