<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verifikasi Email — LectraDesk</title>
<link rel="icon" type="image/png" href="{{ asset('images/lectradesk/logo-icon-blue.png') }}">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root{--ink:#0F172A;--blue:#2563EB;--blue-deep:#1D4ED8;--sky:#EFF6FF;--slate:#64748B;--border:rgba(15,23,42,0.08);}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Inter',sans-serif;min-height:100vh;background:var(--sky);display:flex;align-items:center;justify-content:center;padding:24px;}
.card{background:#fff;border:1px solid var(--border);border-radius:20px;padding:48px 40px;width:100%;max-width:440px;box-shadow:0 20px 60px rgba(15,23,42,0.08);text-align:center;}
.brand{display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:32px;text-decoration:none;}
.brand-name{font-family:'Sora',sans-serif;font-weight:700;font-size:20px;color:var(--ink);}
.brand-name .accent{color:var(--blue);}
.icon{font-size:3.5rem;margin-bottom:16px;}
h1{font-family:'Sora',sans-serif;font-weight:700;font-size:22px;color:var(--ink);margin-bottom:8px;}
.sub{color:var(--slate);font-size:14px;line-height:1.6;margin-bottom:24px;}
.alert-success{background:#F0FDF4;border:1px solid #BBF7D0;border-radius:10px;padding:12px 14px;margin-bottom:20px;color:#15803D;font-size:13px;}
.btn-primary{display:block;width:100%;padding:13px;border-radius:10px;border:none;cursor:pointer;background:linear-gradient(135deg,var(--blue),var(--blue-deep));color:#fff;font-family:'Sora',sans-serif;font-weight:700;font-size:15px;text-decoration:none;margin-bottom:16px;transition:transform 0.15s;}
.btn-primary:hover{transform:translateY(-1px);color:#fff;}
.logout-form button{background:transparent;border:none;color:var(--slate);font-size:13px;cursor:pointer;text-decoration:underline;}
.logout-form button:hover{color:var(--ink);}
</style>
</head>
<body>
<div class="card">
    <a href="{{ route('landing') }}" class="brand">
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

    @if(session('resent'))
    <div class="alert-success">✅ Link verifikasi baru telah dikirim ke email Anda!</div>
    @endif

    <div class="icon">📧</div>
    <h1>Verifikasi Email Anda</h1>
    <p class="sub">
        Kami telah mengirimkan link verifikasi ke email Anda.<br>
        Klik link tersebut untuk mengaktifkan akun LectraDesk.
    </p>

    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit" class="btn-primary">📨 Kirim Ulang Email</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="logout-form">
        @csrf
        <button type="submit">Logout</button>
    </form>
</div>
</body>
</html>