@extends('layouts.app')

@section('title', 'Konfigurasi Nilai Huruf - ' . $course->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-0">🎓 Konfigurasi Nilai Huruf</h4>
        <small class="text-muted">{{ $course->name }} | {{ $course->class_name }} | {{ $course->semester }}</small>
    </div>
    <a href="{{ route('grades.components', $course) }}" class="btn btn-secondary btn-sm">← Kembali</a>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">📊 Rentang Nilai Huruf</h6>
                <small>Nilai di bawah grade terendah = E</small>
            </div>
            <div class="card-body">
                <form action="{{ route('grades.save-grade-letters', $course) }}" method="POST" id="gradeLetterForm">
                    @csrf

                    <div class="alert alert-info py-2 small">
                        ℹ️ Tentukan rentang nilai untuk setiap huruf. Nilai di luar rentang otomatis = <strong>E</strong>.
                    </div>

                    <div id="gradeRows">
                        @forelse($configs as $i => $config)
                        <div class="row g-2 align-items-center mb-2 grade-row">
                            <div class="col-md-2">
                                <input type="text" name="letters[{{ $i }}][letter]"
                                       class="form-control text-center fw-bold"
                                       value="{{ $config->letter }}"
                                       placeholder="A" maxlength="3" required>
                            </div>
                            <div class="col-md-1 text-center text-muted">dari</div>
                            <div class="col-md-3">
                                <input type="number" name="letters[{{ $i }}][min]"
                                       class="form-control"
                                       value="{{ $config->min_score }}"
                                       min="0" max="100" step="0.01"
                                       placeholder="Min" required>
                            </div>
                            <div class="col-md-1 text-center text-muted">s/d</div>
                            <div class="col-md-3">
                                <input type="number" name="letters[{{ $i }}][max]"
                                       class="form-control"
                                       value="{{ $config->max_score }}"
                                       min="0" max="100" step="0.01"
                                       placeholder="Max" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm w-100"
                                        onclick="removeRow(this)">🗑️</button>
                            </div>
                        </div>
                        @empty
                        {{-- Default rows jika belum ada config --}}
                        <div class="row g-2 align-items-center mb-2 grade-row">
                            <div class="col-md-2">
                                <input type="text" name="letters[0][letter]" class="form-control text-center fw-bold" value="A" maxlength="3" required>
                            </div>
                            <div class="col-md-1 text-center text-muted">dari</div>
                            <div class="col-md-3">
                                <input type="number" name="letters[0][min]" class="form-control" value="85" min="0" max="100" step="0.01" required>
                            </div>
                            <div class="col-md-1 text-center text-muted">s/d</div>
                            <div class="col-md-3">
                                <input type="number" name="letters[0][max]" class="form-control" value="100" min="0" max="100" step="0.01" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeRow(this)">🗑️</button>
                            </div>
                        </div>
                        @endforelse
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button type="button" class="btn btn-success btn-sm" onclick="addRow()">
                            ➕ Tambah Grade
                        </button>
                        <button type="submit" class="btn btn-primary fw-bold">
                            💾 Simpan Konfigurasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        {{-- Preview --}}
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0 fw-bold">👁️ Preview Nilai Huruf</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <thead class="table-success">
                        <tr>
                            <th class="text-center">Huruf</th>
                            <th class="text-center">Rentang Nilai</th>
                        </tr>
                    </thead>
                    <tbody id="previewTable">
                        @foreach($configs as $config)
                        <tr>
                            <td class="text-center fw-bold">{{ $config->letter }}</td>
                            <td class="text-center">{{ $config->min_score }} - {{ $config->max_score }}</td>
                        </tr>
                        @endforeach
                        <tr class="table-danger">
                            <td class="text-center fw-bold">E</td>
                            <td class="text-center text-muted">Di bawah nilai minimum</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Contoh preset --}}
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0 fw-bold">📋 Preset Cepat</h6>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-2">Klik untuk menggunakan preset:</p>
                <button class="btn btn-outline-primary btn-sm mb-2 w-100"
                        onclick="applyPreset('standard')">
                    Standar (A≥85, B≥75, C≥65, D≥55)
                </button>
                <button class="btn btn-outline-success btn-sm mb-2 w-100"
                        onclick="applyPreset('strict')">
                    Ketat (A≥90, B≥80, C≥70, D≥60)
                </button>
                <button class="btn btn-outline-warning btn-sm w-100"
                        onclick="applyPreset('lenient')">
                    Longgar (A≥80, B≥70, C≥60, D≥50)
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let rowCount = {{ $configs->count() ?: 1 }};

function addRow() {
    const container = document.getElementById('gradeRows');
    const div = document.createElement('div');
    div.className = 'row g-2 align-items-center mb-2 grade-row';
    div.innerHTML = `
        <div class="col-md-2">
            <input type="text" name="letters[${rowCount}][letter]" class="form-control text-center fw-bold" placeholder="B" maxlength="3" required>
        </div>
        <div class="col-md-1 text-center text-muted">dari</div>
        <div class="col-md-3">
            <input type="number" name="letters[${rowCount}][min]" class="form-control" min="0" max="100" step="0.01" placeholder="Min" required>
        </div>
        <div class="col-md-1 text-center text-muted">s/d</div>
        <div class="col-md-3">
            <input type="number" name="letters[${rowCount}][max]" class="form-control" min="0" max="100" step="0.01" placeholder="Max" required>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeRow(this)">🗑️</button>
        </div>
    `;
    container.appendChild(div);
    rowCount++;
}

function removeRow(btn) {
    const rows = document.querySelectorAll('.grade-row');
    if (rows.length <= 1) {
        Swal.fire({ title: 'Minimal 1 grade!', icon: 'warning' });
        return;
    }
    btn.closest('.grade-row').remove();
}

function applyPreset(type) {
    const presets = {
        standard: [
            {letter: 'A', min: 85, max: 100},
            {letter: 'B', min: 75, max: 84.99},
            {letter: 'C', min: 65, max: 74.99},
            {letter: 'D', min: 55, max: 64.99},
        ],
        strict: [
            {letter: 'A', min: 90, max: 100},
            {letter: 'B', min: 80, max: 89.99},
            {letter: 'C', min: 70, max: 79.99},
            {letter: 'D', min: 60, max: 69.99},
        ],
        lenient: [
            {letter: 'A', min: 80, max: 100},
            {letter: 'B', min: 70, max: 79.99},
            {letter: 'C', min: 60, max: 69.99},
            {letter: 'D', min: 50, max: 59.99},
        ],
    };

    const preset = presets[type];
    const container = document.getElementById('gradeRows');
    container.innerHTML = '';
    rowCount = 0;

    preset.forEach((item, i) => {
        const div = document.createElement('div');
        div.className = 'row g-2 align-items-center mb-2 grade-row';
        div.innerHTML = `
            <div class="col-md-2">
                <input type="text" name="letters[${i}][letter]" class="form-control text-center fw-bold" value="${item.letter}" maxlength="3" required>
            </div>
            <div class="col-md-1 text-center text-muted">dari</div>
            <div class="col-md-3">
                <input type="number" name="letters[${i}][min]" class="form-control" value="${item.min}" min="0" max="100" step="0.01" required>
            </div>
            <div class="col-md-1 text-center text-muted">s/d</div>
            <div class="col-md-3">
                <input type="number" name="letters[${i}][max]" class="form-control" value="${item.max}" min="0" max="100" step="0.01" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeRow(this)">🗑️</button>
            </div>
        `;
        container.appendChild(div);
        rowCount++;
    });
}
</script>
@endpush