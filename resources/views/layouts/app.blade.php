<!DOCTYPE html>
<html lang="id" data-theme="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LectraDesk — @yield('title', 'Dashboard')</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="icon" type="image/png" href="{{ asset('images/lectradesk/logo-icon-blue.png') }}">
</head>
<body>

{{-- Navbar --}}
<nav class="navbar navbar-expand-lg">
    <div class="container">

        {{-- Brand --}}
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
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
            <span>Lectra<span class="brand-desk">Desk</span></span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            {{-- Menu Kiri --}}
            <ul class="navbar-nav me-auto">
                @auth
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-bold' : '' }}"
                       href="{{ route('dashboard') }}">🏠 Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('courses.*') ? 'active fw-bold' : '' }}"
                       href="{{ route('courses.index') }}">📚 Kelas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('grades.*') ? 'active fw-bold' : '' }}"
                       href="{{ route('grades.index') }}">📊 Nilai</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('calendar.*') ? 'active fw-bold' : '' }}"
                       href="{{ route('calendar.index') }}">📅 Kalender</a>
                </li>
                @endauth
            </ul>

            {{-- Menu Kanan --}}
            <ul class="navbar-nav ms-auto align-items-center gap-1">
                @auth

                {{-- Storage indicator --}}
                <li class="nav-item d-none d-lg-block" style="width:140px;">
                    <div class="px-2">
                        <div class="d-flex justify-content-between mb-1">
                            <small style="color:rgba(255,255,255,0.6);font-size:0.7rem;">
                                💾 {{ auth()->user()->storage_used_readable }}
                            </small>
                            <small style="color:rgba(255,255,255,0.4);font-size:0.7rem;">
                                {{ auth()->user()->storage_limit_readable }}
                            </small>
                        </div>
                        <div class="progress" style="height:4px;background:rgba(255,255,255,0.15);">
                            <div class="progress-bar"
                                 style="width:{{ auth()->user()->storage_percentage }}%;
                                        background:{{ auth()->user()->storage_percentage >= 90 ? '#EF4444' : (auth()->user()->storage_percentage >= 70 ? '#F59E0B' : '#34D399') }};">
                            </div>
                        </div>
                    </div>
                </li>

                {{-- Dark Mode Toggle --}}
                <li class="nav-item">
                    <button class="btn btn-link nav-link px-2" onclick="toggleDarkMode()" id="darkModeBtn" title="Toggle Dark Mode">
                        🌙
                    </button>
                </li>

                {{-- User Dropdown --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                        @if(auth()->user()->profile && auth()->user()->profile->photo)
                            <img src="{{ Storage::url(auth()->user()->profile->photo) }}"
                                 width="28" height="28"
                                 class="rounded-circle object-fit-cover"
                                 style="border:2px solid rgba(255,255,255,0.3);">
                        @else
                            <div style="width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;font-size:0.8rem;border:2px solid rgba(255,255,255,0.3);">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                        <span class="d-none d-lg-inline" style="font-size:0.8rem;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            {{ auth()->user()->name }}
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="px-3 py-2">
                            <div style="font-family:'Sora',sans-serif;font-weight:600;font-size:0.875rem;color:var(--ld-ink);">
                                {{ auth()->user()->full_name }}
                            </div>
                            <div style="font-size:0.75rem;color:var(--ld-slate);">
                                {{ auth()->user()->email }}
                            </div>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><a class="dropdown-item" href="{{ route('dashboard') }}">🏠 Dashboard</a></li>
                        <li><a class="dropdown-item" href="{{ route('courses.index') }}">📚 Kelas Saya</a></li>
                        <li><a class="dropdown-item" href="{{ route('grades.index') }}">📊 Rekap Nilai</a></li>
                        <li><a class="dropdown-item" href="{{ route('calendar.index') }}">📅 Kalender</a></li>
                        <li><a class="dropdown-item" href="{{ route('backup.index') }}">💾 Backup Data</a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><a class="dropdown-item" href="{{ route('profile.index') }}">👤 Profil Saya</a></li>
                        <li><a class="dropdown-item" href="{{ route('profile.change-password') }}">🔒 Ganti Password</a></li>
                        @if(auth()->user()->is_admin)
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><a class="dropdown-item text-danger fw-bold" href="{{ route('admin.index') }}">⚙️ Admin Panel</a></li>
                        @endif
                        <li><hr class="dropdown-divider my-1"></li>

                        {{-- Storage di dropdown (mobile) --}}
                        <li class="px-3 py-2 d-lg-none">
                            <small style="color:var(--ld-slate);font-size:0.7rem;">
                                💾 {{ auth()->user()->storage_used_readable }} / {{ auth()->user()->storage_limit_readable }}
                            </small>
                            <div class="progress mt-1" style="height:4px;">
                                <div class="progress-bar bg-primary" style="width:{{ auth()->user()->storage_percentage }}%"></div>
                            </div>
                        </li>

                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">🚪 Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

{{-- Flash Messages --}}
<div class="container mt-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            ✅ {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            ❌ {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show">
            ⚠️ {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>

{{-- Main Content --}}
<main class="container my-4">
    @yield('content')
</main>

{{-- Footer --}}
<footer class="mt-5">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
        <img src="{{ asset('images/lectradesk/logo-icon.png') }}" height="24" alt="LectraDesk">
            <span style="font-family:'Sora',sans-serif;font-weight:600;color:#fff;">
                Lectra<span style="color:#60A5FA;">Desk</span>
            </span>
        </div>
        <span style="font-size:0.75rem;">Teach Better. Manage Smarter. &copy; {{ date('Y') }}</span>
    </div>
</footer>

{{-- Scripts --}}
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Dark Mode
function toggleDarkMode() {
    const html = document.documentElement;
    const btn  = document.getElementById('darkModeBtn');
    if (html.getAttribute('data-theme') === 'dark') {
        html.setAttribute('data-theme', '');
        btn.innerText = '🌙';
        localStorage.setItem('ld-theme', 'light');
    } else {
        html.setAttribute('data-theme', 'dark');
        btn.innerText = '☀️';
        localStorage.setItem('ld-theme', 'dark');
    }
}

const savedTheme = localStorage.getItem('ld-theme');
if (savedTheme === 'dark') {
    document.documentElement.setAttribute('data-theme', 'dark');
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('darkModeBtn');
        if (btn) btn.innerText = '☀️';
    });
}
</script>

@stack('scripts')
</body>
</html>