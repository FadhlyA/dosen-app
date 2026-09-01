<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar — LectraDesk</title>
<link rel="icon" type="image/png" href="{{ asset('images/lectradesk/logo-icon-blue.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<style>
:root{--ink:#0F172A;--blue:#2563EB;--blue-deep:#1D4ED8;--sky:#EFF6FF;--slate:#64748B;--paper:#FAFBFF;--border:rgba(15,23,42,0.08);}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Inter',sans-serif;min-height:100vh;display:grid;grid-template-columns:1fr 1fr;}
.left{background:linear-gradient(160deg,var(--ink) 0%,#1E3A5F 100%);display:flex;flex-direction:column;justify-content:space-between;padding:48px;position:relative;overflow:hidden;}
.left::before{content:'';position:absolute;top:-200px;right:-200px;width:500px;height:500px;border-radius:50%;background:radial-gradient(circle,rgba(37,99,235,0.2) 0%,transparent 70%);}
.left-brand{display:flex;align-items:center;gap:10px;text-decoration:none;}
.left-brand span{font-family:'Sora',sans-serif;font-weight:700;font-size:20px;color:#fff;}
.left-brand .accent{color:#60A5FA;}
.left-content{position:relative;z-index:1;}
.left-content h2{font-family:'Sora',sans-serif;font-weight:800;font-size:28px;color:#fff;line-height:1.3;margin-bottom:16px;}
.left-content p{color:#94A3B8;font-size:14px;line-height:1.7;margin-bottom:24px;}
.steps-mini{display:flex;flex-direction:column;gap:16px;}
.step-mini{display:flex;gap:12px;align-items:flex-start;}
.step-num{width:24px;height:24px;border-radius:50%;background:rgba(37,99,235,0.3);border:1px solid rgba(37,99,235,0.4);color:#93C5FD;font-size:12px;font-weight:700;font-family:'Sora',sans-serif;display:flex;align-items:center;justify-content:center;flex:none;margin-top:1px;}
.step-text h4{font-size:14px;font-weight:600;color:#E2E8F0;margin-bottom:2px;}
.step-text p{font-size:13px;color:#64748B;}
.left-footer{color:#475569;font-size:13px;}
.right{background:var(--paper);display:flex;align-items:center;justify-content:center;padding:48px 32px;}
.auth-box{width:100%;max-width:420px;}
.auth-box h1{font-family:'Sora',sans-serif;font-weight:700;font-size:26px;color:var(--ink);margin-bottom:8px;}
.auth-box .sub{color:var(--slate);font-size:14px;margin-bottom:28px;}
.form-group{margin-bottom:16px;}
.form-group label{display:block;font-size:13px;font-weight:600;color:var(--ink);margin-bottom:6px;}
.form-group label .hint{font-weight:400;color:var(--slate);}
.form-group input{width:100%;padding:11px 14px;border-radius:10px;border:1px solid rgba(15,23,42,0.15);font-family:'Inter',sans-serif;font-size:14px;color:var(--ink);background:#fff;transition:border-color 0.15s,box-shadow 0.15s;outline:none;}
.form-group input:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(37,99,235,0.1);}
.form-row-2{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.info-box{background:var(--sky);border:1px solid rgba(37,99,235,0.15);border-radius:10px;padding:12px 14px;margin-bottom:20px;}
.info-box p{font-size:13px;color:#1D4ED8;line-height:1.5;}
.btn-submit{width:100%;padding:13px;border-radius:10px;border:none;cursor:pointer;background:linear-gradient(135deg,var(--blue),var(--blue-deep));color:#fff;font-family:'Sora',sans-serif;font-weight:700;font-size:15px;transition:transform 0.15s,box-shadow 0.15s;box-shadow:0 8px 20px -8px rgba(37,99,235,0.6);}
.btn-submit:hover{transform:translateY(-1px);box-shadow:0 10px 24px -6px rgba(37,99,235,0.7);}
.divider{display:flex;align-items:center;gap:12px;margin:20px 0;}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--border);}
.divider span{color:var(--slate);font-size:13px;}
.alt-action{text-align:center;font-size:14px;color:var(--slate);}
.alt-action a{color:var(--blue);font-weight:600;text-decoration:none;}
.alert-error{background:#FEF2F2;border:1px solid #FECACA;border-radius:10px;padding:12px 14px;margin-bottom:20px;color:#B91C1C;font-size:13px;}
@media(max-width:768px){body{grid-template-columns:1fr;}.left{display:none;}.right{padding:32px 20px;}}
</style>
</head>
<body>

{{-- LEFT --}}
<div class="left">
    <a href="{{ route('landing') }}" class="left-brand">
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
        <h2>Mulai kelola perkuliahan Anda hari ini.</h2>
        <p>Daftar gratis dan rasakan kemudahan manajemen kelas digital.</p>
        <div class="steps-mini">
            <div class="step-mini">
                <div class="step-num">1</div>
                <div class="step-text">
                    <h4>Buat akun</h4>
                    <p>Isi data & verifikasi email Anda</p>
                </div>
            </div>
            <div class="step-mini">
                <div class="step-num">2</div>
                <div class="step-text">
                    <h4>Setup profil & kampus</h4>
                    <p>Lengkapi info institusi untuk KOP surat</p>
                </div>
            </div>
            <div class="step-mini">
                <div class="step-num">3</div>
                <div class="step-text">
                    <h4>Mulai mengajar</h4>
                    <p>Buat kelas dan kelola perkuliahan</p>
                </div>
            </div>
        </div>
    </div>

    <div class="left-footer">
        Teach Better. Manage Smarter. &copy; {{ date('Y') }} LectraDesk
    </div>
</div>

{{-- RIGHT --}}
<div class="right">
    <div class="auth-box">
        <h1>Buat akun dosen</h1>
        <p class="sub">Gratis — tidak perlu kartu kredit</p>

        @if($errors->any())
        <div class="alert-error">
            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       placeholder="Dr. Nama Dosen, M.Kom" required autofocus>
            </div>
            <div class="form-group">
                <label>Alamat Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       placeholder="dosen@kampus.ac.id" required>
            </div>
            <div class="form-group">
                <label>No HP / WhatsApp <span class="hint">(untuk verifikasi admin)</span></label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                       placeholder="08xxxxxxxxxx">
            </div>
            <div class="form-row-2">
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Min. 8 karakter" required>
                </div>
                <div class="form-group">
                    <label>Konfirmasi</label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi password" required>
                </div>
            </div>

            <div class="info-box">
                <p>ℹ️ Setelah daftar, cek email Anda untuk verifikasi akun. Admin akan menghubungi via WhatsApp untuk konfirmasi.</p>
            </div>

            <button type="submit" class="btn-submit">Buat Akun Gratis</button>
        </form>

        <div class="divider"><span>sudah punya akun?</span></div>
        <div class="alt-action">
            <a href="{{ route('login') }}">Masuk ke LectraDesk</a>
        </div>
    </div>
</div>

</body>
</html>