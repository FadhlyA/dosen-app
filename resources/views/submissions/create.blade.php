<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kumpulkan Tugas — LectraDesk</title>
<link rel="icon" type="image/png" href="{{ asset('images/lectradesk/logo-icon-blue.png') }}">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<style>
:root{--ink:#0F172A;--blue:#2563EB;--blue-deep:#1D4ED8;--sky:#EFF6FF;--slate:#64748B;--paper:#FAFBFF;--border:rgba(15,23,42,0.08);}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Inter',sans-serif;background:var(--paper);color:var(--ink);min-height:100vh;}
nav{background:linear-gradient(135deg,var(--ink),#1E3A5F);padding:14px 0;}
.nav-inner{max-width:860px;margin:0 auto;padding:0 20px;display:flex;justify-content:space-between;align-items:center;}
.brand{display:flex;align-items:center;gap:8px;font-family:'Sora',sans-serif;font-weight:700;font-size:18px;color:#fff;text-decoration:none;}
.brand .accent{color:#60A5FA;}
.btn-back{padding:6px 14px;border-radius:8px;border:1px solid rgba(255,255,255,0.2);color:#fff;background:transparent;font-size:13px;text-decoration:none;transition:background 0.15s;}
.btn-back:hover{background:rgba(255,255,255,0.1);color:#fff;}
.wrap{max-width:860px;margin:0 auto;padding:32px 20px;}
.student-bar{background:#fff;border:1px solid var(--border);border-radius:12px;padding:14px 20px;margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;}
.student-name{font-weight:700;font-size:15px;}
.student-nim{font-family:'JetBrains Mono',monospace;font-size:13px;color:var(--blue);}
.assignment-card{background:#fff;border:1px solid var(--border);border-radius:16px;padding:28px;margin-bottom:20px;}
.assignment-label{font-size:12px;font-weight:600;color:var(--blue);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px;}
.assignment-title{font-family:'Sora',sans-serif;font-weight:700;font-size:20px;margin-bottom:10px;}
.assignment-desc{font-size:14px;color:var(--slate);line-height:1.7;margin-bottom:12px;}
.deadline-badge{display:inline-flex;align-items:center;gap:6px;background:#FEF2F2;color:#DC2626;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600;}
.submit-card{background:#fff;border:1px solid var(--border);border-radius:16px;overflow:hidden;}
.submit-header{background:linear-gradient(135deg,var(--blue),var(--blue-deep));padding:18px 24px;}
.submit-header h5{font-family:'Sora',sans-serif;font-weight:700;color:#fff;font-size:16px;margin:0;}
.submit-body{padding:28px;}
label{display:block;font-size:13px;font-weight:600;color:var(--ink);margin-bottom:6px;}
.file-wrap{border:2px dashed rgba(37,99,235,0.25);border-radius:12px;background:var(--sky);padding:28px;text-align:center;margin-bottom:6px;cursor:pointer;transition:border-color 0.15s;}
.file-wrap:hover{border-color:var(--blue);}
.file-wrap input[type="file"]{display:none;}
.file-icon{font-size:2rem;margin-bottom:8px;}
.file-text{font-size:14px;font-weight:600;color:var(--blue);margin-bottom:4px;}
.file-hint{font-size:12px;color:var(--slate);}
.hint{font-size:12px;color:var(--slate);margin-bottom:20px;}
textarea{width:100%;padding:12px 14px;border-radius:10px;border:1px solid rgba(15,23,42,0.15);font-family:'Inter',sans-serif;font-size:14px;color:var(--ink);resize:vertical;outline:none;transition:border-color 0.15s;}
textarea:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(37,99,235,0.1);}
.btn-submit{width:100%;padding:14px;border-radius:10px;border:none;cursor:pointer;background:linear-gradient(135deg,var(--blue),var(--blue-deep));color:#fff;font-family:'Sora',sans-serif;font-weight:700;font-size:16px;margin-top:24px;transition:transform 0.15s;box-shadow:0 8px 20px -8px rgba(37,99,235,0.6);}
.btn-submit:hover{transform:translateY(-1px);}
.alert-success{background:#F0FDF4;border:1px solid #BBF7D0;border-radius:10px;padding:14px;margin-bottom:20px;color:#15803D;font-size:14px;}
.alert-error{background:#FEF2F2;border:1px solid #FECACA;border-radius:10px;padding:14px;margin-bottom:20px;color:#B91C1C;font-size:14px;}
.submitted-state{text-align:center;padding:32px;}
.submitted-icon{font-size:3rem;margin-bottom:12px;}
.submitted-title{font-family:'Sora',sans-serif;font-weight:700;font-size:18px;color:#16A34A;margin-bottom:8px;}
.submitted-meta{font-size:14px;color:var(--slate);}
footer{text-align:center;padding:28px 20px;color:var(--slate);font-size:13px;border-top:1px solid var(--border);margin-top:40px;}
</style>
</head>
<body>

<nav>
    <div class="nav-inner">
        <a href="{{ route('student.meeting', [$course, $meeting]) }}" class="brand">
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
        <a href="{{ route('student.meeting', [$course, $meeting]) }}" class="btn-back">← Kembali</a>
    </div>
</nav>

<div class="wrap">

    {{-- Student Bar --}}
    <div class="student-bar">
        <div>
            <div class="student-name">{{ session('student_name') }}</div>
            <div class="student-nim">{{ session('student_nim') }}</div>
        </div>
        <div style="font-size:13px;color:var(--slate);">{{ $course->name }} · {{ $course->class_name }}</div>
    </div>

    @if(session('success'))
    <div class="alert-success">✅ {{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    {{-- Assignment Info --}}
    <div class="assignment-card">
        <div class="assignment-label">📋 Detail Tugas</div>
        <div class="assignment-title">{{ $assignment->title }}</div>
        @if($assignment->description)
            <div class="assignment-desc">{{ $assignment->description }}</div>
        @endif
        <div class="deadline-badge">
            ⏰ Deadline: {{ \Carbon\Carbon::parse($assignment->due_date)->format('d M Y') }}
        </div>
    </div>

    {{-- Submit Form --}}
    <div class="submit-card">
        <div class="submit-header">
            <h5>📥 Kumpulkan Tugas</h5>
        </div>
        <div class="submit-body">

            @if($submission)
            {{-- Sudah dikumpulkan --}}
            <div class="submitted-state">
                <div class="submitted-icon">✅</div>
                <div class="submitted-title">Tugas Sudah Dikumpulkan!</div>
                <div class="submitted-meta">
                    {{ $submission->original_name ?? 'File sudah dikirim' }}
                    @if($submission->score !== null)
                        <br><strong style="color:var(--blue);font-size:16px;margin-top:8px;display:block;">
                            Nilai: {{ $submission->score }}
                        </strong>
                    @endif
                    @if($submission->note)
                        <br><span style="font-size:13px;">Catatan: {{ $submission->note }}</span>
                    @endif
                </div>
            </div>
            @else
            {{-- Form Upload --}}
            <form action="{{ route('submissions.store', [$course, $meeting, $assignment]) }}"
                  method="POST" enctype="multipart/form-data" id="submitForm">
                @csrf
                <div style="margin-bottom:20px;">
                    <label>File Tugas</label>
                    <div class="file-wrap" onclick="document.getElementById('fileInput').click()">
                        <input type="file" id="fileInput" name="file"
                               accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.rar"
                               onchange="updateFileName(this)">
                        <div class="file-icon">📄</div>
                        <div class="file-text" id="fileLabel">Klik untuk pilih file</div>
                        <div class="file-hint">PDF, DOC, DOCX, PPT, PPTX, ZIP, RAR · Maks 10MB</div>
                    </div>
                </div>
                <div>
                    <label>Catatan <span style="color:var(--slate);font-weight:400;">(opsional)</span></label>
                    <textarea name="note" rows="3"
                              placeholder="Catatan tambahan untuk dosen..."></textarea>
                </div>
                <button type="submit" class="btn-submit">📥 Kumpulkan Tugas</button>
            </form>
            @endif

        </div>
    </div>
</div>

<footer>LectraDesk · Teach Better. Manage Smarter. &copy; {{ date('Y') }}</footer>

<script>
function updateFileName(input) {
    const label = document.getElementById('fileLabel');
    if (input.files && input.files[0]) {
        label.textContent = '✅ ' + input.files[0].name;
        label.style.color = '#16A34A';
    }
}
</script>
</body>
</html>