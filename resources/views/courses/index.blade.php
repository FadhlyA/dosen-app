@extends('layouts.app')
@section('title', 'Kelas & Matkul')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 style="font-family:'Sora',sans-serif;font-weight:700;margin:0;">📚 Kelas & Matkul</h4>
        <p style="color:var(--ld-slate);font-size:0.85rem;margin:0;">{{ $courses->count() }} kelas terdaftar</p>
    </div>
    <a href="{{ route('courses.create') }}" class="btn btn-primary">+ Tambah Kelas</a>
</div>

@if($courses->isEmpty())
<div class="card">
    <div class="card-body text-center py-5" style="color:var(--ld-slate);">
        <div style="font-size:3rem;">📚</div>
        <p class="mt-2 mb-3">Belum ada kelas.</p>
        <a href="{{ route('courses.create') }}" class="btn btn-primary">+ Tambah Kelas Pertama</a>
    </div>
</div>
@else
<div class="row g-3">
    @foreach($courses as $course)
    <div class="col-md-6 col-lg-4">
        <div class="card h-100" style="transition:transform 0.15s;"
             onmouseover="this.style.transform='translateY(-2px)'"
             onmouseout="this.style.transform=''">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span style="font-size:0.72rem;background:var(--ld-sky);color:var(--ld-blue);padding:2px 8px;border-radius:20px;font-weight:600;">{{ $course->code }}</span>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm" style="background:transparent;border:none;color:var(--ld-slate);" data-bs-toggle="dropdown">⋯</button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('courses.show', $course) }}">👁️ Detail</a></li>
                            <li><a class="dropdown-item" href="{{ route('courses.edit', $course) }}">✏️ Edit</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('courses.destroy', $course) }}" method="POST"
                                      onsubmit="return confirm('Hapus kelas ini?')">
                                    @csrf @method('DELETE')
                                    <button class="dropdown-item text-danger">🗑️ Hapus</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

                <h6 style="font-family:'Sora',sans-serif;font-weight:700;font-size:0.95rem;margin-bottom:4px;">{{ $course->name }}</h6>
                <p style="font-size:0.8rem;color:var(--ld-slate);margin-bottom:1rem;">
                    {{ $course->class_name }} · {{ $course->semester }}
                </p>

                <div style="font-family:'JetBrains Mono',monospace;font-size:1rem;font-weight:500;color:var(--ld-blue);background:var(--ld-sky);padding:6px 12px;border-radius:8px;letter-spacing:3px;display:inline-block;margin-bottom:0.75rem;">
                    {{ $course->access_key }}
                </div>

                <div class="d-flex gap-3" style="font-size:0.78rem;color:var(--ld-slate);">
                    <span>📅 {{ $course->meetings()->count() }} pertemuan</span>
                    <span>👥 {{ $course->students()->count() }} mahasiswa</span>
                </div>
            </div>
            <div class="card-footer" style="background:transparent;border-top:1px solid var(--ld-border);padding:0.75rem 1rem;">
                <div class="d-flex gap-2">
                    <a href="{{ route('courses.show', $course) }}" class="btn btn-primary btn-sm flex-fill">Detail</a>
                    <a href="{{ route('grades.course', $course) }}" class="btn btn-sm flex-fill" style="background:var(--ld-sky);color:var(--ld-blue);">Nilai</a>
                    <a href="{{ route('attendances.recap', $course) }}" class="btn btn-sm" style="background:#ECFEFF;color:#0891B2;">Absen</a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection