@extends('layouts.app')
@section('title', 'Kelola Dosen')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 style="font-family:'Sora',sans-serif;font-weight:700;margin:0;">👨‍🏫 Kelola Dosen</h4>
        <p style="color:var(--ld-slate);font-size:0.85rem;margin:0;">{{ $dosens->count() }} dosen terdaftar</p>
    </div>
    <a href="{{ route('admin.index') }}" class="btn btn-sm btn-secondary">← Admin Panel</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="dosenTable">
                <thead style="background:var(--ld-sky);">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>No HP</th>
                        <th>Storage</th>
                        <th class="text-center">Kelas</th>
                        <th>Daftar</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dosens as $dosen)
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
                        <td style="min-width:140px;">
                            <div class="progress mb-1" style="height:5px;">
                                <div class="progress-bar"
                                     style="width:{{ $dosen->storage_percentage }}%;
                                            background:{{ $dosen->storage_percentage>=90?'#DC2626':($dosen->storage_percentage>=70?'#D97706':'#16A34A') }};">
                                </div>
                            </div>
                            <small style="color:var(--ld-slate);">
                                {{ $dosen->storage_used_readable }} / {{ $dosen->storage_limit_readable }}
                            </small>
                        </td>
                        <td class="text-center">
                            <span style="font-size:0.78rem;background:var(--ld-sky);color:var(--ld-blue);padding:3px 10px;border-radius:20px;font-weight:600;">
                                {{ $dosen->courses()->count() }}
                            </span>
                        </td>
                        <td style="color:var(--ld-slate);font-size:0.85rem;">
                            {{ $dosen->created_at->format('d M Y') }}
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('admin.dosen-detail', $dosen) }}"
                                   class="btn btn-sm" style="background:var(--ld-sky);color:var(--ld-blue);">👁️</a>
                                <form action="{{ route('admin.dosen-destroy', $dosen) }}" method="POST"
                                      onsubmit="return confirmHapusDosen(event, '{{ addslashes($dosen->name) }}')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4" style="color:var(--ld-slate);">Belum ada dosen terdaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#dosenTable').DataTable({
        language:{ search:"🔍 Cari:", lengthMenu:"Tampilkan _MENU_ data", info:"_START_-_END_ dari _TOTAL_", paginate:{first:"«",last:"»",next:"›",previous:"‹"} },
        columnDefs:[{ orderable:false, targets:-1 }],
        pageLength:25,
    });
});

function confirmHapusDosen(e, name) {
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