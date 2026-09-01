@extends('layouts.app')
@section('title', 'Ganti Password')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 style="font-family:'Sora',sans-serif;font-weight:700;margin:0;">🔒 Ganti Password</h4>
        <p style="color:var(--ld-slate);font-size:0.85rem;margin:0;">Perbarui password akun Anda</p>
    </div>
    <a href="{{ route('profile.index') }}" class="btn btn-sm btn-secondary">← Profil</a>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white" style="font-weight:600;">🔑 Form Ganti Password</div>
            <div class="card-body">
                <form action="{{ route('profile.update-password') }}" method="POST" id="changePasswordForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Password Lama</label>
                        <input type="password" name="current_password" id="currentPassword"
                               class="form-control @error('current_password') is-invalid @enderror"
                               placeholder="Masukkan password lama" required>
                        @error('current_password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="new_password" id="newPassword"
                               class="form-control @error('new_password') is-invalid @enderror"
                               placeholder="Minimal 8 karakter" required>
                        @error('new_password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <div class="mt-1" id="strengthBox"></div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="new_password_confirmation" id="confirmPassword"
                               class="form-control" placeholder="Ulangi password baru" required>
                        <div class="mt-1" id="matchBox"></div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">🔒 Ganti Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('newPassword').addEventListener('input', function() {
    const val = this.value;
    let score = 0;
    if(val.length>=8) score++;
    if(/[A-Z]/.test(val)) score++;
    if(/[0-9]/.test(val)) score++;
    if(/[^A-Za-z0-9]/.test(val)) score++;
    const labels = ['','⚠️ Lemah','🟡 Sedang','💪 Kuat','🔒 Sangat Kuat'];
    const colors = ['','#DC2626','#D97706','#16A34A','#16A34A'];
    const box = document.getElementById('strengthBox');
    box.innerHTML = val ? `<span style="font-size:0.78rem;color:${colors[score]};">${labels[score]}</span>` : '';
    checkMatch();
});

document.getElementById('confirmPassword').addEventListener('input', checkMatch);

function checkMatch() {
    const p = document.getElementById('newPassword').value;
    const c = document.getElementById('confirmPassword').value;
    const box = document.getElementById('matchBox');
    if (!c) { box.innerHTML=''; return; }
    box.innerHTML = p===c
        ? '<span style="font-size:0.78rem;color:#16A34A;">✅ Password cocok</span>'
        : '<span style="font-size:0.78rem;color:#DC2626;">❌ Password tidak cocok</span>';
}

document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const p = document.getElementById('newPassword').value;
    const c = document.getElementById('confirmPassword').value;
    if (p !== c) { Swal.fire({title:'Password tidak cocok!',icon:'error'}); return; }
    Swal.fire({
        title:'Ganti Password?', icon:'warning',
        showCancelButton:true, confirmButtonText:'Ya, Ganti!',
        cancelButtonText:'Batal', confirmButtonColor:'#2563EB'
    }).then(r => { if (r.isConfirmed) form.submit(); });
});
</script>
@endpush