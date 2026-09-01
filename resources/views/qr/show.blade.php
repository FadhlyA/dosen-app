@extends('layouts.app')
@section('title', 'QR Absensi - Pertemuan ' . $meeting->meeting_number)
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h4 style="font-family:'Sora',sans-serif;font-weight:700;margin:0;">📱 QR Code Absensi</h4>
        <p style="color:var(--ld-slate);font-size:0.85rem;margin:0;">
            {{ $course->name }} · {{ $course->class_name }} · Pertemuan {{ $meeting->meeting_number }}
        </p>
    </div>
    <a href="{{ route('attendances.index', [$course, $meeting]) }}" class="btn btn-sm btn-secondary">← Kembali</a>
</div>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card text-center">
            <div class="card-header bg-primary text-white" style="font-weight:600;">
                📱 Tampilkan ke Mahasiswa
            </div>
            <div class="card-body p-4">
                {{-- QR Container --}}
                <div id="qrContainer" class="mb-3 d-flex align-items-center justify-content-center"
                     style="min-height:260px;">
                    <div>
                        <div class="spinner-border text-primary" role="status"></div>
                        <p style="color:var(--ld-slate);font-size:0.85rem;margin-top:8px;">Generating QR...</p>
                    </div>
                </div>

                {{-- Timer --}}
                <div class="mb-3">
                    <p style="color:var(--ld-slate);font-size:0.78rem;margin-bottom:4px;">QR berubah dalam</p>
                    <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:2.5rem;color:var(--ld-blue);"
                         id="countdown">60</div>
                    <div class="progress mt-2" style="height:6px;">
                        <div class="progress-bar bg-primary" id="timerBar" style="width:100%;transition:width 1s linear;"></div>
                    </div>
                </div>

                <div class="alert alert-info py-2 small mb-3">
                    ⏱️ Berubah tiap <strong>1 menit</strong> &nbsp;·&nbsp;
                    👥 Maks <strong>2 scan</strong> per QR
                </div>

                <div class="d-flex justify-content-center gap-4">
                    <div class="text-center">
                        <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1.6rem;color:#16A34A;" id="scanCount">0</div>
                        <small style="color:var(--ld-slate);">Sudah Scan</small>
                    </div>
                    <div class="text-center">
                        <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:1.6rem;color:var(--ld-slate);">2</div>
                        <small style="color:var(--ld-slate);">Maks/QR</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body py-2">
                <div class="row text-center g-0">
                    <div class="col">
                        <small style="color:var(--ld-slate);">Pertemuan</small>
                        <div style="font-weight:700;color:var(--ld-blue);">{{ $meeting->meeting_number }}</div>
                    </div>
                    <div class="col">
                        <small style="color:var(--ld-slate);">Tanggal</small>
                        <div style="font-weight:600;font-size:0.85rem;">{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y') }}</div>
                    </div>
                    <div class="col">
                        <small style="color:var(--ld-slate);">Topik</small>
                        <div style="font-weight:600;font-size:0.82rem;">{{ Str::limit($meeting->title, 18) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
let countdown = 60;
let timerInterval;
const generateUrl = "{{ route('qr.generate', [$course, $meeting]) }}";
const csrfToken   = "{{ csrf_token() }}";

async function generateQR() {
    try {
        const res  = await fetch(generateUrl, {
            method:'POST',
            headers:{'X-CSRF-TOKEN':csrfToken,'Content-Type':'application/json'}
        });
        const data = await res.json();
        const container = document.getElementById('qrContainer');
        container.innerHTML = '<div id="qrCanvas"></div>';
        new QRCode(document.getElementById('qrCanvas'), {
            text: data.qr_url, width:220, height:220,
            colorDark:'#0F172A', colorLight:'#ffffff'
        });
        document.getElementById('scanCount').innerText = '0';
        countdown = 60;
    } catch(e) { console.error(e); }
}

function startTimer() {
    timerInterval = setInterval(() => {
        countdown--;
        document.getElementById('countdown').innerText = countdown;
        document.getElementById('timerBar').style.width = (countdown/60*100) + '%';
        if (countdown <= 10) {
            document.getElementById('countdown').style.color = '#DC2626';
            document.getElementById('timerBar').style.background = '#DC2626';
        }
        if (countdown <= 0) {
            document.getElementById('countdown').style.color = '#2563EB';
            document.getElementById('timerBar').style.background = '';
            generateQR();
        }
    }, 1000);
}

generateQR();
startTimer();
</script>
@endpush