<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lupa Password — LectraDesk</title>
<link rel="icon" type="image/png" href="{{ asset('images/lectradesk/logo-icon-blue.png') }}">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root{--ink:#0F172A;--blue:#2563EB;--blue-deep:#1D4ED8;--sky:#EFF6FF;--slate:#64748B;--border:rgba(15,23,42,0.08);}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Inter',sans-serif;min-height:100vh;background:var(--sky);display:flex;align-items:center;justify-content:center;padding:24px;}
.card{background:#fff;border:1px solid var(--border);border-radius:20px;padding:48px 40px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(15,23,42,0.08);}
.brand{display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:32px;text-decoration:none;}
.brand-name{font-family:'Sora',sans-serif;font-weight:700;font-size:20px;color:var(--ink);}
.brand-name .accent{color:var(--blue);}
h1{font-family:'Sora',sans-serif;font-weight:700;font-size:22px;color:var(--ink);margin-bottom:8px;}
.sub{color:var(--slate);font-size:14px;line-height:1.6;margin-bottom:28px;}
label{display:block;font-size:13px;font-weight:600;color:var(--ink);margin-bottom:6px;}
input{width:100%;padding:11px 14px;border-radius:10px;border:1px solid rgba(15,23,42,0.15);font-family:'Inter',sans-serif;font-size:14px;color:var(--ink);outline:none;transition:border-color 0.15s,box-shadow 0.15s;margin-bottom:20px;}
input:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(37,99,235,0.1);}
.btn-submit{width:100%;padding:13px;border-radius:10px;border:none;cursor:pointer;background:linear-gradient(135deg,var(--blue),var(--blue-deep));color:#fff;font-family:'Sora',sans-serif;font-weight:700;font-size:15px;margin-bottom:16px;transition:transform 0.15s;}
.btn-submit:hover{transform:translateY(-1px);}
.back{display:block;text-align:center;font-size:13px;color:var(--slate);text-decoration:none;}
.back:hover{color:var(--blue);}
.alert-success{background:#F0FDF4;border:1px solid #BBF7D0;border-radius:10px;padding:12px 14px;margin-bottom:20px;color:#15803D;font-size:13px;}
.alert-error{background:#FEF2F2;border:1px solid #FECACA;border-radius:10px;padding:12px 14px;margin-bottom:20px;color:#B91C1C;font-size:13px;}
</style>
</head>
<body>
<div class="card">
    <a href="{{ route('login') }}" class="brand">
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

    @if(session('status'))
    <div class="alert-success">✅ {{ session('status') }}</div>
    @endif
    @if($errors->any())
    <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <h1>🔑 Lupa Password</h1>
    <p class="sub">Masukkan email Anda dan kami akan mengirimkan link untuk reset password.</p>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <label>Alamat Email</label>
        <input type="email" name="email" value="{{ old('email') }}"
               placeholder="dosen@kampus.ac.id" required autofocus>
        <button type="submit" class="btn-submit">📧 Kirim Link Reset</button>
    </form>

    <a href="{{ route('login') }}" class="back">← Kembali ke Login</a>
</div>
</body>
</html>