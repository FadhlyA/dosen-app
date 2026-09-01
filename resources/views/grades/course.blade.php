@extends('layouts.app')
@section('title', 'Rekap Nilai - ' . $course->name)
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h4 style="font-family:'Sora',sans-serif;font-weight:700;margin:0;">📊 Rekap Nilai</h4>
        <p style="color:var(--ld-slate);font-size:0.85rem;margin:0;">{{ $course->name }} · {{ $course->class_name }} · {{ $course->semester }}</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#inputNilaiModal">✏️ Input Nilai</button>
        <button class="btn btn-sm" style="background:#EFF6FF;color:#2563EB;" onclick="scrollToImport()">📂 Import</button>
        <a href="{{ route('grades.components', $course) }}" class="btn btn-sm btn-primary">⚙️ Komponen</a>
        <a href="{{ route('grades.grade-letters', $course) }}" class="btn btn-sm" style="background:#F5F3FF;color:#7C3AED;">🎓 Nilai Huruf</a>
        <a href="{{ route('grades.print', $course) }}" target="_blank" class="btn btn-sm btn-dark">🖨️ Cetak</a>
        <a href="{{ route('grades.export-excel', $course) }}" class="btn btn-sm btn-success">📊 Excel</a>
        <a href="{{ route('grades.export', $course) }}" class="btn btn-sm" style="background:#F0FDF4;color:#16A34A;">⬇️ CSV</a>
        <a href="{{ route('grades.index') }}" class="btn btn-sm btn-secondary">← Kembali</a>
    </div>
</div>

@if($students->isEmpty())
    <div class="alert alert-warning">⚠️ Belum ada mahasiswa. <a href="{{ route('students.index', $course) }}">Tambah dulu!</a></div>
@endif
@if($components->isEmpty())
    <div class="alert alert-info">⚠️ Belum ada komponen. <a href="{{ route('grades.components', $course) }}">Tambah dulu!</a></div>
@endif

<div class="card mb-4">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <span style="font-weight:600;">📋 Rekap Nilai Akhir</span>
        <span style="font-size:0.78rem;opacity:0.7;">{{ $students->count() }} Mahasiswa</span>
    </div>
    <div class="card-body p-0">
        @if(empty($finalGrades))
            <div class="text-center py-5" style="color:var(--ld-slate);"><small>Belum ada data nilai.</small></div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="tableGrades">
                <thead style="background:var(--ld-sky);">
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        @foreach($components as $c)
                            <th class="text-center">{{ $c->name }}<br><small style="color:var(--ld-slate);font-weight:400;">({{ $c->weight }}%)</small></th>
                        @endforeach
                        <th class="text-center">Akhir</th>
                        <th class="text-center">Huruf</th>
                        <th class="text-center">Absensi</th>
                        <th class="text-center">Tugas</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($finalGrades as $nim => $data)
                    <tr>
                        <td style="font-family:'JetBrains Mono',monospace;font-size:0.82rem;">{{ $nim }}</td>
                        <td style="font-weight:600;">{{ $data['name'] }}</td>
                        @foreach($components as $c)
                        <td class="text-center" style="font-size:0.85rem;">
                            @if($data['scores'][$c->id] !== null)
                                {{ $data['scores'][$c->id] }}
                            @else
                                <span style="color:var(--ld-slate);">-</span>
                            @endif
                        </td>
                        @endforeach
                        <td class="text-center"><strong>{{ $data['final'] }}</strong></td>
                        <td class="text-center">
                            @php $l = $data['letter']; @endphp
                            <span style="font-size:0.82rem;font-weight:700;padding:3px 10px;border-radius:20px;
                                background:{{ $l==='A'?'#F0FDF4':($l==='B'?'#EFF6FF':($l==='C'?'#FFFBEB':($l==='D'?'#F8FAFC':'#FEF2F2'))) }};
                                color:{{ $l==='A'?'#16A34A':($l==='B'?'#2563EB':($l==='C'?'#D97706':($l==='D'?'#64748B':'#DC2626'))) }};">
                                {{ $l }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span style="font-size:0.78rem;padding:3px 10px;border-radius:20px;font-weight:600;
                                background:{{ $data['absen_warn']?'#FEF2F2':'#F0FDF4' }};
                                color:{{ $data['absen_warn']?'#DC2626':'#16A34A' }};">
                                {{ $data['absen_pct'] }}%
                            </span>
                        </td>
                        <td class="text-center">
                            <span style="font-size:0.78rem;padding:3px 10px;border-radius:20px;font-weight:600;
                                background:{{ $data['tugas_kumpul']<$data['tugas_total']?'#FFFBEB':'#F0FDF4' }};
                                color:{{ $data['tugas_kumpul']<$data['tugas_total']?'#D97706':'#16A34A' }};">
                                {{ $data['tugas_kumpul'] }}/{{ $data['tugas_total'] }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('grades.student-detail', [$course, $nim]) }}"
                                   class="btn btn-sm" style="background:var(--ld-sky);color:var(--ld-blue);">👁️</a>
                                <button class="btn btn-sm btn-warning"
                                        onclick="openEditNilai('{{ $nim }}','{{ addslashes($data['name']) }}')">✏️</button>
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

