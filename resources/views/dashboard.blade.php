@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 style="font-family:'Sora',sans-serif;font-weight:700;margin:0;">
            Selamat datang, {{ auth()->user()->name }} 👋
        </h4>
        <p style="color:var(--ld-slate);font-size:0.85rem;margin:0;">
            {{ now()->translatedFormat('l, d F Y') }}
        </p>
    </div>
    <a href="{{ route('courses.create') }}" class="btn btn-primary">+ Tambah Kelas</a>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    @php
    $stats = [
        ['icon'=>'📚','value'=>$totalCourses,'label'=>'Kelas','color'=>'#2563EB'],
        ['icon'=>'🎓','value'=>$totalStudents,'label'=>'Mahasiswa','color'=>'#16A34A'],
        ['icon'=>'📅','value'=>$totalMeetings,'label'=>'Pertemuan','color'=>'#0891B2'],
        ['icon'=>'📋','value'=>$totalAssignments,'label'=>'Tugas','color'=>'#D97706'],
        ['icon'=>'📥','value'=>$totalSubmissions,'label'=>'Submission','color'=>'#7C3AED'],
        ['icon'=>'⚠️','value'=>$lowAttendance,'label'=>'Absensi <75%','color'=>'#DC2626'],
    ];
    @endphp
    @foreach($stats as $s)
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card h-100 text-center p-3" style="border-top:3px solid {{ $s['color'] }};">
            <div style="font-size:1.75rem;">{{ $s['icon'] }}</div>
            <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1.6rem;color:{{ $s['color'] }};">{{ $s['value'] }}</div>
            <div style="font-size:0.78rem;color:var(--ld-slate);">{{ $s['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Kalender + Grafik --}}
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span style="font-weight:600;">📅 Kalender Pertemuan</span>
                <a href="{{ route('calendar.index') }}" style="color:rgba(255,255,255,0.8);font-size:0.8rem;text-decoration:none;">Lihat penuh →</a>
            </div>
            <div class="card-body p-2">
                <div id="dashCalendar"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-success text-white" style="font-weight:600;">📈 Status Kehadiran</div>
            <div class="card-body d-flex align-items-center justify-content-center">
                @if(array_sum($attendanceChartData) === 0)
                    <div class="text-center" style="color:var(--ld-slate);">
                        <div style="font-size:2rem;">📋</div>
                        <small>Belum ada data</small>
                    </div>
                @else
                    <canvas id="attChart" style="max-height:200px;"></canvas>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Menu Cepat --}}
<div class="mb-4">
    <h6 style="font-family:'Sora',sans-serif;font-weight:700;margin-bottom:1rem;">⚡ Menu Cepat</h6>
    <div class="row g-2">
        @php
        $menus = [
            ['icon'=>'📚','title'=>'Kelas','route'=>route('courses.index'),'color'=>'#2563EB'],
            ['icon'=>'📊','title'=>'Nilai','route'=>route('grades.index'),'color'=>'#16A34A'],
            ['icon'=>'📅','title'=>'Kalender','route'=>route('calendar.index'),'color'=>'#0891B2'],
            ['icon'=>'🎓','title'=>'Portal Mhs','route'=>route('student.index'),'color'=>'#7C3AED','target'=>'_blank'],
            ['icon'=>'💾','title'=>'Backup','route'=>route('backup.index'),'color'=>'#D97706'],
            ['icon'=>'👤','title'=>'Profil','route'=>route('profile.index'),'color'=>'#64748B'],
        ];
        @endphp
        @foreach($menus as $m)
        <div class="col-4 col-md-2">
            <a href="{{ $m['route'] }}" class="text-decoration-none" {{ isset($m['target']) ? 'target='.$m['target'] : '' }}>
                <div class="card text-center p-3" style="border-top:3px solid {{ $m['color'] }};transition:transform 0.15s;"
                     onmouseover="this.style.transform='translateY(-3px)'"
                     onmouseout="this.style.transform=''">
                    <div style="font-size:1.6rem;">{{ $m['icon'] }}</div>
                    <div style="font-size:0.75rem;font-weight:600;color:{{ $m['color'] }};margin-top:4px;">{{ $m['title'] }}</div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>

{{-- Kelas Saya --}}
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 style="font-family:'Sora',sans-serif;font-weight:700;margin:0;">📚 Kelas Saya</h6>
        <a href="{{ route('courses.index') }}" style="font-size:0.8rem;color:var(--ld-blue);">Lihat semua →</a>
    </div>
    @if($courses->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5" style="color:var(--ld-slate);">
                <div style="font-size:3rem;">📚</div>
                <p class="mt-2 mb-3">Belum ada kelas.</p>
                <a href="{{ route('courses.create') }}" class="btn btn-primary">+ Tambah Kelas</a>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($courses->take(6) as $course)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100" style="transition:transform 0.15s;"
                     onmouseover="this.style.transform='translateY(-2px)'"
                     onmouseout="this.style.transform=''">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 style="font-family:'Sora',sans-serif;font-weight:700;font-size:0.9rem;margin:0;">{{ $course->name }}</h6>
                            <span style="font-size:0.7rem;background:var(--ld-sky);color:var(--ld-blue);padding:2px 8px;border-radius:20px;white-space:nowrap;">
                                {{ $course->meetings_count }} Ptm
                            </span>
                        </div>
                        <p style="font-size:0.78rem;color:var(--ld-slate);margin-bottom:0.75rem;">
                            {{ $course->code }} · {{ $course->class_name }} · {{ $course->semester }}
                        </p>
                        <div style="font-family:'JetBrains Mono',monospace;font-size:1rem;font-weight:500;color:var(--ld-blue);background:var(--ld-sky);padding:6px 12px;border-radius:8px;letter-spacing:3px;display:inline-block;">
                            {{ $course->access_key }}
                        </div>
                    </div>
                    <div class="card-footer" style="background:transparent;border-top:1px solid var(--ld-border);padding:0.75rem 1rem;">
                        <div class="d-flex gap-2">
                            <a href="{{ route('courses.show', $course) }}" class="btn btn-primary btn-sm flex-fill">Detail</a>
                            <a href="{{ route('grades.course', $course) }}" class="btn btn-sm flex-fill" style="background:var(--ld-sky);color:var(--ld-blue);">Nilai</a>
                            <a href="{{ route('attendances.recap', $course) }}" class="btn btn-sm" style="background:#ECFEFF;color:#0891B2;">Absen</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

@if(!empty($gradeChartData))
<div class="card mb-4">
    <div class="card-header bg-primary text-white" style="font-weight:600;">📊 Nilai Rata-rata per Kelas</div>
    <div class="card-body"><canvas id="gradeChart" style="max-height:180px;"></canvas></div>
</div>
@endif

@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new FullCalendar.Calendar(document.getElementById('dashCalendar'), {
        initialView:'dayGridMonth', locale:'id',
        headerToolbar:{ left:'prev,next', center:'title', right:'today' },
        buttonText:{ today:'Hari Ini' },
        events:"{{ route('calendar.events') }}",
        height:320, dayMaxEvents:2,
        eventClick: function(info) { info.jsEvent.preventDefault(); window.location.href="{{ route('calendar.index') }}"; }
    }).render();

    @if(array_sum($attendanceChartData) > 0)
    new Chart(document.getElementById('attChart'), {
        type:'doughnut',
        data:{
            labels:['Hadir','Izin','Sakit','Alpha'],
            datasets:[{ data:[{{ $attendanceChartData['hadir'] }},{{ $attendanceChartData['izin'] }},{{ $attendanceChartData['sakit'] }},{{ $attendanceChartData['alpha'] }}], backgroundColor:['#16A34A','#D97706','#0891B2','#DC2626'], borderWidth:0 }]
        },
        options:{ cutout:'65%', plugins:{ legend:{ position:'bottom', labels:{ font:{ size:11 }, padding:10 } } } }
    });
    @endif

    @if(!empty($gradeChartData))
    new Chart(document.getElementById('gradeChart'), {
        type:'bar',
        data:{
            labels:{!! json_encode(array_column($gradeChartData,'label')) !!},
            datasets:[{ data:{!! json_encode(array_column($gradeChartData,'avg')) !!}, backgroundColor:'#2563EB', borderRadius:6 }]
        },
        options:{ scales:{ y:{ beginAtZero:true, max:100 } }, plugins:{ legend:{ display:false } } }
    });
    @endif
});
</script>
@endpush    