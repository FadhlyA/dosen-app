@extends('layouts.app')

@section('title', 'Detail Mahasiswa - ' . $student->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-0">👤 Detail Mahasiswa</h4>
        <small class="text-muted">{{ $course->name }} | {{ $course->class_name }}</small>
    </div>
    <a href="{{ route('grades.course', $course) }}" class="btn btn-secondary btn-sm">← Kembali ke Rekap</a>
</div>

<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm border-primary">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0 fw-bold">👤 Info Mahasiswa</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr><th>NIM</th><td>{{ $student->nim }}</td></tr>
                    <tr><th>Nama</th><td>{{ $student->name }}</td></tr>
                    <tr><th>Email</th><td>{{ $student->email ?? '-' }}</td></tr>
                </table>
                <hr>
                <button class="btn btn-warning btn-sm w-100"
                        onclick="openEditMhs({{ $student->id }}, '{{ $student->nim }}', '{{ addslashes($student->name) }}', '{{ $student->email }}')">
                    ✏️ Edit Data Mahasiswa
                </button>
            </div>
        </div>

        <div class="card shadow-sm mt-3 border-success">
            <div class="card-body text-center">
                <h6 class="text-muted">Nilai Akhir</h6>
                <h1 class="fw-bold text-primary">{{ $finalScore }}</h1>
                <span class="badge fs-4
                    {{ $letterGrade == 'A' ? 'bg-success' :
                      ($letterGrade == 'B' ? 'bg-primary' :
                      ($letterGrade == 'C' ? 'bg-warning text-dark' :
                      ($letterGrade == 'D' ? 'bg-secondary' : 'bg-danger'))) }}">
                    {{ $letterGrade }}
                </span>
                <hr>
                <small class="text-muted">Total Bobot: {{ $totalWeight }}%</small>
            </div>
        </div>

        @if($otherCourses->isNotEmpty())
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0 fw-bold">📚 Terdaftar di Kelas Lain</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($otherCourses as $other)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold small">{{ $other->course->name }}</div>
                            <small class="text-muted">{{ $other->course->class_name }}</small>
                        </div>
                        <a href="{{ route('grades.student-detail', [$other->course, $student->nim]) }}"
                           class="btn btn-outline-info btn-sm">Lihat</a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-warning">
                <h6 class="mb-0 fw-bold">📊 Nilai Per Komponen</h6>
            </div>
            <div class="card-body">
                @if($components->isEmpty())
                    <div class="alert alert-info mb-0">Belum ada komponen nilai.</div>
                @else
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Komponen</th>
                                <th>Bobot</th>
                                <th>Nilai</th>
                                <th>Kontribusi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($components as $component)
                            @php $grade = $component->grades->first(); @endphp
                            <tr>
                                <td class="fw-bold">{{ $component->name }}</td>
                                <td>{{ $component->weight }}%</td>
                                <td>
                                    @if($grade)
                                        <span class="fw-bold text-primary">{{ $grade->score }}</span>
                                    @else
                                        <span class="text-muted">Belum dinilai</span>
                                    @endif
                                </td>
                                <td>
                                    @if($grade)
                                        {{ round($grade->score * $component->weight / 100, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning"
                                            onclick="openEditGrade({{ $component->id }}, '{{ $component->name }}', {{ $grade ? $grade->score : 0 }})">
                                        ✏️ Edit
                                    </button>
                                    @if($grade)
                                    <form action="{{ route('grades.destroy-grade', [$course, $component, $grade]) }}"
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('Hapus nilai {{ $component->name }}?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger">🗑️</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <th colspan="2">Nilai Akhir</th>
                                <th colspan="3">{{ $finalScore }} ({{ $letterGrade }})</th>
                            </tr>
                        </tfoot>
                    </table>
                @endif
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h6 class="mb-0 fw-bold">📥 Riwayat Pengumpulan Tugas</h6>
            </div>
            <div class="card-body">
                @if($submissions->isEmpty())
                    <div class="alert alert-info mb-0">Belum ada tugas yang dikumpulkan.</div>
                @else
                    <table class="table table-bordered table-hover">
                        <thead class="table-secondary">
                            <tr>
                                <th>Pertemuan</th>
                                <th>Tugas</th>
                                <th>Waktu Kumpul</th>
                                <th>Catatan</th>
                                <th>File</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($submissions as $submission)
                            <tr>
                                <td>Pertemuan {{ $submission->assignment->meeting->meeting_number }}</td>
                                <td>{{ $submission->assignment->title }}</td>
                                <td>{{ $submission->created_at->format('d M Y H:i') }}</td>
                                <td>{{ $submission->note ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('submissions.download', [
                                            $course,
                                            $submission->assignment->meeting,
                                            $submission->assignment,
                                            $submission
                                        ]) }}"
                                       class="btn btn-sm btn-primary">⬇️</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit Nilai --}}
<div class="modal fade" id="editGradeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold">✏️ Edit Nilai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editGradeForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Komponen</label>
                        <input type="text" id="editComponentName" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nilai (0-100)</label>
                        <input type="number" name="score" id="editScore" class="form-control"
                               min="0" max="100" step="0.01" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Update Nilai</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- Modal Edit Data Mahasiswa --}}
<div class="modal fade" id="editMhsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold">✏️ Edit Data Mahasiswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editMhsForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">NIM</label>
                        <input type="text" name="nim" id="editMhsNim" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" name="name" id="editMhsName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email <span class="text-muted">(opsional)</span></label>
                        <input type="email" name="email" id="editMhsEmail" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-bold" id="editMhsSubmit">💾 Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
// Buka modal edit data mahasiswa
function openEditMhs(id, nim, name, email) {
    const baseUrl = "{{ url('/courses/' . $course->id . '/students/') }}";
    document.getElementById('editMhsForm').action = baseUrl + id;
    document.getElementById('editMhsNim').value = nim;
    document.getElementById('editMhsName').value = name;
    document.getElementById('editMhsEmail').value = (email && email !== 'null') ? email : '';
    new bootstrap.Modal(document.getElementById('editMhsModal')).show();
}

// Konfirmasi update data mahasiswa
document.getElementById('editMhsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    Swal.fire({
        title: 'Konfirmasi Perubahan',
        text: 'Apakah Anda yakin ingin mengubah data mahasiswa ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f0ad4e',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Ubah!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
});

// Edit nilai
function openEditGrade(componentId, componentName, currentScore) {
    const baseUrl = "{{ url('/grades/' . $course->id . '/components') }}";
    const nim = "{{ $student->nim }}";
    document.getElementById('editGradeForm').action = baseUrl + '/' + componentId + '/student/' + nim;
    document.getElementById('editComponentName').value = componentName;
    document.getElementById('editScore').value = currentScore;
    new bootstrap.Modal(document.getElementById('editGradeModal')).show();
}

// Konfirmasi update nilai
document.getElementById('editGradeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    Swal.fire({
        title: 'Konfirmasi Perubahan Nilai',
        text: 'Apakah Anda yakin ingin mengubah nilai ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f0ad4e',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Ubah!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
});
</script>
@endpush