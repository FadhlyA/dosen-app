<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $course->name }} — LectraDesk</title>
<link rel="icon" type="image/png" href="{{ asset('images/lectradesk/logo-icon-blue.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<style>
:root{--ink:#0F172A;--blue:#2563EB;--blue-deep:#1D4ED8;--sky:#EFF6FF;--slate:#64748B;--paper:#FAFBFF;--border:rgba(15,23,42,0.08);}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Inter',sans-serif;background:var(--paper);color:var(--ink);min-height:100vh;}
nav{background:linear-gradient(135deg,var(--ink),#1E3A5F);padding:14px 0;position:sticky;top:0;z-index:50;}
.nav-inner{max-width:960px;margin:0 auto;padding:0 20px;display:flex;justify-content:space-between;align-items:center;}
.brand{display:flex;align-items:center;gap:8px;font-family:'Sora',sans-serif;font-weight:700;font-size:18px;color:#fff;text-decoration:none;}
.brand .accent{color:#60A5FA;}
.nav-right{display:flex;align-items:center;gap:16px;}
.student-info{color:rgba(255,255,255,0.7);font-size:13px;}
.student-nim{font-family:'JetBrains Mono',monospace;color:#93C5FD;}
.btn-logout{padding:6px 14px;border-radius:8px;border:1px solid rgba(255,255,255,0.2);color:#fff;background:transparent;font-size:13px;text-decoration:none;transition:background 0.15s;}
.btn-logout:hover{background:rgba(255,255,255,0.1);color:#fff;}
.wrap{max-width:960px;margin:0 auto;padding:32px 20px;}
.course-header{background:#fff;border:1px solid var(--border);border-radius:16px;padding:28px;margin-bottom:24px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;}
.course-title{font-family:'Sora',sans-serif;font-weight:700;font-size:22px;margin-bottom:4px;}
.course-meta{color:var(--slate);font-size:14px;}
.btn-absen{padding:10px 20px;border-radius:10px;background:linear-gradient(135deg,var(--blue),var(--blue-deep));color:#fff;text-decoration:none;font-weight:600;font-size:14px;transition:transform 0.15s;display:inline-flex;align-items:center;gap:6px;}
.btn-absen:hover{transform:translateY(-1px);color:#fff;}
.section-title{font-family:'Sora',sans-serif;font-weight:700;font-size:16px;margin-bottom:12px;color:var(--ink);}
.meeting-card{background:#fff;border:1px solid var(--border);border-radius:12px;padding:18px 20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:10px;transition:box-shadow 0.15s;}
.meeting-card:hover{box-shadow:0 4px 16px rgba(15,23,42,0.08);}
.meeting-num{font-family:'JetBrains Mono',monospace;font-size:0.75rem;font-weight:600;color:var(--blue);background:var(--sky);padding:3px 10px;border-radius:20px;margin-bottom:4px;display:inline-block;}
.meeting-title{font-weight:600;font-size:15px;margin-bottom:2px;}
.meeting-date{font-size:13px;color:var(--slate);}
.meeting-right{display:flex;align-items:center;gap:10px;}
.status-badge{font-size:0.75rem;padding:4px 12px;border-radius:20px;font-weight:600;}
.status-done{background:#F0FDF4;color:#16A34A;}
.status-upcoming{background:var(--sky);color:var(--blue);}
.btn-detail{padding:8px 18px;border-radius:8px;background:var(--blue);color:#fff;text-decoration:none;font-size:13px;font-weight:600;transition:background 0.15s;}
.btn-detail:hover{background:var(--blue-deep);color:#fff;}
.empty-state{background:#fff;border:1px solid var(--border);border-radius:12px;padding:48px;text-align:center;color:var(--slate);}
.empty-icon{font-size:2.5rem;margin-bottom:8px;}
footer{text-align:center;padding:32px 20px;color:var(--slate);font-size:13px;border-top:1px solid var(--border);margin-top:40px;}
@media(max-width:640px){.course-header{flex-direction:column;align-items:flex-start;}.nav-right .student-info{display:none;}}
</style>
</head>
<body>

<nav>
    <div class="nav-inner">
        <a href="{{ route('student.index') }}" class="brand">
            <svg width="28" height="28" viewBox="0 0 100 100" fill="none">
                <rect x="8" y="16" width="84" height="76" rx="18" fill="#2563EB"/>
                <rect x="24" y="6" width="8" height="18" rx="4" fill="#60A5FA"/>
                <rect x="68" y="6" width="8" height="18" rx="4" fill="#60A5FA"/>
                <rect x="16" y="30" width="68" height="50" rx="8" fill="#fff"/>
                <path d="M50 30 V80" stroke="#DBEAFE" stroke-width="2"/>
                <rect x="24" y="40" width="10" height="10" rx="2" fill="#93C5FD"/>
                <rect x="38" y="40" width="10" height="10" rx="2" fill="#93C5FD"/>
                <rect x="24" y="56" width="10" height="10" rx="2" fill="#93C5FD"/>
                <rect x="38" y="56" width="10" height="10" rx="2" fill="#93C5FD"/>
                <path d="M56 44 h18 M60 58 h14 M56 44 l3 3 l5 -6" stroke="#2563EB" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Lectra<span class="accent">Desk</span>
        </a>
        <div class="nav-right">
            <span class="student-info">
                🎓 {{ session('student_name') }} &nbsp;·&nbsp;
                <span class="student-nim">{{ session('student_nim') }}</span>
            </span>
            <a href="{{ route('student.index') }}" class="btn-logout">Keluar</a>
        </div>
    </div>
</nav>

<div class="wrap">
    @if(session('success'))
        <div style="background:#F0FDF4;border:1px solid #BBF7D0;border-radius:10px;padding:12px 16px;margin-bottom:20px;color:#15803D;font-size:14px;">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div class="course-header">
        <div>
            <div class="course-title">{{ $course->name }}</div>
            <div class="course-meta">{{ $course->code }} · {{ $course->class_name }} · {{ $course->semester }}</div>
            @if($course->description)
                <p style="font-size:14px;color:var(--slate);margin-top:8px;">{{ $course->description }}</p>
            @endif
        </div>
        <a href="{{ route('student.attendance', $course) }}" class="btn-absen">📊 Absensi Saya</a>
    </div>

    <div class="section-title">📅 Daftar Pertemuan</div>

    @if($meetings->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">📅</div>
            <p>Belum ada pertemuan tersedia.</p>
        </div>
    @else
        @foreach($meetings as $meeting)
        <div class="meeting-card">
            <div>
                <div class="meeting-num">Pertemuan {{ $meeting->meeting_number }}</div>
                <div class="meeting-title">{{ $meeting->title }}</div>
                <div class="meeting-date">📅 {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y') }}</div>
            </div>
            <div class="meeting-right">
                @if($meeting->status === 'done')
                    <span class="status-badge status-done">✅ Selesai</span>
                @else
                    <span class="status-badge status-upcoming">📅 Upcoming</span>
                @endif
                <a href="{{ route('student.meeting', [$course, $meeting]) }}" class="btn-detail">Lihat →</a>
            </div>
        </div>
        @endforeach
    @endif
</div>

<footer>LectraDesk · Teach Better. Manage Smarter. &copy; {{ date('Y') }}</footer>
</body>
</html>