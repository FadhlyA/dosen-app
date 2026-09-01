<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pertemuan {{ $meeting->meeting_number }} — LectraDesk</title>
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
.nav-right{display:flex;align-items:center;gap:12px;}
.student-info{color:rgba(255,255,255,0.7);font-size:13px;}
.nim{font-family:'JetBrains Mono',monospace;color:#93C5FD;}
.btn-back{padding:6px 14px;border-radius:8px;border:1px solid rgba(255,255,255,0.2);color:#fff;background:transparent;font-size:13px;text-decoration:none;transition:background 0.15s;}
.btn-back:hover{background:rgba(255,255,255,0.1);color:#fff;}
.wrap{max-width:960px;margin:0 auto;padding:28px 20px;}

/* Meeting Header */
.meeting-header{background:#fff;border:1px solid var(--border);border-radius:16px;padding:24px;margin-bottom:20px;}
.meeting-num{font-family:'JetBrains Mono',monospace;font-size:0.75rem;font-weight:600;color:var(--blue);background:var(--sky);padding:3px 12px;border-radius:20px;display:inline-block;margin-bottom:8px;}
.meeting-title{font-family:'Sora',sans-serif;font-weight:700;font-size:20px;margin-bottom:4px;}
.meeting-date{font-size:13px;color:var(--slate);}
.status-done{font-size:0.75rem;background:#F0FDF4;color:#16A34A;padding:3px 12px;border-radius:20px;font-weight:600;}
.status-up{font-size:0.75rem;background:var(--sky);color:var(--blue);padding:3px 12px;border-radius:20px;font-weight:600;}

/* QR Absensi Button */
.qr-section{background:#fff;border:1px solid var(--border);border-radius:12px;padding:20px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;}
.qr-info h6{font-family:'Sora',sans-serif;font-weight:700;font-size:14px;margin-bottom:4px;}
.qr-info p{font-size:13px;color:var(--slate);margin:0;}
.btn-qr{padding:10px 20px;border-radius:10px;background:linear-gradient(135deg,#7C3AED,#6D28D9);color:#fff;border:none;font-family:'Sora',sans-serif;font-weight:600;font-size:14px;cursor:pointer;display:flex;align-items:center;gap:6px;transition:transform 0.15s;}
.btn-qr:hover{transform:translateY(-1px);}

/* Section */
.section-title{font-family:'Sora',sans-serif;font-weight:700;font-size:15px;margin-bottom:12px;color:var(--ink);display:flex;align-items:center;gap:8px;}
.card{background:#fff;border:1px solid var(--border);border-radius:12px;margin-bottom:16px;overflow:hidden;}
.card-empty{padding:32px;text-align:center;color:var(--slate);font-size:14px;}

/* Material Item */
.material-item{display:flex;align-items:center;gap:14px;padding:16px 20px;border-bottom:1px solid var(--border);}
.material-item:last-child{border-bottom:none;}
.material-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex:none;}
.material-icon.file{background:#EFF6FF;}
.material-icon.link{background:#F0FDF4;}
.material-info{flex:1;min-width:0;}
.material-title{font-weight:600;font-size:14px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.material-type{font-size:12px;color:var(--slate);}
.btn-material{padding:7px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:transform 0.15s;border:none;cursor:pointer;}
.btn-material:hover{transform:translateY(-1px);}
.btn-download{background:var(--blue);color:#fff;}
.btn-link{background:#F0FDF4;color:#16A34A;}

/* Assignment Item */
.assignment-item{padding:20px;border-bottom:1px solid var(--border);}
.assignment-item:last-child{border-bottom:none;}
.assignment-title{font-family:'Sora',sans-serif;font-weight:700;font-size:15px;margin-bottom:6px;}
.assignment-desc{font-size:13px;color:var(--slate);margin-bottom:8px;line-height:1.6;}
.deadline{font-size:12px;font-weight:600;color:#DC2626;}
.btn-submit{display:inline-flex;align-items:center;gap:6px;margin-top:12px;padding:9px 18px;border-radius:8px;background:linear-gradient(135deg,var(--blue),var(--blue-deep));color:#fff;font-weight:600;font-size:13px;text-decoration:none;transition:transform 0.15s;}
.btn-submit:hover{transform:translateY(-1px);color:#fff;}
.submitted-badge{display:inline-flex;align-items:center;gap:6px;margin-top:12px;padding:9px 16px;border-radius:8px;background:#F0FDF4;color:#16A34A;font-weight:600;font-size:13px;}

footer{text-align:center;padding:28px 20px;color:var(--slate);font-size:13px;border-top:1px solid var(--border);margin-top:32px;}
@media(max-width:640px){.nav-right .student-info{display:none;}}
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
        <div class="nav-right">
            <span class="student-info">🎓 {{ session('student_name') }} · <span class="nim">{{ session('student_nim') }}</span></span>
            <a href="{{ route('student.course', $course) }}" class="btn-back">← Kembali</a>
        </div>
    </div>
</nav>

<div class="wrap">

    {{-- Meeting Header --}}
    <div class="meeting-header">
        <div class="meeting-num">Pertemuan {{ $meeting->meeting_number }}</div>
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2" style="margin-top:6px;">
            <div>
                <div class="meeting-title">{{ $meeting->title }}</div>
                <div class="meeting-date">📅 {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d F Y') }}</div>
            </div>
            @if($meeting->status === 'done')
                <span class="status-done">✅ Selesai</span>
            @else
                <span class="status-up">📅 Upcoming</span>
            @endif
        </div>
    </div>

    {{-- QR Absensi --}}
    <div class="qr-section">
        <div class="qr-info">
            <h6>📱 Absensi via QR Code</h6>
            <p>Scan QR Code yang ditampilkan dosen untuk mencatat kehadiran Anda</p>
        </div>
        <button class="btn-qr" data-bs-toggle="modal" data-bs-target="#qrModal">
            📱 Scan QR Absensi
        </button>
    </div>

    {{-- Materi --}}
    <div class="section-title">📎 Materi</div>
    <div class="card">
        @if($materials->isEmpty())
            <div class="card-empty">Belum ada materi untuk pertemuan ini.</div>
        @else
            @foreach($materials as $material)
            <div class="material-item">
                <div class="material-icon {{ $material->type === 'file' ? 'file' : 'link' }}">
                    {{ $material->type === 'file' ? '📄' : '🔗' }}
                </div>
                <div class="material-info">
                    <div class="material-title">{{ $material->title }}</div>
                    <div class="material-type">{{ $material->type === 'file' ? 'File' : 'Link' }}</div>
                </div>
                @if($material->type === 'file')
                    <a href="{{ route('materials.download', [$course, $meeting, $material]) }}"
                       class="btn-material btn-download">⬇️ Download</a>
                @else
                    <a href="{{ $material->link_url }}" target="_blank"
                       class="btn-material btn-link">🔗 Buka</a>
                @endif
            </div>
            @endforeach
        @endif
    </div>

    {{-- Tugas --}}
    <div class="section-title">📋 Tugas</div>
    <div class="card">
        @php $assignments = $meeting->assignments; @endphp
        @if($assignments->isEmpty())
            <div class="card-empty">Belum ada tugas untuk pertemuan ini.</div>
        @else
            @foreach($assignments as $assignment)
            <div class="assignment-item">
                <div class="assignment-title">{{ $assignment->title }}</div>
                @if($assignment->description)
                    <div class="assignment-desc">{{ $assignment->description }}</div>
                @endif
                <div class="deadline">⏰ Deadline: {{ \Carbon\Carbon::parse($assignment->due_date)->format('d M Y') }}</div>

                @php
                    $submission = $assignment->submissions()
                                    ->where('student_nim', session('student_nim'))
                                    ->first();
                @endphp

                @if($submission)
                    <div class="submitted-badge">
                        ✅ Sudah dikumpulkan
                        @if($submission->score !== null)
                            &nbsp;·&nbsp; Nilai: <strong>{{ $submission->score }}</strong>
                        @endif
                    </div>
                @else
                    <a href="{{ route('submissions.create', [$course, $meeting, $assignment]) }}"
                       class="btn-submit">📥 Kumpulkan Tugas</a>
                @endif
            </div>
            @endforeach
        @endif
    </div>
</div>

{{-- Modal QR Scanner --}}
<div class="modal fade" id="qrModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header" style="background:linear-gradient(135deg,#7C3AED,#6D28D9);border-radius:16px 16px 0 0;">
                <h5 class="modal-title fw-bold" style="color:#fff;">📱 Scan QR Absensi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                <p style="color:var(--slate);font-size:14px;margin-bottom:16px;">
                    Arahkan kamera ke QR Code yang ditampilkan dosen
                </p>
                <div id="qrReader" style="width:100%;border-radius:12px;overflow:hidden;"></div>
                <div id="qrResult" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

<footer>LectraDesk · Teach Better. Manage Smarter. &copy; {{ date('Y') }}</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let html5QrCode = null;

document.getElementById('qrModal').addEventListener('shown.bs.modal', function() {
    html5QrCode = new Html5Qrcode("qrReader");
    html5QrCode.start(
        { facingMode:"environment" },
        { fps:10, qrbox:{ width:240, height:240 } },
        async (decodedText) => {
            await html5QrCode.stop();
            try {
                const url = new URL(decodedText);
                const token = url.pathname.split('/qr/')[1];
                if (!token) throw new Error('Invalid');

                const res = await fetch('/qr/' + token + '/process', {
                    method:'POST',
                    headers:{ 'X-CSRF-TOKEN':'{{ csrf_token() }}', 'Content-Type':'application/json' }
                });
                const data = await res.json();
                document.getElementById('qrResult').innerHTML = data.success
                    ? '<div style="background:#F0FDF4;border:1px solid #BBF7D0;border-radius:10px;padding:14px;color:#15803D;font-weight:600;">✅ ' + data.message + '</div>'
                    : '<div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:10px;padding:14px;color:#B91C1C;font-weight:600;">❌ ' + data.message + '</div>';
            } catch(e) {
                document.getElementById('qrResult').innerHTML =
                    '<div style="background:#FFFBEB;border:1px solid #FDE68A;border-radius:10px;padding:14px;color:#92400E;">⚠️ QR tidak valid.</div>';
            }
        },
        () => {}
    ).catch(() => {
        document.getElementById('qrResult').innerHTML =
            '<div style="background:#FFFBEB;border:1px solid #FDE68A;border-radius:10px;padding:14px;color:#92400E;">⚠️ Tidak bisa akses kamera.</div>';
    });
});

document.getElementById('qrModal').addEventListener('hidden.bs.modal', function() {
    if (html5QrCode) html5QrCode.stop().catch(()=>{});
    document.getElementById('qrResult').innerHTML = '';
    document.getElementById('qrReader').innerHTML = '';
});
</script>
</body>
</html>