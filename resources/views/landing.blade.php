<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LectraDesk — Teach Better. Manage Smarter.</title>
<link rel="icon" type="image/png" href="{{ asset('images/lectradesk/logo-icon-blue.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<style>
:root{
    --ink:#0F172A; --blue:#2563EB; --blue-deep:#1D4ED8;
    --sky:#EFF6FF; --slate:#64748B; --paper:#FAFBFF;
    --white:#FFFFFF; --border:rgba(15,23,42,0.08);
}
*{box-sizing:border-box;}
html{scroll-behavior:smooth;}
body{margin:0;background:var(--paper);color:var(--ink);font-family:'Inter',sans-serif;line-height:1.6;}
h1,h2,h3{font-family:'Sora',sans-serif;font-weight:700;letter-spacing:-0.02em;margin:0;}
.wrap{max-width:1120px;margin:0 auto;padding:0 24px;}

/* ── NAV (dari referensi: light sticky) ── */
nav{
    position:sticky;top:0;z-index:50;
    background:rgba(250,251,255,0.92);
    backdrop-filter:blur(8px);
    border-bottom:1px solid var(--border);
}
.nav-inner{
    display:flex;align-items:center;justify-content:space-between;
    padding:14px 24px;max-width:1120px;margin:0 auto;
}
.brand{display:flex;align-items:center;gap:10px;text-decoration:none;}
.brand-name{font-family:'Sora',sans-serif;font-weight:700;font-size:20px;color:var(--ink);}
.brand-name .accent{color:var(--blue);}
.nav-links{display:flex;align-items:center;gap:32px;}
.nav-links a{color:var(--ink);text-decoration:none;font-size:14px;font-weight:500;transition:color 0.15s;}
.nav-links a:hover{color:var(--blue);}
.nav-cta{display:flex;gap:12px;align-items:center;}
.btn{
    display:inline-flex;align-items:center;justify-content:center;
    padding:10px 20px;border-radius:10px;font-weight:600;font-size:14px;
    text-decoration:none;cursor:pointer;border:none;
    transition:transform .15s ease,box-shadow .15s ease;
    font-family:'Inter',sans-serif;
}
.btn-primary{
    background:linear-gradient(135deg,var(--blue),var(--blue-deep));
    color:#fff;box-shadow:0 8px 20px -8px rgba(37,99,235,0.6);
}
.btn-primary:hover{transform:translateY(-1px);box-shadow:0 10px 24px -6px rgba(37,99,235,0.7);color:#fff;}
.btn-ghost{background:transparent;color:var(--ink);border:1px solid var(--border);}
.btn-ghost:hover{background:var(--sky);color:var(--ink);}
.btn-lg{padding:14px 26px;font-size:15px;border-radius:12px;}

/* ── HERO (dari referensi: 2 kolom + SVG book-scene) ── */
.hero{
    padding:88px 0 64px;
    display:grid;grid-template-columns:1.05fr 0.95fr;
    gap:48px;align-items:center;
}
.eyebrow{
    display:inline-flex;align-items:center;gap:8px;
    background:var(--sky);color:var(--blue-deep);
    font-size:13px;font-weight:600;
    padding:6px 14px;border-radius:999px;margin-bottom:20px;
}
.eyebrow::before{content:"●";font-size:8px;}
.hero h1{font-size:48px;line-height:1.08;}
.hero h1 .accent{color:var(--blue);}
.hero p.lead{color:var(--slate);font-size:17px;margin-top:20px;max-width:480px;}
.hero-cta{display:flex;gap:14px;margin-top:32px;flex-wrap:wrap;}

/* Book scene illustration */
.hero-visual{position:relative;height:420px;display:flex;align-items:center;justify-content:center;}
.book-scene{position:relative;width:320px;height:320px;}
.book-base{
    position:absolute;inset:0;border-radius:28px;
    background:linear-gradient(160deg,var(--blue) 0%,var(--blue-deep) 100%);
    box-shadow:0 30px 60px -20px rgba(29,78,216,0.45);
}
.book-base::before{content:"";position:absolute;top:-8px;left:28%;width:10px;height:16px;background:#1E40AF;border-radius:4px 4px 0 0;}
.book-base::after{content:"";position:absolute;top:-8px;left:62%;width:10px;height:16px;background:#1E40AF;border-radius:4px 4px 0 0;}
.book-page{
    position:absolute;top:18px;left:18px;right:18px;bottom:18px;
    background:#fff;border-radius:18px;display:flex;overflow:hidden;
}
.book-page::after{content:"";position:absolute;top:0;bottom:0;left:50%;width:1px;background:var(--border);}
.page-half{flex:1;padding:22px;display:flex;flex-direction:column;gap:10px;}
.grid-dot{display:grid;grid-template-columns:repeat(2,1fr);gap:8px;}
.grid-dot span{width:22px;height:22px;border-radius:6px;background:var(--sky);}
.check-row{display:flex;align-items:center;gap:8px;}
.check-row .dot{width:16px;height:16px;border-radius:5px;background:var(--blue);position:relative;flex:none;}
.check-row .dot::after{content:"✓";color:#fff;font-size:10px;position:absolute;inset:0;display:flex;align-items:center;justify-content:center;}
.check-row .line{height:6px;flex:1;border-radius:3px;background:var(--sky);}

/* Floating cards */
.float-card{
    position:absolute;background:#fff;border-radius:14px;
    box-shadow:0 20px 40px -14px rgba(15,23,42,0.18);
    padding:14px 16px;border:1px solid var(--border);
    animation:floaty 5s ease-in-out infinite;
}
@keyframes floaty{0%,100%{transform:translateY(0);}50%{transform:translateY(-8px);}}
.card-qr{top:-18px;right:-30px;display:flex;align-items:center;gap:10px;animation-delay:.4s;}
.qr-box{width:34px;height:34px;border-radius:8px;background:var(--ink);position:relative;flex:none;}
.qr-box::after{content:"";position:absolute;inset:6px;background:repeating-linear-gradient(90deg,#fff 0 3px,transparent 3px 6px);}
.card-qr .lbl{font-size:12px;font-weight:600;color:var(--ink);}
.card-qr .mono{font-size:11px;color:var(--blue);font-family:'JetBrains Mono',monospace;}
.card-score{bottom:-10px;left:-36px;width:170px;animation-delay:1.2s;}
.card-score .lbl{font-size:11px;color:var(--slate);margin-bottom:6px;font-weight:500;}
.bar-row{display:flex;align-items:center;gap:8px;margin-bottom:5px;}
.bar-row .tag{font-size:11px;width:34px;color:var(--slate);}
.bar-track{flex:1;height:6px;background:var(--sky);border-radius:3px;overflow:hidden;}
.bar-fill{height:100%;background:linear-gradient(90deg,var(--blue),var(--blue-deep));border-radius:3px;}

/* ── SECTIONS ── */
.section{padding:72px 0;}
.section-head{text-align:center;max-width:620px;margin:0 auto 48px;}
.section-head h2{font-size:32px;}
.section-head p{color:var(--slate);margin-top:12px;font-size:16px;}

/* ── PROBLEM → SOLUTION (dari referensi) ── */
.ps-grid{
    display:grid;grid-template-columns:1fr auto 1fr;gap:0;
    align-items:stretch;background:var(--white);
    border:1px solid var(--border);border-radius:20px;padding:8px;
}
.ps-card{padding:32px;border-radius:16px;}
.ps-before{background:#F8FAFC;}
.ps-after{background:var(--sky);}
.ps-tag{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;}
.ps-before .ps-tag{color:var(--slate);}
.ps-after .ps-tag{color:var(--blue-deep);}
.ps-card p.body{margin-top:14px;font-size:16px;color:var(--ink);line-height:1.7;}
.ps-arrow{align-self:center;font-size:24px;color:var(--blue);padding:0 16px;}

/* ── FEATURES (dari referensi: icon box rounded) ── */
.feat-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;}
.feat-card{
    background:#fff;border:1px solid var(--border);border-radius:16px;
    padding:26px;transition:box-shadow .2s ease,transform .2s ease;
}
.feat-card:hover{box-shadow:0 16px 32px -18px rgba(15,23,42,0.2);transform:translateY(-3px);}
.feat-icon{
    width:44px;height:44px;border-radius:12px;
    background:var(--sky);color:var(--blue);
    display:flex;align-items:center;justify-content:center;
    font-size:20px;margin-bottom:16px;
}
.feat-card h3{font-size:17px;color:var(--ink);}
.feat-card p{color:var(--slate);font-size:14px;margin-top:8px;line-height:1.6;}

/* ── HOW IT WORKS (dari referensi: nomor outline) ── */
.steps{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;}
.step{position:relative;padding-top:8px;}
.step .num{
    font-family:'Sora',sans-serif;font-weight:800;font-size:42px;
    -webkit-text-stroke:1.5px var(--blue);color:transparent;
    line-height:1;
}
.step h3{font-size:16px;margin-top:8px;color:var(--ink);}
.step p{color:var(--slate);font-size:14px;margin-top:8px;line-height:1.6;}
.step:not(:last-child)::after{
    content:"";position:absolute;top:24px;right:-32px;
    width:24px;height:1px;background:var(--border);
}

/* ── INSTITUTION (dari referensi: dark gradient) ── */
.inst{
    background:linear-gradient(135deg,var(--ink),#1E293B);
    border-radius:24px;padding:56px;color:#fff;
    display:grid;grid-template-columns:1.2fr 0.8fr;gap:32px;align-items:center;
}
.inst h2{color:#fff;font-size:28px;}
.inst p{color:#CBD5E1;margin-top:14px;line-height:1.7;}
.inst-badge{
    display:inline-flex;align-items:center;gap:10px;
    background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.14);
    border-radius:999px;padding:8px 16px;font-size:13px;margin-top:20px;color:#fff;
}
.inst-visual{
    background:rgba(255,255,255,0.06);border-radius:16px;
    padding:24px;border:1px solid rgba(255,255,255,0.1);
}
.inst-visual .row{
    display:flex;justify-content:space-between;font-size:13px;
    padding:10px 0;border-bottom:1px solid rgba(255,255,255,0.08);
}
.inst-visual .row:last-child{border-bottom:none;}
.inst-visual .row .v{color:#93C5FD;font-family:'JetBrains Mono',monospace;}

/* ── FINAL CTA ── */
.final-cta{
    text-align:center;background:var(--sky);
    border-radius:24px;padding:64px 32px;
}
.final-cta h2{font-size:30px;}
.final-cta p{color:var(--slate);margin-top:12px;font-size:16px;}
.final-cta .hero-cta{justify-content:center;margin-top:28px;}

/* ── FOOTER (dari referensi: simple clean) ── */
footer{padding:40px 0;border-top:1px solid var(--border);margin-top:24px;}
.footer-inner{
    display:flex;justify-content:space-between;
    align-items:center;color:var(--slate);font-size:13px;
}
.footer-brand{display:flex;align-items:center;gap:8px;}
.footer-links{display:flex;gap:20px;}
.footer-links a{color:var(--slate);text-decoration:none;transition:color 0.15s;}
.footer-links a:hover{color:var(--blue);}

/* ── RESPONSIVE ── */
@media(max-width:860px){
    .hero{grid-template-columns:1fr;}
    .hero-visual{height:300px;}
    .book-scene{width:260px;height:260px;}
    .nav-links{display:none;}
    .ps-grid{grid-template-columns:1fr;}
    .ps-arrow{text-align:center;padding:8px 0;}
    .feat-grid,.steps{grid-template-columns:1fr;}
    .inst{grid-template-columns:1fr;padding:32px;}
    .hero h1{font-size:32px;}
    .card-qr{right:-10px;top:-10px;}
    .card-score{left:-10px;}
    .footer-inner{flex-direction:column;gap:16px;text-align:center;}
    .step:not(:last-child)::after{display:none;}
}
</style>
</head>
<body>

{{-- NAV --}}
<nav>
  <div class="nav-inner">
    <a href="#" class="brand">
      <svg width="34" height="34" viewBox="0 0 100 100" fill="none">
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
    <div class="nav-links">
      <a href="#fitur">Fitur</a>
      <a href="#cara-kerja">Cara Kerja</a>
      <a href="#institusi">Untuk Institusi</a>
    </div>
    <div class="nav-cta">
      <a href="{{ route('login') }}" class="btn btn-ghost">Masuk</a>
      <a href="{{ route('register') }}" class="btn btn-primary">Daftar Gratis</a>
    </div>
  </div>
</nav>

{{-- HERO --}}
<header class="wrap hero">
  <div>
    <span class="eyebrow">Dibangun oleh dosen, untuk dosen</span>
    <h1>Teach Better.<br><span class="accent">Manage Smarter.</span></h1>
    <p class="lead">Kelola kelas, presensi QR, materi, tugas, dan rekap nilai dalam satu dasbor. Tidak perlu Excel manual lagi tiap akhir semester.</p>
    <div class="hero-cta">
      <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Daftar Sebagai Dosen</a>
      <a href="#cara-kerja" class="btn btn-ghost btn-lg">Lihat Cara Kerja</a>
    </div>
  </div>
  <div class="hero-visual">
    <div class="book-scene">
      <div class="book-base"></div>
      <div class="book-page">
        <div class="page-half">
          <div class="grid-dot">
            <span></span><span></span><span></span><span></span>
          </div>
        </div>
        <div class="page-half">
          <div class="check-row"><div class="dot"></div><div class="line"></div></div>
          <div class="check-row"><div class="dot"></div><div class="line"></div></div>
          <div class="check-row"><div class="dot"></div><div class="line"></div></div>
        </div>
      </div>
      <div class="float-card card-qr">
        <div class="qr-box"></div>
        <div>
          <div class="lbl">Token Presensi</div>
          <div class="mono">XK9-2F7</div>
        </div>
      </div>
      <div class="float-card card-score">
        <div class="lbl">Rekap Nilai Kelas</div>
        <div class="bar-row">
          <span class="tag">TI-3A</span>
          <div class="bar-track"><div class="bar-fill" style="width:82%"></div></div>
        </div>
        <div class="bar-row">
          <span class="tag">MI-4</span>
          <div class="bar-track"><div class="bar-fill" style="width:75%"></div></div>
        </div>
        <div class="bar-row">
          <span class="tag">TI-2B</span>
          <div class="bar-track"><div class="bar-fill" style="width:91%"></div></div>
        </div>
      </div>
    </div>
  </div>
</header>

{{-- PROBLEM → SOLUTION --}}
<section class="section" style="background:#F8FAFC;">
  <div class="wrap">
    <div class="section-head">
      <h2>Dari ribet jadi ringan</h2>
      <p>Dosen menghabiskan terlalu banyak waktu di administrasi. LectraDesk mengubah itu.</p>
    </div>
    <div class="ps-grid">
      <div class="ps-card ps-before">
        <div class="ps-tag">😓 Sebelum</div>
        <p class="body">Absensi kertas, rekap nilai di Excel terpisah, tugas dikumpul via WA, KOP surat dicetak manual tiap semester.</p>
      </div>
      <div class="ps-arrow">→</div>
      <div class="ps-card ps-after">
        <div class="ps-tag">✨ Sesudah LectraDesk</div>
        <p class="body">QR presensi otomatis, nilai terekap langsung, tugas terorganisir per pertemuan, dokumen siap cetak dengan KOP institusi.</p>
      </div>
    </div>
  </div>
</section>

{{-- FEATURES --}}
<section class="section" id="fitur">
  <div class="wrap">
    <div class="section-head">
      <h2>Semua yang Anda butuhkan</h2>
      <p>Fitur lengkap untuk manajemen perkuliahan modern, dirancang khusus untuk dosen.</p>
    </div>
    <div class="feat-grid">
      @php
      $features = [
        ['icon'=>'📱','title'=>'QR Absensi Otomatis','desc'=>'Token berubah tiap 1 menit, max 2 scan. Mahasiswa cukup buka HP dan scan — hadir tercatat otomatis.'],
        ['icon'=>'📊','title'=>'Rekap Nilai Fleksibel','desc'=>'Komponen bebas, bobot kustom, rentang huruf per kelas. Export Excel & CSV siap lapor.'],
        ['icon'=>'📥','title'=>'Manajemen Tugas','desc'=>'Mahasiswa upload via portal. Dosen download ZIP, beri nilai langsung di tabel.'],
        ['icon'=>'📅','title'=>'Kalender Pertemuan','desc'=>'Visualisasi semua pertemuan & deadline dalam kalender interaktif. Generate otomatis mingguan.'],
        ['icon'=>'🏛️','title'=>'KOP Surat Institusi','desc'=>'Set logo & info kampus di profil. Semua dokumen cetak otomatis pakai KOP Anda.'],
        ['icon'=>'💾','title'=>'Backup & Export','desc'=>'Download semua data kelas dalam ZIP — Excel rekap, file materi, submission mahasiswa.'],
      ];
      @endphp
      @foreach($features as $f)
      <div class="feat-card">
        <div class="feat-icon">{{ $f['icon'] }}</div>
        <h3>{{ $f['title'] }}</h3>
        <p>{{ $f['desc'] }}</p>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- HOW IT WORKS --}}
<section class="section" id="cara-kerja" style="background:var(--sky);">
  <div class="wrap">
    <div class="section-head">
      <h2>Mulai dalam 3 langkah</h2>
      <p>Setup cepat, langsung produktif.</p>
    </div>
    <div class="steps">
      <div class="step">
        <div class="num">01</div>
        <h3>Daftar & Setup Profil</h3>
        <p>Buat akun, isi profil & info kampus Anda. Komponen nilai default sudah tersedia otomatis.</p>
      </div>
      <div class="step">
        <div class="num">02</div>
        <h3>Kelola Perkuliahan</h3>
        <p>Buat kelas, generate pertemuan, upload materi, buat tugas, absensi via QR atau manual.</p>
      </div>
      <div class="step">
        <div class="num">03</div>
        <h3>Rekap & Cetak</h3>
        <p>Nilai terekap otomatis, export Excel, cetak dokumen resmi lengkap dengan KOP surat.</p>
      </div>
    </div>
  </div>
</section>

{{-- INSTITUTION --}}
<section class="section" id="institusi">
  <div class="wrap">
    <div class="inst">
      <div>
        <h2>Dirancang untuk institusi pendidikan</h2>
        <p>Setiap dosen punya ruang kerja sendiri. Data terisolasi, aman, dan hanya bisa diakses oleh dosen yang bersangkutan.</p>
        <div class="inst-badge">
          <img src="{{ asset('images/logo_kampus.png') }}" height="20" alt="AMIK" style="border-radius:4px;">
          AMIK Mahaputra Riau — Pengguna Pertama
        </div>
      </div>
      <div class="inst-visual">
        <div class="row"><span>Dosen aktif</span><span class="v">Unlimited</span></div>
        <div class="row"><span>Storage per dosen</span><span class="v">200 MB</span></div>
        <div class="row"><span>Kelas per dosen</span><span class="v">Unlimited</span></div>
        <div class="row"><span>Keamanan data</span><span class="v">HTTPS + Auth</span></div>
        <div class="row"><span>Verifikasi email</span><span class="v">✓ Aktif</span></div>
        <div class="row"><span>Backup data</span><span class="v">ZIP Download</span></div>
      </div>
    </div>
  </div>
</section>

{{-- FINAL CTA --}}
<section class="section">
  <div class="wrap">
    <div class="final-cta">
      <h2>Siap mengajar lebih efisien?</h2>
      <p>Bergabung dengan dosen-dosen yang sudah meninggalkan Excel manual.</p>
      <div class="hero-cta">
        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Daftar Sebagai Dosen</a>
        <a href="{{ route('login') }}" class="btn btn-ghost btn-lg">Sudah punya akun?</a>
      </div>
    </div>
  </div>
</section>

{{-- FOOTER --}}
<footer>
  <div class="wrap footer-inner">
    <div class="footer-brand">
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
      <span style="font-family:'Sora',sans-serif;font-weight:700;font-size:16px;color:var(--ink);">
        Lectra<span style="color:var(--blue);">Desk</span>
      </span>
    </div>
    <span>Teach Better. Manage Smarter. &copy; {{ date('Y') }}</span>
    <div class="footer-links">
      <a href="{{ route('login') }}">Login</a>
      <a href="{{ route('register') }}">Daftar</a>
      <a href="{{ route('student.index') }}">Portal Mahasiswa</a>
    </div>
  </div>
</footer>

</body>
</html>