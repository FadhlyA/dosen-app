<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verifikasi NIM — LectraDesk</title>
<link rel="icon" type="image/png" href="{{ asset('images/lectradesk/logo-icon-blue.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<style>
:root{--ink:#0F172A;--blue:#2563EB;--blue-deep:#1D4ED8;--sky:#EFF6FF;--slate:#64748B;--paper:#FAFBFF;--border:rgba(15,23,42,0.08);}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Inter',sans-serif;min-height:100vh;background:var(--paper);display:flex;align-items:center;justify-content:center;padding:24px;}
.card{background:#fff;border:1px solid var(--border);border-radius:20px;padding:40px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(15,23,42,0.08);}
.brand{display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:28px;text-decoration:none;}
.brand-name{font-family:'Sora',sans-serif;font-weight:700;font-size:20px;color:var(--ink);}
.brand-name .accent{color:var(--blue);}
.course-info{background:var(--sky);border-radius:12px;padding:14px 16px;margin-bottom:24px;text-align:center;}
.course-name{font-family:'Sora',sans-serif;font-weight:700;font-size:16px;color:var(--ink);margin-bottom:2px;}
.course-meta{font-size:13px;color:var(--slate);}
h1{font-family:'Sora',sans-serif;font-weight:700;font-size:22px;color:var(--ink);margin-bottom:6px;}
.sub{color:var(--slate);font-size:14px;margin-bottom:24px;}
label{display:block;font-size:13px;font-weight:600;color:var(--ink);margin-bottom:6px;}
.nim-input{
    width:100%;padding:14px;border-radius:10px;
    border:2px solid rgba(15,23,42,0.12);
    font-family:'JetBrains Mono',monospace;
    font-size:1.25rem;font-weight:600;
    text-align:center;letter-spacing:4px;
    color:var(--ink);background:#fff;
    transition:border-color 0.15s,box-shadow 0.15s;outline:none;
    margin-bottom:6px;
}
.nim-input:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(37,99,235,0.1);}
.hint{font-size:12px;color:var(--slate);margin-bottom:20px;display:block;}
.btn-submit{width:100%;padding:13px;border-radius:10px;border:none;cursor:pointer;background:linear-gradient(135deg,var(--blue),var(--blue-deep));color:#fff;font-family:'Sora',sans-serif;font-weight:700;font-size:15px;transition:transform 0.15s,box-shadow 0.15s;box-shadow:0 8px 20px -8px rgba(37,99,235,0.6);margin-bottom:16px;}
.btn-submit:hover{transform:translateY(-1px);box-shadow:0 10px 24px -6px rgba(37,99,235,0.7);}
.back-link{display:block;text-align:center;font-size:13px;color:var(--slate);text-decoration:none;}
.back-link:hover{color:var(--blue);}
.alert-error{background:#FEF2F2;border:1px solid #FECACA;border-radius:10px;padding:12px 14px;margin-bottom:20px;color:#B91C1C;font-size:13px;}
</style>
</head>
<body>

<div class="card">
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
        <span class="brand-name">Lectra<span class="accent">Desk</span></span>
    </a>

    <div class="course-info">
        <div class="course-name">{{ $course->name }}</div>
        <div class="course-meta">{{ $course->class_name }} · {{ $course->semester }}</div>
    </div>

    <h1>🎓 Verifikasi NIM</h1>
    <p class="sub">Masukkan NIM Anda untuk melanjutkan</p>

    @if($errors->any())
    <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('student.verify-nim.post', $course) }}" method="POST">
        @csrf
        <label>Nomor Induk Mahasiswa (NIM)</label>
        <input type="text" name="nim" class="nim-input"
               placeholder="NIM Anda"
               value="{{ old('nim') }}"
               required autofocus>
        <span class="hint">NIM harus terdaftar di kelas ini</span>
        <button type="submit" class="btn-submit">Verifikasi & Masuk</button>
    </form>

    <a href="{{ route('student.index') }}" class="back-link">← Ganti Key Kelas</a>
</div>

</body>
</html>