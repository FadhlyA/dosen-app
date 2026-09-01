@extends('layouts.app')
@section('title', 'Absensi Pertemuan ' . $meeting->meeting_number)
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h4 style="font-family:'Sora',sans-serif;font-weight:700;margin:0;">📋 Input Absensi</h4>
        <p style="color:var(--ld-slate);font-size:0.85rem;margin:0;">
            {{ $course->name }} · Pertemuan {{ $meeting->meeting_number }} · {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y') }}
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('qr.show', [$course, $meeting]) }}"
           class="btn btn-sm" style="background:#F5F3FF;color:#7C3AED;">📱 QR</a>
        <a href="{{ route('attendances.recap', $course) }}"
           class="btn btn-sm" style="background:var(--ld-sky);color:var(--ld-blue);">📊 Rekap</a>
        <a href="{{ route('meetings.show', [$course, $meeting]) }}"
           class="btn btn-sm btn-secondary">← Kembali</a>
    </div>
</div>

@if($students->isEmpty())
<div class="card">
    <div class="card-body text-center py-5" style="color:var(--ld-slate);">
        <div style="font-size:2.5rem;">👥</div>
        <p class="mt-2">Belum ada mahasiswa.
            <a href="{{ route('students.index', $course) }}">Tambah dulu!</a>
        </p>
    </div>
</div>
@else
<div class="card">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <span style="font-weight:600;">👥 {{ $students->count() }} Mahasiswa</span>
        <span style="font-size:0.75rem;opacity:0.6;">
            {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y') }}
        </span>
    </div>
    <div class="card-body p-0">
        <form action="{{ route('attendances.store', [$course, $meeting]) }}" method="POST" id="absensiForm">
            @csrf

            {{-- Toolbar --}}
            <div class="p-3 d-flex gap-2 flex-wrap align-items-center"
                 style="border-bottom:1px solid var(--ld-border);background:var(--ld-sky);">
                <button type="button" class="btn btn-sm"
                        style="background:#F0FDF4;color:#16A34A;border:1px solid #BBF7D0;"
                        onclick="setAll('hadir')">✅ Semua Hadir</button>
                <button type="button" class="btn btn-sm btn-danger"
                        onclick="setAll('alpha')">❌ Semua Alpha</button>
                <button type="button" class="btn btn-sm btn-secondary"
                        onclick="resetAll()">🔄 Reset</button>
                <div class="ms-auto">
                    <input type="text" id="searchAbsen" class="form-control form-control-sm"
                           placeholder="🔍 Cari nama / NIM..."
                           style="width:200px;">
                </div>
            </div>

            {{-- Tabel --}}
            <div style="max-height:520px;overflow-y:auto;">
                <table class="table table-hover mb-0" id="absenTable">
                    <thead style="position:sticky;top:0;z-index:10;background:var(--ld-sky);">
                        <tr>
                            <th width="40" style="font-size:0.78rem;">#</th>
                            <th style="cursor:pointer;font-size:0.78rem;" onclick="sortTable(1)">NIM ↕</th>
                            <th style="cursor:pointer;font-size:0.78rem;" onclick="sortTable(2)">Nama ↕</th>
                            <th class="text-center" style="font-size:0.78rem;">
                                <span style="color:#16A34A;">✅ Hadir</span>
                            </th>
                            <th class="text-center" style="font-size:0.78rem;">
                                <span style="color:#D97706;">📝 Izin</span>
                            </th>
                            <th class="text-center" style="font-size:0.78rem;">
                                <span style="color:#0891B2;">🏥 Sakit</span>
                            </th>
                            <th class="text-center" style="font-size:0.78rem;">
                                <span style="color:#DC2626;">❌ Alpha</span>
                            </th>
                            <th style="font-size:0.78rem;">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $i => $student)
                        @php
                            $att    = $attendances->get($student->id);
                            $status = $att ? $att->status : 'alpha';
                            $note   = $att ? $att->note : '';
                        @endphp
                        <tr id="row_{{ $student->id }}"
                            style="{{ $status==='hadir'?'background:#F0FDF4':($status==='izin'?'background:#FFFBEB':($status==='sakit'?'background:#ECFEFF':'background:#FEF2F2')) }}">
                            <td style="color:var(--ld-slate);font-size:0.78rem;">{{ $i+1 }}</td>
                            <td style="font-family:'JetBrains Mono',monospace;font-size:0.82rem;">{{ $student->nim }}</td>
                            <td style="font-weight:600;font-size:0.88rem;">{{ $student->name }}</td>
                            <td class="text-center">
                                <input type="radio" name="status_{{ $student->id }}" value="hadir"
                                       class="form-check-input status-radio"
                                       data-student="{{ $student->id }}"
                                       {{ $status==='hadir'?'checked':'' }}
                                       onchange="updateRow({{ $student->id }},'hadir')">
                            </td>
                            <td class="text-center">
                                <input type="radio" name="status_{{ $student->id }}" value="izin"
                                       class="form-check-input status-radio"
                                       data-student="{{ $student->id }}"
                                       {{ $status==='izin'?'checked':'' }}
                                       onchange="updateRow({{ $student->id }},'izin')">
                            </td>
                            <td class="text-center">
                                <input type="radio" name="status_{{ $student->id }}" value="sakit"
                                       class="form-check-input status-radio"
                                       data-student="{{ $student->id }}"
                                       {{ $status==='sakit'?'checked':'' }}
                                       onchange="updateRow({{ $student->id }},'sakit')">
                            </td>
                            <td class="text-center">
                                <input type="radio" name="status_{{ $student->id }}" value="alpha"
                                       class="form-check-input status-radio"
                                       data-student="{{ $student->id }}"
                                       {{ $status==='alpha'?'checked':'' }}
                                       onchange="updateRow({{ $student->id }},'alpha')">
                            </td>
                            <td>
                                <input type="text" name="note_{{ $student->id }}"
                                       class="form-control form-control-sm"
                                       value="{{ $note }}"
                                       placeholder="Catatan..."
                                       style="font-size:0.8rem;">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Submit --}}
            <div class="p-3 d-flex justify-content-between align-items-center"
                 style="border-top:1px solid var(--ld-border);background:var(--ld-sky);">
                <small style="color:var(--ld-slate);">
                    <span style="background:#F0FDF4;color:#16A34A;padding:2px 8px;border-radius:20px;font-weight:600;">H</span> Hadir &nbsp;
                    <span style="background:#FFFBEB;color:#D97706;padding:2px 8px;border-radius:20px;font-weight:600;">I</span> Izin &nbsp;
                    <span style="background:#ECFEFF;color:#0891B2;padding:2px 8px;border-radius:20px;font-weight:600;">S</span> Sakit &nbsp;
                    <span style="background:#FEF2F2;color:#DC2626;padding:2px 8px;border-radius:20px;font-weight:600;">A</span> Alpha
                </small>
                <button type="submit" class="btn btn-primary fw-bold">💾 Simpan Absensi</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
