@extends('layouts.app')
@section('title', 'Detail Dosen')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 style="font-family:'Sora',sans-serif;font-weight:700;margin:0;">👤 Detail Dosen</h4>
        <p style="color:var(--ld-slate);font-size:0.85rem;margin:0;">{{ $user->name }}</p>
    </div>
    <a href="{{ route('admin.dosens') }}" class="btn btn-sm btn-secondary">← Kelola Dosen</a>
</div>

<div class="row g-3">
    <div class="col-md-4">

        {{-- Info Dosen --}}
        <div class="card mb-3">
            <div class="card-header bg-primary text-white" style="font-weight:600;">👤 Info Dosen</div>
            <div class="card-body">
                @if($user->profile && $user->profile->photo)
                <div class="text-center mb-3">
                    <img src="{{ Storage::url($user->profile->photo) }}"
                         class="rounded-circle"
                         style="width:72px;height:72px;object-fit:cover;border:3px solid var(--ld-sky);">
                </div>
                @endif
                <table style="width:100%;font-size:0.85rem;border-collapse:collapse;">
                    <tr><td style="color:var(--ld-slate);padding:4px 0;width:90px;">Nama</td><td style="font-weight:600;">{{ $user->name }}</td></tr>
                    <tr><td style="color:var(--ld-slate);padding:4px 0;">Email</td><td>{{ $user->email }}</td></tr>
                    <tr><td style="color:var(--ld-slate);padding:4px 0;">No HP</td>
                        <td>
                            @if($user->phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}"
                                   target="_blank" style="color:#16A34A;">📱 {{ $user->phone }}</a>
                            @else
                                <span style="color:var(--ld-slate);">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr><td style="color:var(--ld-slate);padding:4px 0;">NIP</td><td>{{ $user->profile->nip ?? '-' }}</td></tr>
                    <tr><td style="color:var(--ld-slate);padding:4px 0;">NIDN</td><td style="font-family:'JetBrains Mono',monospace;">{{ $user->profile->nidn ?? '-' }}</td></tr>
                    <tr><td style="color:var(--ld-slate);padding:4px 0;">Prodi</td><td>{{ $user->profile->study_program ?? '-' }}</td></tr>
                    <tr><td style="color:var(--ld-slate);padding:4px 0;">Jabatan</td><td>{{ $user->profile->position ?? '-' }}</td></tr>
                    <tr><td style="color:var(--ld-slate);padding:4px 0;">Daftar</td><td>{{ $user->created_at->format('d M Y') }}</td></tr>
                </table>
            </div>
        </div>

        {{-- Storage --}}
        <div class="card mb-3">
            <div class="card-header bg-info text-white" style="font-weight:600;">💾 Storage</div>
            <div class="card-body">
                <div class="progress mb-2" style="height:8px;">
                    <div class="progress-bar"
                         style="width:{{ $user->storage_percentage }}%;
                                background:{{ $user->storage_percentage>=90?'#DC2626':($user->storage_percentage>=70?'#D97706':'#16A34A') }};">
                    </div>
                </div>
                <small style="color:var(--ld-slate);">
                    {{ $user->storage_used_readable }} / {{ $user->storage_limit_readable }}
                    ({{ $user->storage_percentage }}%)
                </small>
                <hr style="border-color:var(--ld-border);">
                <form action="{{ route('admin.update-storage', $user) }}" method="POST">
                    @csrf
                    <label class="form-label" style="font-size:0.8rem;font-weight:600;">Ubah Storage Limit</label>
                    <select name="storage_limit" class="form-select form-select-sm mb-2">
                        <option value="209715200" {{ $user->storage_limit==209715200?'selected':'' }}>200 MB</option>
                        <option value="524288000" {{ $user->storage_limit==524288000?'selected':'' }}>500 MB</option>
                        <option value="1073741824" {{ $user->storage_limit==1073741824?'selected':'' }}>1 GB</option>
                        <option value="2147483648" {{ $user->storage_limit==2147483648?'selected':'' }}>2 GB</option>
                    </select>
                    <button type="submit" class="btn btn-info btn-sm w-100 text-white">💾 Update Limit</button>
                </form>
            </div>
        </div>

        {{-- Hapus --}}
        <div class="card border-danger">
            <div class="card-body">
                <form action="{{ route('admin.dosen-destroy', $user) }}" method="POST"
                      onsubmit="return confirmHapus(event, '{{ addslashes($user->name) }}')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger w-100">🗑️ Hapus Akun Dosen</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Daftar Kelas --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <span style="font-weight:600;">📚 Kelas yang Dimiliki</span>
                <span style="font-size:0.78rem;opacity:0.7;">{{ $totalCourses }} kelas · {{ $totalStudents }} mahasiswa</span>
            </div>
            <div class="card-body p-0">
                @if($courses->isEmpty())
                    <div class="text-center py-4" style="color:var(--ld-slate);"><small>Belum ada kelas.</small></div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background:var(--ld-sky);">
                            <tr>
                                <th>Nama Matkul</th>
                                <th>Kelas</th>
                                <th>Semester</th>
                                <th class="text-center">Mahasiswa</th>
                                <th class="text-center">Pertemuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                            <tr>
                                <td style="font-weight:600;">{{ $course->name }}</td>
                                <td style="color:var(--ld-slate);">{{ $course->class_name }}</td>
                                <td style="color:var(--ld-slate);font-size:0.85rem;">{{ $course->semester }}</td>
                                <td class="text-center">
                                    <span style="font-size:0.75rem;background:var(--ld-sky);color:var(--ld-blue);padding:2px 8px;border-radius:20px;font-weight:600;">
                                        {{ $course->students_count }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span style="font-size:0.75rem;background:var(--ld-sky);color:var(--ld-blue);padding:2px 8px;border-radius:20px;font-weight:600;">
                                        {{ $course->meetings_count }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmHapus(e, name) {
    e.preventDefault();
    const form = e.target.closest('form');
    Swal.fire({
        title:'Hapus Akun Dosen?',
        text: name + ' dan semua datanya akan dihapus permanen!',
        icon:'error', showCancelButton:true,
        confirmButtonColor:'#DC2626', cancelButtonColor:'#64748B',
        confirmButtonText:'Ya, Hapus!', cancelButtonText:'Batal'
    }).then(r => { if (r.isConfirmed) form.submit(); });
}
</script>
@endpush