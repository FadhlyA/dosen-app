@extends('layouts.app')
@section('title', 'Rekap Tugas - ' . $course->name)
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h4 style="font-family:'Sora',sans-serif;font-weight:700;margin:0;">📋 Rekap Tugas</h4>
        <p style="color:var(--ld-slate);font-size:0.85rem;margin:0;">
            {{ $course->name }} · {{ $course->class_name }} · {{ $course->semester }}
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('assignments.print-recap', $course) }}" target="_blank" class="btn btn-sm btn-dark">🖨️ Cetak</a>
        <a href="{{ route('assignments.export-excel', $course) }}" class="btn btn-sm btn-success">📊 Excel</a>
        <a href="{{ route('courses.show', $course) }}" class="btn btn-sm btn-secondary">← Kembali</a>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    @php
    $stats = [
        ['label'=>'Total Mahasiswa','value'=>count($recap),'color'=>'#2563EB'],
        ['label'=>'Total Tugas','value'=>$assignments->count(),'color'=>'#16A34A'],
        ['label'=>'Ada Tugas Belum Kumpul','value'=>collect($recap)->where('warning',true)->count(),'color'=>'#DC2626'],
        ['label'=>'Semua Lengkap','value'=>collect($recap)->where('warning',false)->count(),'color'=>'#16A34A'],
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

{{-- Tabel --}}
<div class="card">
    <div class="card-header bg-dark text-white" style="font-weight:600;">📊 Rekap Pengumpulan Tugas</div>
    <div class="card-body p-0">
        @if($assignments->isEmpty())
            <div class="text-center py-4" style="color:var(--ld-slate);"><small>Belum ada tugas.</small></div>
        @elseif(empty($recap))
            <div class="text-center py-4" style="color:var(--ld-slate);"><small>Belum ada mahasiswa.</small></div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="recapTugasTable">
                <thead style="background:var(--ld-sky);">
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        @foreach($assignments as $assignment)
                            <th class="text-center" style="font-size:0.75rem;"
                                title="{{ $assignment->title }}">
                                P{{ $assignment->meeting->meeting_number }}<br>
                                <span style="font-weight:400;color:var(--ld-slate);">
                                    {{ Str::limit($assignment->title, 12) }}
                                </span>
                            </th>
                        @endforeach
                        <th class="text-center">Nilai Rata²</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recap as $studentId => $data)
                    <tr>
                        <td style="font-family:'JetBrains Mono',monospace;font-size:0.82rem;">{{ $data['nim'] }}</td>
                        <td style="font-weight:600;">{{ $data['name'] }}</td>
                        @foreach($assignments as $assignment)
                        <td class="text-center">
                            @if($data['submissions'][$assignment->id])
                                <span style="color:#16A34A;font-weight:700;">✓</span>
                            @else
                                <span style="color:#DC2626;font-weight:700;">✗</span>
                            @endif
                        </td>
                        @endforeach
                        <td class="text-center">
                            @php
                                $avgScore = \App\Models\Submission::where('student_nim', $data['nim'])
                                    ->whereHas('assignment.meeting', function($q) use ($course) {
                                        $q->where('course_id', $course->id);
                                    })
                                    ->whereNotNull('score')
                                    ->avg('score');
                            @endphp
                            <span style="font-weight:600;color:var(--ld-blue);">
                                {{ $avgScore ? round($avgScore, 1) : '-' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span style="font-size:0.78rem;font-weight:600;padding:3px 10px;border-radius:20px;
                                background:{{ $data['total']<$data['total_all']?'#FFFBEB':'#F0FDF4' }};
                                color:{{ $data['total']<$data['total_all']?'#D97706':'#16A34A' }};">
                                {{ $data['total'] }}/{{ $data['total_all'] }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($data['warning'])
                                <span style="font-size:0.72rem;background:#FEF2F2;color:#DC2626;padding:3px 10px;border-radius:20px;font-weight:600;">⚠️ Belum Lengkap</span>
                            @else
                                <span style="font-size:0.72rem;background:#F0FDF4;color:#16A34A;padding:3px 10px;border-radius:20px;font-weight:600;">✅ Lengkap</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3" style="border-top:1px solid var(--ld-border);font-size:0.78rem;color:var(--ld-slate);">
            <strong>Keterangan:</strong>
            <span style="color:#16A34A;font-weight:700;">✓</span> Sudah dikumpulkan &nbsp;
            <span style="color:#DC2626;font-weight:700;">✗</span> Belum dikumpulkan
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#recapTugasTable').DataTable({
        language:{ search:"🔍 Cari:", lengthMenu:"Tampilkan _MENU_ data", info:"_START_-_END_ dari _TOTAL_", paginate:{first:"«",last:"»",next:"›",previous:"‹"} },
        columnDefs:[{ orderable:false, targets:-1 }],
        scrollX:true, pageLength:25,
    });
});
</script>
@endpush