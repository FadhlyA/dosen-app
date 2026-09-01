@extends('layouts.app')
@section('title', 'Admin Panel')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 style="font-family:'Sora',sans-serif;font-weight:700;margin:0;">⚙️ Admin Panel</h4>
        <p style="color:var(--ld-slate);font-size:0.85rem;margin:0;">Kelola semua dosen yang terdaftar</p>
    </div>
    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-secondary">← Dashboard</a>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-center p-3" style="border-top:3px solid #2563EB;">
            <div style="font-size:1.75rem;">👨‍🏫</div>
            <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1.6rem;color:#2563EB;">{{ $totalDosen }}</div>
            <div style="font-size:0.78rem;color:var(--ld-slate);">Total Dosen Terdaftar</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center p-3" style="border-top:3px solid #0891B2;">
            <div style="font-size:1.75rem;">💾</div>
            <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1.6rem;color:#0891B2;">
                {{ round($totalStorage / 1048576, 1) }} MB
            </div>
            <div style="font-size:0.78rem;color:var(--ld-slate);">Total Storage Terpakai</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center p-3" style="border-top:3px solid #16A34A;">
            <div style="font-size:1.75rem;">🆕</div>
            <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1.6rem;color:#16A34A;">{{ $recentDosen->count() }}</div>
            <div style="font-size:0.78rem;color:var(--ld-slate);">Dosen Terbaru</div>
        </div>
    </div>
</div>

{{-- Menu Cepat --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <a href="{{ route('admin.dosens') }}" class="text-decoration-none">
            <div class="card p-4 text-center" style="border-top:3px solid #2563EB;transition:transform 0.15s;"
                 onmouseover="this.style.transform='translateY(-2px)'"
                 onmouseout="this.style.transform=''">
                <div style="font-size:2rem;">👨‍🏫</div>
                <h6 style="font-family:'Sora',sans-serif;font-weight:700;color:var(--ld-ink);margin-top:8px;">Kelola Dosen</h6>
                <small style="color:var(--ld-slate);">Lihat, hapus, atur storage dosen</small>
            </div>
        </a>
    </div>
    <div class="col-md-6">
        <a href="{{ route('dashboard') }}" class="text-decoration-none">
            <div class="card p-4 text-center" style="border-top:3px solid #16A34A;transition:transform 0.15s;"
                 onmouseover="this.style.transform='translateY(-2px)'"
                 onmouseout="this.style.transform=''">
                <div style="font-size:2rem;">🏠</div>
                <h6 style="font-family:'Sora',sans-serif;font-weight:700;color:var(--ld-ink);margin-top:8px;">Dashboard Dosen</h6>
                <small style="color:var(--ld-slate);">Kembali ke dashboard dosen</small>
            </div>
        </a>
    </div>
</div>

{{-- Dosen Terbaru --}}
<div class="card">
    <div class="card-header bg-dark text-white" style="font-weight:600;">🕐 Dosen Terbaru Mendaftar</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background:var(--ld-sky);">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>No HP</th>
                        <th>Daftar</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentDosen as $dosen)
                    <tr>
                        <td style="font-weight:600;">{{ $dosen->name }}</td>
                        <td style="color:var(--ld-slate);font-size:0.85rem;">{{ $dosen->email }}</td>
                        <td>
                            @if($dosen->phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $dosen->phone) }}"
                                   target="_blank" style="color:#16A34A;font-size:0.85rem;">
                                    📱 {{ $dosen->phone }}
                                </a>
                            @else
                                <span style="color:var(--ld-slate);">-</span>
                            @endif
                        </td>
                        <td style="color:var(--ld-slate);font-size:0.85rem;">
                            {{ $dosen->created_at->format('d M Y') }}
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.dosen-detail', $dosen) }}"
                               class="btn btn-sm" style="background:var(--ld-sky);color:var(--ld-blue);">👁️ Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4" style="color:var(--ld-slate);">Belum ada dosen terdaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection