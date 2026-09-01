@extends('layouts.app')
@section('title', 'Kelola Komponen Nilai')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 style="font-family:'Sora',sans-serif;font-weight:700;margin:0;">⚙️ Kelola Komponen Nilai</h4>
        <p style="color:var(--ld-slate);font-size:0.85rem;margin:0;">
            {{ $course->name }} · {{ $course->class_name }} · {{ $course->semester }}
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('grades.grade-letters', $course) }}"
           class="btn btn-sm" style="background:#F5F3FF;color:#7C3AED;">🎓 Nilai Huruf</a>
        <a href="{{ route('grades.course', $course) }}" class="btn btn-sm btn-secondary">← Rekap Nilai</a>
    </div>
</div>

<div class="row g-4">
    {{-- Daftar Komponen --}}
    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <span style="font-weight:600;">📋 Daftar Komponen Nilai</span>
                @php $totalBobot = $components->sum('weight'); @endphp
                <span style="font-size:0.78rem;padding:3px 12px;border-radius:20px;font-weight:600;
                    background:{{ $totalBobot==100?'#F0FDF4':'#FEF2F2' }};
                    color:{{ $totalBobot==100?'#16A34A':'#DC2626' }};">
                    Total: {{ $totalBobot }}%
                </span>
            </div>
            <div class="card-body p-0">
                @if($totalBobot != 100)
                <div class="alert alert-warning m-3 py-2 small mb-0">
                    ⚠️ Total bobot saat ini <strong>{{ $totalBobot }}%</strong> — idealnya harus <strong>100%</strong>
                </div>
                @else
                <div class="alert alert-success m-3 py-2 small mb-0">
                    ✅ Total bobot sudah <strong>100%</strong>
                </div>
                @endif

                @if($components->isEmpty())
                    <div class="text-center py-4" style="color:var(--ld-slate);">
                        <small>Belum ada komponen nilai.</small>
                    </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="componentsTable">
                        <thead style="background:var(--ld-sky);">
                            <tr>
                                <th>Komponen</th>
                                <th>Bobot</th>
                                <th>Tipe</th>
                                <th class="text-center">Sudah Dinilai</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($components as $component)
                            <tr>
                                <td>
                                    <div style="font-weight:600;">{{ $component->name }}</div>
                                    <div class="d-flex gap-1 mt-1 flex-wrap">
                                        @if($component->is_fixed)
                                            <span style="font-size:0.68rem;background:#F1F5F9;color:#475569;padding:2px 8px;border-radius:20px;font-weight:600;">🔒 Tetap</span>
                                        @endif
                                        @if($component->is_attendance)
                                            <span style="font-size:0.68rem;background:#ECFEFF;color:#0891B2;padding:2px 8px;border-radius:20px;font-weight:600;">📅 Absensi</span>
                                        @endif
                                        @if($component->is_assignment_based)
                                            <span style="font-size:0.68rem;background:#FFFBEB;color:#D97706;padding:2px 8px;border-radius:20px;font-weight:600;">📋 Tugas</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($component->is_fixed)
                                        <form action="{{ route('grades.update-component', [$course, $component]) }}"
                                              method="POST" class="d-flex gap-1 align-items-center">
                                            @csrf @method('PUT')
                                            <input type="number" name="weight"
                                                   value="{{ $component->weight }}"
                                                   min="1" max="100"
                                                   class="form-control form-control-sm"
                                                   style="width:60px;">
                                            <span style="color:var(--ld-slate);">%</span>
                                            <button class="btn btn-sm btn-primary">💾</button>
                                        </form>
                                    @else
                                        <span style="font-weight:600;">{{ $component->weight }}%</span>
                                    @endif
                                </td>
                                <td>
                                    @if($component->is_attendance)
                                        <span style="font-size:0.75rem;background:#ECFEFF;color:#0891B2;padding:3px 10px;border-radius:20px;font-weight:600;">Auto Absensi</span>
                                    @elseif($component->is_assignment_based)
                                        <span style="font-size:0.75rem;background:#FFFBEB;color:#D97706;padding:3px 10px;border-radius:20px;font-weight:600;">Auto Tugas</span>
                                    @elseif($component->is_fixed)
                                        <span style="font-size:0.75rem;background:#F1F5F9;color:#475569;padding:3px 10px;border-radius:20px;font-weight:600;">Manual</span>
                                    @else
                                        <span style="font-size:0.75rem;background:var(--ld-sky);color:var(--ld-blue);padding:3px 10px;border-radius:20px;font-weight:600;">Manual</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span style="font-size:0.75rem;background:var(--ld-sky);color:var(--ld-blue);padding:3px 10px;border-radius:20px;font-weight:600;">
                                        {{ $component->grades->count() }}/{{ $students }} mhs
                                    </span>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('grades.destroy-component', [$course, $component]) }}"
                                          method="POST"
                                          onsubmit="return confirmHapusKomponen(event, '{{ addslashes($component->name) }}', {{ $component->is_fixed ? 'true' : 'false' }})">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        {{-- Setting Absensi --}}
        <div class="card mt-3">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <span style="font-weight:600;">📅 Setting Absensi</span>
                <small style="opacity:0.8;font-size:0.75rem;">Berlaku untuk rekap & komponen absensi</small>
            </div>
            <div class="card-body">
                <form action="{{ route('grades.save-attendance-settings', $course) }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label">Formula Kehadiran</label>
                            <select name="attendance_formula" class="form-select">
                                <option value="hadir_only"
                                    {{ $course->attendance_formula === 'hadir_only' ? 'selected' : '' }}>
                                    Formula B: Hadir saja ÷ Total × 100
                                </option>
                                <option value="include_izin_sakit"
                                    {{ $course->attendance_formula === 'include_izin_sakit' ? 'selected' : '' }}>
                                    Formula A: (Hadir+Izin+Sakit) ÷ Total × 100
                                </option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Batas Minimum Kehadiran</label>
                            <div class="input-group">
                                <input type="number" name="attendance_threshold"
                                       class="form-control"
                                       value="{{ $course->attendance_threshold ?? 75 }}"
                                       min="1" max="100" step="0.5" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-info text-white btn-sm mt-3">💾 Simpan Setting</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Tambah Komponen --}}
    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-success text-white" style="font-weight:600;">+ Tambah Komponen</div>
            <div class="card-body">
                <form action="{{ route('grades.store-component', $course) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Komponen</label>
                        <input type="text" name="name" class="form-control"
                               placeholder="contoh: Quiz, Presentasi, UTS" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bobot (%)</label>
                        <div class="input-group">
                            <input type="number" name="weight" class="form-control"
                                   min="1" max="100" placeholder="10" required>
                            <span class="input-group-text">%</span>
                        </div>
                        <small style="color:var(--ld-slate);">
                            Sisa bobot: <strong>{{ 100 - $totalBobot }}%</strong>
                        </small>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   name="is_assignment_based" id="isAssignmentBased" value="1"
                                   onchange="toggleAssignment()">
                            <label class="form-check-label" for="isAssignmentBased" style="font-weight:600;">
                                📋 Hubungkan ke Nilai Tugas
                            </label>
                        </div>
                        <small style="color:var(--ld-slate);">Nilai dihitung otomatis dari rata-rata nilai tugas mahasiswa</small>
                    </div>

                    <button type="submit" class="btn btn-success w-100 fw-bold">+ Tambah Komponen</button>
                </form>

                <hr style="border-color:var(--ld-border);">

                {{-- Contoh Pembagian --}}
                <p style="font-size:0.78rem;color:var(--ld-slate);margin-bottom:8px;">Contoh pembagian bobot:</p>
                <div class="table-responsive">
                    <table class="table table-sm mb-0" style="font-size:0.78rem;">
                        <tbody>
                            <tr><td>Tugas</td><td style="color:var(--ld-blue);font-weight:600;">10%</td></tr>
                            <tr><td>UTS</td><td style="color:var(--ld-blue);font-weight:600;">30%</td></tr>
                            <tr><td>UAS</td><td style="color:var(--ld-blue);font-weight:600;">40%</td></tr>
                            <tr><td>Absensi</td><td style="color:var(--ld-blue);font-weight:600;">10%</td></tr>
                            <tr style="border-top:2px solid var(--ld-border);">
                                <td style="font-weight:700;">Total</td>
                                <td style="color:#16A34A;font-weight:700;">100%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleAssignment() {
    const cb = document.getElementById('isAssignmentBased');
    const attCb = document.getElementById('isAttendance');
    if (attCb && cb.checked) attCb.checked = false;
}

function confirmHapusKomponen(e, name, isFixed) {
    e.preventDefault();
    const form = e.target.closest('form');
    Swal.fire({
        title: 'Hapus Komponen?',
        text: name + ' dan semua nilainya akan terhapus!' + (isFixed ? ' ⚠️ Ini komponen tetap!' : ''),
        icon: isFixed ? 'error' : 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626', cancelButtonColor: '#64748B',
        confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal'
    }).then(r => { if (r.isConfirmed) form.submit(); });
}
</script>
@endpush