{{-- Import Section --}}
<div id="importSection" class="card mt-4">
    <div class="card-header bg-primary text-white" style="font-weight:600;">📂 Import Nilai</div>
    <div class="card-body">
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabPetunjuk">📖 Petunjuk</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabUpload">📂 Upload CSV</button></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="tabPetunjuk">
                <ol style="font-size:0.875rem;padding-left:1.25rem;">
                    <li class="mb-1">Download template → isi nilai (0-100) di kolom komponen</li>
                    <li class="mb-1">Kolom <strong>nim</strong> & <strong>nama</strong> jangan diubah</li>
                    <li class="mb-1">Simpan sebagai <strong>.CSV</strong> → upload di tab Upload</li>
                </ol>
                <div class="alert alert-warning py-2 small mb-3">⚠️ Komponen <strong>Tugas</strong> & <strong>Absensi</strong> tidak ada di template — dihitung otomatis.</div>
                <a href="{{ route('grades.download-template', $course) }}" class="btn btn-success btn-sm">📥 Download Template CSV</a>
            </div>
            <div class="tab-pane fade" id="tabUpload">
                <form action="{{ route('grades.import', $course) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">File CSV</label>
                        <input type="file" name="csv_file" class="form-control" accept=".csv,.txt" required>
                    </div>
                    <button type="submit" class="btn btn-primary">📂 Import Nilai</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Input Nilai --}}
<div class="modal fade" id="inputNilaiModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold">✏️ Input Nilai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Mahasiswa</label>
                    <select id="inputNim" class="form-select">
                        <option value="">-- Pilih Mahasiswa --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->nim }}">{{ $student->nim }} - {{ $student->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Komponen</label>
                    <select id="inputComponent" class="form-select">
                        <option value="">-- Pilih Komponen --</option>
                        @foreach($components as $c)
                            @if(!$c->is_attendance && !$c->is_assignment_based)
                            <option value="{{ $c->id }}" data-url="{{ route('grades.store-grade', [$course, $c]) }}">
                                {{ $c->name }} ({{ $c->weight }}%)
                            </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nilai (0-100)</label>
                    <input type="number" id="inputScore" class="form-control" min="0" max="100" step="0.01" placeholder="85">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning fw-bold" onclick="submitInputNilai()">💾 Simpan</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit Nilai --}}
<div class="modal fade" id="editNilaiModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold">✏️ Edit Nilai — <span id="editNamaLabel"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Komponen</label>
                    <select id="editComponentSelect" class="form-select">
                        <option value="">-- Pilih Komponen --</option>
                        @foreach($components as $c)
                            @if(!$c->is_attendance && !$c->is_assignment_based)
                            <option value="{{ $c->id }}" data-url-prefix="{{ url('/grades/'.$course->id.'/components/'.$c->id.'/student') }}">
                                {{ $c->name }} ({{ $c->weight }}%)
                            </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nilai (0-100)</label>
                    <input type="number" id="editScoreInput" class="form-control" min="0" max="100" step="0.01" placeholder="85">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning fw-bold" onclick="submitEditNilai()">💾 Update</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#tableGrades').DataTable({
        language:{ search:"🔍 Cari:", lengthMenu:"Tampilkan _MENU_ data", info:"_START_-_END_ dari _TOTAL_", paginate:{first:"«",last:"»",next:"›",previous:"‹"} },
        columnDefs:[{ orderable:false, targets:-1 }],
        pageLength:25,
    });
});

function scrollToImport() {
    document.getElementById('importSection').scrollIntoView({ behavior:'smooth' });
}

function submitInputNilai() {
    const nim=document.getElementById('inputNim').value;
    const comp=document.getElementById('inputComponent');
    const score=document.getElementById('inputScore').value;
    const url=comp.options[comp.selectedIndex]?.dataset?.url;
    if(!nim){Swal.fire({title:'Pilih Mahasiswa!',icon:'warning'});return;}
    if(!comp.value||!url){Swal.fire({title:'Pilih Komponen!',icon:'warning'});return;}
    if(score===''||score<0||score>100){Swal.fire({title:'Nilai tidak valid!',icon:'warning'});return;}
    const form=document.createElement('form');form.method='POST';form.action=url;
    const csrf=document.createElement('input');csrf.type='hidden';csrf.name='_token';csrf.value='{{ csrf_token() }}';
    const nimI=document.createElement('input');nimI.type='hidden';nimI.name='student_nim';nimI.value=nim;
    const scI=document.createElement('input');scI.type='hidden';scI.name='score';scI.value=score;
    form.appendChild(csrf);form.appendChild(nimI);form.appendChild(scI);
    document.body.appendChild(form);form.submit();
}

function openEditNilai(nim,nama) {
    document.getElementById('editNamaLabel').innerText=nama;
    document.getElementById('editNilaiModal').dataset.nim=nim;
    document.getElementById('editComponentSelect').value='';
    document.getElementById('editScoreInput').value='';
    new bootstrap.Modal(document.getElementById('editNilaiModal')).show();
}

function submitEditNilai() {
    const select=document.getElementById('editComponentSelect');
    const score=document.getElementById('editScoreInput').value;
    const nim=document.getElementById('editNilaiModal').dataset.nim;
    const prefix=select.options[select.selectedIndex]?.dataset?.urlPrefix;
    const url=prefix?prefix+'/'+nim:'';
    if(!select.value||!prefix){Swal.fire({title:'Pilih Komponen!',icon:'warning'});return;}
    if(score===''||Number(score)<0||Number(score)>100){Swal.fire({title:'Nilai tidak valid!',icon:'warning'});return;}
    const form=document.createElement('form');form.method='POST';form.action=url;
    const csrf=document.createElement('input');csrf.type='hidden';csrf.name='_token';csrf.value='{{ csrf_token() }}';
    const meth=document.createElement('input');meth.type='hidden';meth.name='_method';meth.value='PUT';
    const scI=document.createElement('input');scI.type='hidden';scI.name='score';scI.value=score;
    form.appendChild(csrf);form.appendChild(meth);form.appendChild(scI);
    document.body.appendChild(form);form.submit();
}
</script>
@endpush