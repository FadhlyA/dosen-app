<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Absensi Saya — LectraDesk</title>
<link rel="icon" type="image/png" href="{{ asset('images/lectradesk/logo-icon-blue.png') }}">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<style>
:root{--ink:#0F172A;--blue:#2563EB;--blue-deep:#1D4ED8;--sky:#EFF6FF;--slate:#64748B;--paper:#FAFBFF;--border:rgba(15,23,42,0.08);}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Inter',sans-serif;background:var(--paper);color:var(--ink);min-height:100vh;}
nav{background:linear-gradient(135deg,var(--ink),#1E3A5F);padding:14px 0;position:sticky;top:0;z-index:50;}
.nav-inner{max-width:960px;margin:0 auto;padding:0 20px;display:flex;justify-content:space-between;align-items:center;}
.brand{display:flex;align-items:center;gap:8px;font-family:'Sora',sans-serif;font-weight:700;font-size:18px;color:#fff;text-decoration:none;}
.brand .accent{color:#60A5FA;}
.btn-back{padding:6px 14px;border-radius:8px;border:1px solid rgba(255,255,255,0.2);color:#fff;background:transparent;font-size:13px;text-decoration:none;}
.btn-back:hover{background:rgba(255,255,255,0.1);color:#fff;}
.wrap{max-width:960px;margin:0 auto;padding:28px 20px;}

/* Student Info */
.student-card{background:#fff;border:1px solid var(--border);border-radius:16px;padding:20px 24px;margin-bottom:20px;display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:16px;}
.info-item small{display:block;color:var(--slate);font-size:12px;margin-bottom:2px;}
.info-item div{font-weight:600;font-size:14px;}

/* Stats */
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;}
.stat{background:#fff;border:1px solid var(--border);border-radius:12px;padding:16px;text-align:center;}
.stat .val{font-family:'Sora',sans-serif;font-weight:800;font-size:1.8rem;line-height:1;}
.stat .lbl{font-size:12px;color:var(--slate);margin-top:4px;}

/* Percentage Card */
.pct-card{background:#fff;border:1px solid var(--border);border-radius:16px;padding:24px;margin-bottom:20px;}
.pct-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;}
.pct-title{font-family:'Sora',sans-serif;font-weight:700;font-size:15px;}
.pct-badge{font-size:0.9rem;font-weight:700;padding:5px 16px;border-radius:20px;}
.progress-bar-wrap{height:12px;background:var(--sky);border-radius:20px;overflow:hidden;margin-bottom:10px;}
.progress-bar-fill{height:100%;border-radius:20px;transition:width 0.5s ease;}
.pct-status{font-size:13px;font-weight:600;padding:10px 14px;border-radius:10px;display:flex;align-items:center;gap:8px;}

/* Table */
.card{background:#fff;border:1px solid var(--border);border-radius:16px;overflow:hidden;}
.card-header{background:var(--ink);color:#fff;padding:14px 20px;font-family:'Sora',sans-serif;font-weight:600;font-size:14px;display:flex;justify-content:space-between;align-items:center;}
table{width:100%;border-collapse:collapse;font-size:14px;}
thead th{background:var(--sky);padding:10px 16px;text-align:left;font-weight:600;font-size:13px;color:var(--ink);}
tbody td{padding:12px 16px;border-bottom:1px solid var(--border);}
tbody tr:last-child td{border-bottom:none;}
tbody tr:hover{background:#FAFBFF;}

footer{text-align:center;padding:28px 20px;color:var(--slate);font-size:13px;border-top:1px solid var(--border);margin-top:32px;}
@media(max-width:640px){.stats{grid-template-columns:repeat(2,1fr);}}
</style>
</head>
<body>

<nav>
    <div class="nav-inner">
        <a href="{{ route('student.course', $course) }}" class="brand">
            <svg width="26" height="26" viewBox="0 0 100 100" fill="none">
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
        <a href="{{ route('student.course', $course) }}" class="btn-back">← Kembali</a>
    </div>
</nav>

<div class="wrap">

    {{-- Student Info --}}
    <div class="student-card">
        <div class="info-item"><small>Nama</small><div>{{ $student->name }}</div></div>
        <div class="info-item"><small>NIM</small><div style="font-family:'JetBrains Mono',monospace;">{{ $student->nim }}</div></div>
        <div class="info-item"><small>Mata Kuliah</small><div>{{ $course->name }}</div></div>
        <div class="info-item"><small>Kelas</small><div>{{ $course->class_name }}</div></div>
    </div>

    {{-- Stats --}}
    <div class="stats">
        <div class="stat">
            <div class="val" style="color:#16A34A;">{{ $hadir }}</div>
            <div class="lbl">✅ Hadir</div>
        </div>
        <div class="stat">
            <div class="val" style="color:#D97706;">{{ $izin }}</div>
            <div class="lbl">📝 Izin</div>
        </div>
        <div class="stat">
            <div class="val" style="color:#0891B2;">{{ $sakit }}</div>
            <div class="lbl">🏥 Sakit</div>
        </div>
        <div class="stat">
            <div class="val" style="color:#DC2626;">{{ $alpha }}</div>
            <div class="lbl">❌ Alpha</div>
        </div>
    </div>

    {{-- Persentase --}}
    <div class="pct-card">
        <div class="pct-header">
            <div class="pct-title">📊 Persentase Kehadiran</div>
            <span class="pct-badge" style="background:{{ $percentage >= $threshold ? '#F0FDF4' : '#FEF2F2' }};color:{{ $percentage >= $threshold ? '#16A34A' : '#DC2626' }};">
                {{ $percentage }}%
            </span>
        </div>
        <div class="progress-bar-wrap">
            <div class="progress-bar-fill"
                 style="width:{{ $percentage }}%;background:{{ $percentage >= $threshold ? '#16A34A' : '#DC2626' }};"></div>
        </div>
        @if($percentage >= $threshold)
            <div class="pct-status" style="background:#F0FDF4;color:#16A34A;">
                ✅ Kehadiran Anda memenuhi syarat minimum {{ $threshold }}%
            </div>
        @else
            <div class="pct-status" style="background:#FEF2F2;color:#DC2626;">
                ⚠️ Kehadiran Anda di bawah syarat minimum <strong>{{ $threshold }}%</strong> — perlu ditingkatkan!
            </div>
        @endif
    </div>

    {{-- Detail per Pertemuan --}}
    <div class="card">
        <div class="card-header">
            <span>📋 Detail Kehadiran Per Pertemuan</span>
            <span style="font-size:0.75rem;opacity:0.7;">{{ $totalMeetings }} Pertemuan</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Ke-</th>
                    <th>Judul</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($meetings as $meeting)
                @php $d = $detail[$meeting->id] ?? ['status'=>null,'note'=>null]; @endphp
                <tr>
                    <td style="font-family:'JetBrains Mono',monospace;font-weight:600;color:var(--blue);">{{ $meeting->meeting_number }}</td>
                    <td style="font-weight:500;">{{ $meeting->title }}</td>
                    <td style="color:var(--slate);font-size:13px;">{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y') }}</td>
                    <td>
                        @if($d['status'] === 'hadir')
                            <span style="font-size:0.72rem;background:#F0FDF4;color:#16A34A;padding:3px 10px;border-radius:20px;font-weight:600;">✅ Hadir</span>
                        @elseif($d['status'] === 'izin')
                            <span style="font-size:0.72rem;background:#FFFBEB;color:#D97706;padding:3px 10px;border-radius:20px;font-weight:600;">📝 Izin</span>
                        @elseif($d['status'] === 'sakit')
                            <span style="font-size:0.72rem;background:#ECFEFF;color:#0891B2;padding:3px 10px;border-radius:20px;font-weight:600;">🏥 Sakit</span>
                        @elseif($d['status'] === 'alpha')
                            <span style="font-size:0.72rem;background:#FEF2F2;color:#DC2626;padding:3px 10px;border-radius:20px;font-weight:600;">❌ Alpha</span>
                        @else
                            <span style="font-size:0.72rem;color:var(--slate);">— Belum diisi</span>
                        @endif
                    </td>
                    <td style="font-size:13px;color:var(--slate);">{{ $d['note'] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<footer>LectraDesk · Teach Better. Manage Smarter. &copy; {{ date('Y') }}</footer>

</body>
</html>