const colors = {
    hadir: '#F0FDF4', izin: '#FFFBEB',
    sakit: '#ECFEFF', alpha: '#FEF2F2'
};

function updateRow(id, status) {
    document.getElementById('row_' + id).style.background = colors[status];
}

function setAll(status) {
    Swal.fire({
        title: 'Set semua ' + (status==='hadir'?'Hadir':'Alpha') + '?',
        icon: 'question', showCancelButton: true,
        confirmButtonText: 'Ya!', cancelButtonText: 'Batal',
        confirmButtonColor: status==='hadir' ? '#16A34A' : '#DC2626'
    }).then(r => {
        if (r.isConfirmed) {
            document.querySelectorAll('.status-radio').forEach(radio => {
                if (radio.value === status) {
                    radio.checked = true;
                    updateRow(radio.dataset.student, status);
                }
            });
        }
    });
}

function resetAll() {
    Swal.fire({
        title: 'Reset semua ke Alpha?', icon: 'warning',
        showCancelButton: true, confirmButtonText: 'Ya!', cancelButtonText: 'Batal'
    }).then(r => {
        if (r.isConfirmed) {
            document.querySelectorAll('.status-radio').forEach(radio => {
                if (radio.value === 'alpha') {
                    radio.checked = true;
                    updateRow(radio.dataset.student, 'alpha');
                }
            });
        }
    });
}

document.getElementById('absensiForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    Swal.fire({
        title: 'Simpan Absensi?', icon: 'question',
        showCancelButton: true, confirmButtonText: 'Ya, Simpan!', cancelButtonText: 'Batal'
    }).then(r => { if (r.isConfirmed) form.submit(); });
});

document.getElementById('searchAbsen').addEventListener('input', function() {
    const kw = this.value.toLowerCase();
    document.querySelectorAll('#absenTable tbody tr').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(kw) ? '' : 'none';
    });
});

const sortDirs = {};
function sortTable(col) {
    const tbody = document.querySelector('#absenTable tbody');
    const rows  = Array.from(tbody.querySelectorAll('tr'));
    const key   = 'col_' + col;
    sortDirs[key] = !sortDirs[key];
    rows.sort((a, b) => {
        const aT = a.cells[col].innerText.trim().toLowerCase();
        const bT = b.cells[col].innerText.trim().toLowerCase();
        return sortDirs[key] ? aT.localeCompare(bT) : bT.localeCompare(aT);
    });
    rows.forEach(r => tbody.appendChild(r));
}
</script>
@endpush