@extends('layouts.app')
@section('title', 'Profil Saya')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 style="font-family:'Sora',sans-serif;font-weight:700;margin:0;">👤 Profil Saya</h4>
        <p style="color:var(--ld-slate);font-size:0.85rem;margin:0;">Kelola informasi pribadi & institusi</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('profile.change-password') }}" class="btn btn-sm"
           style="background:#F5F3FF;color:#7C3AED;">🔒 Ganti Password</a>
        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-secondary">← Dashboard</a>
    </div>
</div>

<div class="row g-4">
    {{-- Kolom Kiri: Foto & Info Singkat --}}
    <div class="col-md-4">

        {{-- Foto Profil --}}
        <div class="card mb-3">
            <div class="card-header bg-dark text-white" style="font-weight:600;">📷 Foto Profil</div>
            <div class="card-body text-center p-4">
                @if($profile && $profile->photo)
                    <img src="{{ Storage::url($profile->photo) }}"
                         class="rounded-circle mb-3"
                         style="width:100px;height:100px;object-fit:cover;border:3px solid var(--ld-sky);">
                @else
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                         style="width:100px;height:100px;background:var(--ld-sky);border:3px solid var(--ld-border);">
                        <span style="font-size:2.5rem;">👤</span>
                    </div>
                @endif
                <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1rem;">
                    {{ auth()->user()->full_name }}
                </div>
                <div style="font-size:0.8rem;color:var(--ld-slate);">{{ auth()->user()->email }}</div>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="mt-3">
                    @csrf
                    <input type="file" name="photo" id="photoInput" class="d-none" accept=".jpg,.jpeg,.png"
                           onchange="this.form.submit()">
                    <label for="photoInput" class="btn btn-sm btn-primary w-100" style="cursor:pointer;">
                        📷 Ganti Foto
                    </label>
                </form>

                @if($profile && $profile->photo)
                <form action="{{ route('profile.delete-photo') }}" method="POST" class="mt-2"
                      onsubmit="return confirm('Hapus foto profil?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger w-100">🗑️ Hapus Foto</button>
                </form>
                @endif
            </div>
        </div>

        {{-- Storage --}}
        <div class="card">
            <div class="card-header bg-dark text-white" style="font-weight:600;">💾 Storage</div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-1" style="font-size:0.8rem;">
                    <span style="color:var(--ld-slate);">Terpakai</span>
                    <span style="font-weight:600;">{{ auth()->user()->storage_used_readable }}</span>
                </div>
                <div class="progress mb-1" style="height:8px;">
                    <div class="progress-bar"
                         style="width:{{ auth()->user()->storage_percentage }}%;
                                background:{{ auth()->user()->storage_percentage >= 90 ? '#DC2626' : (auth()->user()->storage_percentage >= 70 ? '#D97706' : '#16A34A') }};">
                    </div>
                </div>
                <div style="font-size:0.75rem;color:var(--ld-slate);">
                    {{ auth()->user()->storage_percentage }}% dari {{ auth()->user()->storage_limit_readable }}
                </div>
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Form --}}
    <div class="col-md-8">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @if(session('success'))
            <div class="alert alert-success py-2 mb-3">✅ {{ session('success') }}</div>
            @endif
            @if($errors->any())
            <div class="alert alert-danger py-2 mb-3">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
            @endif

            {{-- Data Pribadi --}}
            <div class="card mb-3">
                <div class="card-header bg-primary text-white" style="font-weight:600;">👤 Data Pribadi</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Gelar Depan</label>
                            <input type="text" name="front_title" class="form-control"
                                   placeholder="Dr." value="{{ old('front_title', $profile->front_title ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control"
                                   value="{{ old('name', auth()->user()->name) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Gelar Belakang</label>
                            <input type="text" name="back_title" class="form-control"
                                   placeholder="M.Kom" value="{{ old('back_title', $profile->back_title ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" class="form-control"
                                   placeholder="Nomor Induk Pegawai"
                                   value="{{ old('nip', $profile->nip ?? '') }}"
                                   style="font-family:'JetBrains Mono',monospace;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIDN</label>
                            <input type="text" name="nidn" class="form-control"
                                   placeholder="Nomor Induk Dosen Nasional"
                                   value="{{ old('nidn', $profile->nidn ?? '') }}"
                                   style="font-family:'JetBrains Mono',monospace;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Program Studi</label>
                            <input type="text" name="study_program" class="form-control"
                                   placeholder="Teknik Informatika"
                                   value="{{ old('study_program', $profile->study_program ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="position" class="form-control"
                                   placeholder="Dosen Tetap"
                                   value="{{ old('position', $profile->position ?? '') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">No HP / WhatsApp</label>
                            <input type="text" name="phone" class="form-control"
                                   placeholder="08xxxxxxxxxx"
                                   value="{{ old('phone', auth()->user()->phone ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info Institusi --}}
            <div class="card mb-3">
                <div class="card-header bg-success text-white" style="font-weight:600;">🏛️ Info Institusi / Kampus</div>
                <div class="card-body">
                    <div class="alert alert-info py-2 small mb-3">
                        ℹ️ Info ini digunakan sebagai KOP surat di semua dokumen cetak.
                    </div>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nama Institusi</label>
                            <input type="text" name="institution_name" class="form-control"
                                   placeholder="AMIK Mahaputra Riau"
                                   value="{{ old('institution_name', $profile->institution_name ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">No Telp Kampus</label>
                            <input type="text" name="institution_phone" class="form-control"
                                   placeholder="0853-xxxx-xxxx"
                                   value="{{ old('institution_phone', $profile->institution_phone ?? '') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat Kampus</label>
                            <textarea name="institution_address" class="form-control" rows="2"
                                      placeholder="Jl. ...">{{ old('institution_address', $profile->institution_address ?? '') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Kampus</label>
                            <input type="email" name="institution_email" class="form-control"
                                   placeholder="info@kampus.ac.id"
                                   value="{{ old('institution_email', $profile->institution_email ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Website Kampus</label>
                            <input type="text" name="institution_website" class="form-control"
                                   placeholder="www.kampus.ac.id"
                                   value="{{ old('institution_website', $profile->institution_website ?? '') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Logo Kampus</label>
                            @if($profile && $profile->institution_logo)
                                <div class="mb-2 d-flex align-items-center gap-3">
                                    <img src="{{ Storage::url($profile->institution_logo) }}"
                                         height="48" alt="Logo" class="border rounded p-1">
                                    <small style="color:var(--ld-slate);">Logo saat ini</small>
                                </div>
                            @endif
                            <input type="file" name="institution_logo" class="form-control"
                                   accept=".jpg,.jpeg,.png">
                            <small style="color:var(--ld-slate);">Format JPG/PNG, maks 2MB. Dipakai di KOP surat dokumen cetak.</small>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary fw-bold w-100">💾 Simpan Profil</button>
        </form>
    </div>
</div>
@endsection