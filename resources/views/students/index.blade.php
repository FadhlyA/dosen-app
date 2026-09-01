@extends('layouts.app')
@section('title', 'Daftar Mahasiswa - ' . $course->name)
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h4 style="font-family:'Sora',sans-serif;font-weight:700;margin:0;">👥 Daftar Mahasiswa</h4>
        <p style="color:var(--ld-slate);font-size:0.85rem;margin:0;">
            {{ $course->name }} · {{ $course->class_name }} · {{ $course->semester }}
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            + Tambah
        </button>
        <button class="btn btn-sm" style="background:var(--ld-sky);color:var(--ld-blue);"
                data-bs-toggle="modal" data-bs-target="#importModal">
            📂 Import CSV
        </button>
        <a href="{{ route('students.export-excel', $course) }}" class="btn btn-sm btn-success">📊 Excel</a>
        <a href="{{ route('courses.show', $course) }}" class="btn btn-sm btn-secondary">← Kembali</a>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center p-3" style="border-top:3px solid #2563EB;">
            <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1.6rem;color:#2563EB;">{{ $students->count() }}</div>
            <div style="font-size:0.78rem;color:var(--ld-slate);">Total Mahasiswa</div>
        </div>
    </div>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="card-header bg-dark text-white" style="font-weight:600;">
        👥 Daftar Mahasiswa Terdaftar
    </div>
    <div class="card-body p-0">
        @if($students->isEmpty())
            <div class="text-center py-5" style="color:var(--ld-slate);">
                <div style="font-size:2.5rem;">👥</div>
                <p class="mt-2 mb-3">Belum ada mahasiswa.</p>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                    + Tambah Mahasiswa
                </button>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="studentTable">
                <thead style="background:var(--ld-sky);">
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr>
                        <td style="font-family:'JetBrains Mono',monospace;font-size:0.85rem;font-weight:600;color:var(--ld-blue);">
                            {{ $student->nim }}
                        </td>
                        <td style="font-weight:600;">{{ $student->name }}</td>
                        <td style="color:var(--ld-slate);font-size:0.85rem;">{{ $student->email ?? '-' }}</td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <button class="btn btn-sm" style="background:var(--ld-sky);color:var(--ld-blue);"
                                        onclick="openEdit('{{ $student->id }}','{{ addslashes($student->nim) }}','{{ addslashes($student->name) }}','{{ addslashes($student->email ?? '') }}')">
                                    ✏️
                                </button>
                                <form action="{{ route('students.destroy', [$course, $student]) }}"
                                      method="POST"
                                      onsubmit="return confirmHapus(event, '{{ addslashes($student->name) }}')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">+ Tambah Mahasiswa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('students.store', $course) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">NIM</label>
                        <input type="text" name="nim" class="form-control"
                               style="font-family:'JetBrains Mono',monospace;font-size:1rem;"
                               placeholder="Nomor Induk Mahasiswa" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama mahasiswa" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email <span style="color:var(--ld-slate);font-weight:400;">(opsional)</span></label>
                        <input type="email" name="email" class="form-control" placeholder="email@mahasiswa.ac.id">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">+ Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold">✏️ Edit Mahasiswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">NIM</label>
                        <input type="text" name="nim" id="editNim" class="form-control"
                               style="font-family:'JetBrains Mono',monospace;" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="editEmail" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-bold">💾 Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Import --}}
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">📂 Import Mahasiswa CSV</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('students.import', $course) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info py-2 small mb-3">
                        Format CSV: <code>nim, nama, email</code><br>
                        Email bersifat opsional.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File CSV</label>
                        <input type="file" name="csv_file" class="form-control" accept=".csv,.txt" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">📂 Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#studentTable').DataTable({
        language:{ search:"🔍 Cari:", lengthMenu:"Tampilkan _MENU_ data", info:"_START_-_END_ dari _TOTAL_", paginate:{first:"«",last:"»",next:"›",previous:"‹"} },
        columnDefs:[{ orderable:false, targets:-1 }],
        pageLength:25,
    });
});

function openEdit(id, nim, name, email) {
    document.getElementById('editNim').value   = nim;
    document.getElementById('editName').value  = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editForm').action = '/courses/{{ $course->id }}/students/' + id;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function confirmHapus(e, name) {
    e.preventDefault();
    const form = e.target.closest('form');
    Swal.fire({
        title:'Hapus Mahasiswa?',
        text: name + ' akan dihapus dari kelas ini.',
        icon:'warning', showCancelButton:true,
        confirmButtonColor:'#DC2626', cancelButtonColor:'#64748B',
        confirmButtonText:'Ya, Hapus!', cancelButtonText:'Batal'
    }).then(r => { if (r.isConfirmed) form.submit(); });
}
</script>
@endpush