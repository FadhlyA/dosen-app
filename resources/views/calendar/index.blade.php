@extends('layouts.app')
@section('title', 'Kalender Pertemuan')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 style="font-family:'Sora',sans-serif;font-weight:700;margin:0;">📅 Kalender Pertemuan</h4>
        <p style="color:var(--ld-slate);font-size:0.85rem;margin:0;">Semua pertemuan & deadline tugas</p>
    </div>
</div>

{{-- Legend --}}
<div class="card mb-3">
    <div class="card-body py-2 d-flex gap-3 flex-wrap align-items-center">
        <small style="font-weight:600;color:var(--ld-slate);">Keterangan:</small>
        <span style="font-size:0.78rem;"><span style="background:#2563EB;color:#fff;padding:2px 10px;border-radius:20px;">■</span> Pertemuan</span>
        <span style="font-size:0.78rem;"><span style="background:#6B7280;color:#fff;padding:2px 10px;border-radius:20px;">■</span> Selesai</span>
        <span style="font-size:0.78rem;"><span style="background:#DC2626;color:#fff;padding:2px 10px;border-radius:20px;">■</span> Deadline Tugas</span>
        @foreach($courses as $i => $course)
        <span style="font-size:0.78rem;">
            <span style="background:{{ ['#2563EB','#16A34A','#DC2626','#D97706','#0891B2','#7C3AED'][$i%6] }};color:#fff;padding:2px 10px;border-radius:20px;">■</span>
            {{ Str::limit($course->name, 20) }}
        </span>
        @endforeach
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div id="calendar"></div>
    </div>
</div>

{{-- Modal Detail --}}
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" id="eventModalHeader">
                <h5 class="modal-title fw-bold" id="eventModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table style="width:100%;font-size:0.85rem;border-collapse:collapse;">
                    <tr><td style="color:var(--ld-slate);padding:4px 0;width:100px;">Mata Kuliah</td><td id="eventCourse" style="font-weight:600;"></td></tr>
                    <tr><td style="color:var(--ld-slate);padding:4px 0;">Kelas</td><td id="eventClass"></td></tr>
                    <tr><td style="color:var(--ld-slate);padding:4px 0;">Tanggal</td><td id="eventDate"></td></tr>
                    <tr><td style="color:var(--ld-slate);padding:4px 0;">Status</td><td id="eventStatus"></td></tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="eventLink" class="btn btn-primary btn-sm">Buka Detail →</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView:'dayGridMonth', locale:'id',
        headerToolbar:{ left:'prev,next today', center:'title', right:'dayGridMonth,timeGridWeek,listMonth' },
        buttonText:{ today:'Hari Ini', month:'Bulan', week:'Minggu', list:'Daftar' },
        events:"{{ route('calendar.events') }}",
        height:'auto', dayMaxEvents:3,
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            const p = info.event.extendedProps;
            const header = document.getElementById('eventModalHeader');
            header.style.background = info.event.backgroundColor;
            header.style.color = '#fff';
            document.getElementById('eventModalTitle').innerText = info.event.title;
            document.getElementById('eventCourse').innerText = p.course ?? '-';
            document.getElementById('eventClass').innerText = p.class_name ?? '-';
            document.getElementById('eventDate').innerText = info.event.startStr;
            const status = p.status;
            document.getElementById('eventStatus').innerHTML =
                status==='done' ? '<span style="background:#F0FDF4;color:#16A34A;padding:2px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;">✅ Selesai</span>'
                : status==='deadline' ? '<span style="background:#FEF2F2;color:#DC2626;padding:2px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;">⏰ Deadline</span>'
                : '<span style="background:#EFF6FF;color:#2563EB;padding:2px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;">📅 Upcoming</span>';
            document.getElementById('eventLink').href = p.url ?? '#';
            new bootstrap.Modal(document.getElementById('eventModal')).show();
        }
    }).render();
});
</script>
@endpush