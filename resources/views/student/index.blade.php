<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Portal Mahasiswa — LectraDesk</title>
<link rel="icon" type="image/png" href="{{ asset('images/lectradesk/logo-icon-blue.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<style>
:root{--ink:#0F172A;--blue:#2563EB;--blue-deep:#1D4ED8;--sky:#EFF6FF;--slate:#64748B;--paper:#FAFBFF;--border:rgba(15,23,42,0.08);}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Inter',sans-serif;min-height:100vh;display:grid;grid-template-columns:1fr 1fr;}
.left{background:linear-gradient(160deg,var(--ink) 0%,#1E3A5F 100%);display:flex;flex-direction:column;justify-content:space-between;padding:48px;position:relative;overflow:hidden;}
.left::before{content:'';position:absolute;top:-200px;right:-200px;width:500px;height:500px;border-radius:50%;background:radial-gradient(circle,rgba(37,99,235,0.2) 0%,transparent 70%);}
.brand{display:flex;align-items:center;gap:10px;text-decoration:none;}
.brand span{font-family:'Sora',sans-serif;font-weight:700;font-size:20px;color:#fff;}
.brand .accent{color:#60A5FA;}
.left-content{position:relative;z-index:1;}
.left-content h2{font-family:'Sora',sans-serif;font-weight:800;font-size:28px;color:#fff;line-height:1.3;margin-bottom:16px;}
.left-content p{color:#94A3B8;font-size:14px;line-height:1.7;margin-bottom:24px;}
.info-list{display:flex;flex-direction:column;gap:12px;}
.info-item{display:flex;align-items:center;gap:12px;color:#CBD5E1;font-size:14px;}
.info-icon{width:32px;height:32px;border-radius:8px;background:rgba(37,99,235,0.25);border:1px solid rgba(37,99,235,0.3);display:flex;align-items:center;justify-content:center;font-size:14px;flex:none;}
.left-footer{color:#475569;font-size:13px;}
.right{background:var(--paper);display:flex;align-items:center;justify-content:center;padding:48px 32px;}
.auth-box{width:100%;max-width:380px;}
.auth-box h1{font-family:'Sora',sans-serif;font-weight:700;font-size:26px;color:var(--ink);margin-bottom:8px;}
.auth-box .sub{color:var(--slate);font-size:14px;margin-bottom:28px;}
.form-group{margin-bottom:20px;}
.form-group label{display:block;font-size:13px;font-weight:600;color:var(--ink);margin-bottom:6px;}
.key-input{
    width:100%;padding:16px;border-radius:12px;
    border:2px solid rgba(15,23,42,0.12);
    font-family:'JetBrains Mono',monospace;
    font-size:1.5rem;font-weight:600;
    text-align:center;letter-spacing:6px;
    color:var(--blue);background:#fff;
    text-transform:uppercase;
    transition:border-color 0.15s,box-shadow 0.15s;outline:none;
}
.key-input:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(37,99,235,0.1);}
.key-input::placeholder{color:#CBD5E1;letter-spacing:2px;font-size:1rem;}
.btn-submit{width:100%;padding:13px;border-radius:10px;border:none;cursor:pointer;background:linear-gradient(135deg,var(--blue),var(--blue-deep));color:#fff;font-family:'Sora',sans-serif;font-weight:700;font-size:15px;transition:transform 0.15s,box-shadow 0.15s;box-shadow:0 8px 20px -8px rgba(37,99,235,0.6);}
.btn-submit:hover{transform:translateY(-1px);box-shadow:0 10px 24px -6px rgba(37,99,235,0.7);}
.divider{display:flex;align-items:center;gap:12px;margin:24px 0;}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--border);}
.divider span{color:var(--slate);font-size:13px;}
.dosen-link{display:flex;align-items:center;justify-content:center;gap:8px;padding:11px;border-radius:10px;border:1px solid var(--border);color:var(--slate);font-size:13px;text-decoration:none;font-weight:500;transition:background 0.15s,border-color 0.15s;}
.dosen-link:hover{background:var(--sky);border-color:rgba(37,99,235,0.2);color:var(--blue);}
.alert-error{background:#FEF2F2;border:1px solid #FECACA;border-radius:10px;padding:12px 14px;margin-bottom:20px;color:#B91C1C;font-size:13px;}
@media(max-width:768px){body{grid-template-columns:1fr;}.left{display:none;}.right{padding:32px 20px;}}
</style>
</head>
<body>

<div class="left">
    <a href="{{ route('landing') }}" class="brand">
        <svg width="32" height="32" viewBox="0 0 100 100" fill="none">
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
        <span>Lectra<span class="accent">Desk</span></span>
    </a>

    <div class="left-content">
        <h2>Portal Mahasiswa</h2>
        <p>Akses materi, kumpulkan tugas, dan cek absensi Anda dengan memasukkan key kelas dari dosen.</p>
        <div class="info-list">
            <div class="info-item"><div class="info-icon">📎</div>Akses materi & download file perkuliahan</div>
            <div class="info-item"><div class="info-icon">📥</div>Upload & kumpulkan tugas online</div>
            <div class="info-item"><div class="info-icon">📱</div>Scan QR Code untuk absensi otomatis</div>
            <div class="info-item"><div class="info-icon">📊</div>Pantau rekap kehadiran Anda</div>
        </div>
    </div>

    <div class="left-footer">Teach Better. Manage Smarter. &copy; {{ date('Y') }} LectraDesk</div>
</div>

<div class="right">
    <div class="auth-box">
        <h1>🔑 Masuk ke Kelas</h1>
        <p class="sub">Masukkan key kelas yang diberikan dosen Anda</p>

        @if($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('student.verify') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Key Kelas</label>
                <input type="text" name="access_key" class="key-input"
                       placeholder="AB12CD34"
                       value="{{ old('access_key') }}"
                       maxlength="8" required>
            </div>
            <button type="submit" class="btn-submit">Masuk ke Kelas</button>
        </form>

        <div class="divider"><span>atau</span></div>
        <a href="{{ route('login') }}" class="dosen-link">👨‍🏫 Saya Dosen — Login di sini</a>
    </div>
</div>

<script>
document.querySelector('.key-input').addEventListener('input', function() {
    this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g,'');
});
</script>
</body>
</html>