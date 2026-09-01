@extends('layouts.app')
@section('title', 'Rekap Absensi - ' . $course->name)
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h4 style="font-family:'Sora',sans-serif;font-weight:700;margin:0;">📊 Rekap Absensi</h4>
        <p style="color:var(--ld-slate);font-size:0.85rem;margin:0;">{{ $course->name }} · {{ $course->class_name }} · {{ $course->semester }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('attendances.print-recap', $course) }}" target="_blank" class="btn btn-sm btn-dark">🖨️ Cetak</a>
        <a href="{{ route('attendances.export-excel', $course) }}" class="btn btn-sm btn-success">📊 Excel</a>
        <a href="{{ route('attendances.export', $course) }}" class="btn btn-sm" style="background:#F0FDF4;color:#16A34A;">⬇️ CSV</a>
        <a href="{{ route('courses.show', $course) }}" class="btn btn-sm btn-secondary">← Kembali</a>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    @php
    $stats = [
        ['label'=>'Total Pertemuan','value'=>$totalMeetings,'color'=>'#2563EB'],
        ['label'=>'Total Mahasiswa','value'=>$students->count(),'color'=>'#16A34A'],
        ['label'=>'Kehadiran < '.$course->attendance_threshold.'%','value'=>collect($recap)->where('warning',true)->count(),'color'=>'#DC2626'],
        ['label'=>'Batas Kehadiran','value'=>$course->attendance_threshold.'%','color'=>'#D97706'],
    ];
    @endphp
    @foreach($stats as $s)
    <div class="col-6 col-md-3">
        <div class="card text-center p-3" style="border-top:3px solid {{ $s['color'] }};">
            <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1.6rem;color:{{ $s['color'] }};">{{ $s['value'] }}</div>
            <div style="font-size:0.78rem;color:var(--ld-slate);">{{ $s['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Tabel Rekap --}}
<div class="card mb-4">
    <div class="card-header bg-dark text-white" style="font-weight:600;">📋 Rekap Kehadiran Mahasiswa</div>
    <div class="card-body p-0">
        @if($students->isEmpty() || $totalMeetings === 0)
            <div class="text-center py-4" style="color:var(--ld-slate);"><small>Belum ada data.</small></div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="recapTable">
                <thead style="background:var(--ld-sky);">
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        @foreach($meetings as $m)
                            <th class="text-center" style="font-size:0.75rem;" title="{{ $m->title }}">P{{ $m->meeting_number }}</th>
                        @endforeach
                        <th class="text-center">H</th>
                        <th class="text-center">I</th>
                        <th class="text-center">S</th>
                        <th class="text-center">A</th>
                        <th class="text-center">%</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recap as $data)
                    <tr>
                        <td style="font-family:'JetBrains Mono',monospace;font-size:0.82rem;">{{ $data['nim'] }}</td>
                        <td style="font-weight:600;">{{ $data['name'] }}</td>
                        @foreach($meetings as $m)
                        @php $s = $data['detail'][$m->id] ?? null; @endphp
                        <td class="text-center">
                            @if($s === 'hadir')
                                <span style="font-size:0.7rem;background:#F0FDF4;color:#16A34A;padding:2px 6px;border-radius:20px;font-weight:700;">H</span>
                            @elseif($s === 'izin')
                                <span style="font-size:0.7rem;background:#FFFBEB;color:#D97706;padding:2px 6px;border-radius:20px;font-weight:700;">I</span>
                            @elseif($s === 'sakit')
                                <span style="font-size:0.7rem;background:#ECFEFF;color:#0891B2;padding:2px 6px;border-radius:20px;font-weight:700;">S</span>
                            @elseif($s === 'alpha')
                                <span style="font-size:0.7rem;background:#FEF2F2;color:#DC2626;padding:2px 6px;border-radius:20px;font-weight:700;">A</span>
                            @else
                                <span style="color:var(--ld-slate);font-size:0.75rem;">-</span>
                            @endif
                        </td>
                        @endforeach
                        <td class="text-center" style="font-weight:600;color:#16A34A;">{{ $data['hadir'] }}</td>
                        <td class="text-center" style="font-weight:600;color:#D97706;">{{ $data['izin'] }}</td>
                        <td class="text-center" style="font-weight:600;color:#0891B2;">{{ $data['sakit'] }}</td>
                        <td class="text-center" style="font-weight:600;color:#DC2626;">{{ $data['alpha'] }}</td>
                        <td class="text-center">
                            <span style="font-size:0.78rem;font-weight:700;padding:3px 10px;border-radius:20px;
                                background:{{ $data['warning']?'#FEF2F2':'#F0FDF4' }};
                                color:{{ $data['warning']?'#DC2626':'#16A34A' }};">
                                {{ $data['percentage'] }}%
                            </span>
                        </td>
                        <td class="text-center">
                            @if($data['warning'])
                                <span style="font-size:0.72rem;background:#FEF2F2;color:#DC2626;padding:3px 10px;border-radius:20px;font-weight:600;">⚠️ Kurang</span>
                            @else
                                <span style="font-size:0.72rem;background:#F0FDF4;color:#16A34A;padding:3px 10px;border-radius:20px;font-weight:600;">✅ Memenuhi</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3" style="border-top:1px solid var(--ld-border);font-size:0.78rem;color:var(--ld-slate);">
            <strong>Keterangan:</strong>
            <span style="background:#F0FDF4;color:#16A34A;padding:2px 8px;border-radius:20px;font-weight:700;margin:0 4px;">H</span> Hadir
            <span style="background:#FFFBEB;color:#D97706;padding:2px 8px;border-radius:20px;font-weight:700;margin:0 4px;">I</span> Izin
            <span style="background:#ECFEFF;color:#0891B2;padding:2px 8px;border-radius:20px;font-weight:700;margin:0 4px;">S</span> Sakit
            <span style="background:#FEF2F2;color:#DC2626;padding:2px 8px;border-radius:20px;font-weight:700;margin:0 4px;">A</span> Alpha
            — Formula: <strong>{{ $course->attendance_formula === 'include_izin_sakit' ? 'A (H+I+S)' : 'B (H saja)' }}</strong>
        </div>
        @endif
    </div>
</div>

{{-- Input Absensi per Pertemuan --}}
<div class="card">
    <div class="card-header bg-primary text-white" style="font-weight:600;">📅 Input Absensi per Pertemuan</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
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
                    @forelse($meetings as $meeting)
                    <tr>
                        <td style="font-weight:700;color:var(--ld-blue);">{{ $meeting->meeting_number }}</td>
                        <td>{{ $meeting->title }}</td>
                        <td style="color:var(--ld-slate);font-size:0.85rem;">{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y') }}</td>
                        <td>
                            @php
                                $h = $meeting->attendances->where('status','hadir')->count();
                                $i = $meeting->attendances->where('status','izin')->count();
                                $s = $meeting->attendances->where('status','sakit')->count();
                                $a = $meeting->attendances->where('status','alpha')->count();
                                $total = $meeting->attendances->count();
                            @endphp
                            @if($total > 0)
                                <div style="font-size:0.72rem;display:flex;gap:4px;flex-wrap:wrap;">
                                    <span style="background:#F0FDF4;color:#16A34A;padding:2px 8px;border-radius:20px;font-weight:600;">H:{{ $h }}</span>
                                    <span style="background:#FFFBEB;color:#D97706;padding:2px 8px;border-radius:20px;font-weight:600;">I:{{ $i }}</span>
                                    <span style="background:#ECFEFF;color:#0891B2;padding:2px 8px;border-radius:20px;font-weight:600;">S:{{ $s }}</span>
                                    <span style="background:#FEF2F2;color:#DC2626;padding:2px 8px;border-radius:20px;font-weight:600;">A:{{ $a }}</span>
                                </div>
                            @else
                                <span style="font-size:0.75rem;background:#FFFBEB;color:#D97706;padding:3px 10px;border-radius:20px;font-weight:600;">⏳ Belum diisi</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('attendances.index', [$course, $meeting]) }}" class="btn btn-sm btn-primary">📋 Input</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-3" style="color:var(--ld-slate);">Belum ada pertemuan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#recapTable').DataTable({
        language:{ search:"🔍 Cari:", lengthMenu:"Tampilkan _MENU_ data", info:"_START_-_END_ dari _TOTAL_", paginate:{first:"«",last:"»",next:"›",previous:"‹"} },
        columnDefs:[{ orderable:false, targets:-1 }],
        scrollX:true, pageLength:25,
    });
});
</script>
@endpush