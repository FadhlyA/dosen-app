<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR Absensi</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-primary">
    <div class="container">
        <span class="navbar-brand fw-bold">📚 DosenApp</span>
        <a href="{{ route('student.course', $course) }}" class="btn btn-outline-light btn-sm">← Kembali</a>
    </div>
</nav>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="mb-0 fw-bold">📱 Absensi QR Code</h5>
                    <small>{{ $course->name }} | Pertemuan {{ $meeting->meeting_number }}</small>
                </div>
                <div class="card-body text-center p-4">

                    <div class="mb-3">
                        <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center mb-2"
                             style="width:60px;height:60px;">
                            <span style="font-size:1.8rem">🎓</span>
                        </div>
                        <h6 class="fw-bold mb-0">{{ session('student_name') }}</h6>
                        <small class="text-muted">{{ session('student_nim') }}</small>
                    </div>

                    <div id="scanResult" class="d-none alert mb-3"></div>

                    <button id="btnAbsen" class="btn btn-primary btn-lg w-100 fw-bold" onclick="processAbsen()">
                        ✅ Catat Kehadiran Saya
                    </button>

                    <p class="text-muted small mt-3">
                        Pastikan QR Code yang ditampilkan dosen masih aktif sebelum klik tombol di atas.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function processAbsen() {
    const btn = document.getElementById('btnAbsen');
    btn.disabled = true;
    btn.innerText = '⏳ Memproses...';

    try {
        const response = await fetch("{{ route('qr.process', $qrToken->token) }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();
        const resultDiv = document.getElementById('scanResult');
        resultDiv.classList.remove('d-none', 'alert-success', 'alert-danger');

        if (data.success) {
            resultDiv.classList.add('alert-success');
            resultDiv.innerHTML = '✅ ' + data.message;
            btn.innerText = '✅ Sudah Absen';
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-success');
        } else {
            resultDiv.classList.add('alert-danger');
            resultDiv.innerHTML = '❌ ' + data.message;
            btn.disabled = false;
            btn.innerText = '✅ Catat Kehadiran Saya';
        }
    } catch(e) {
        document.getElementById('scanResult').classList.remove('d-none');
        document.getElementById('scanResult').classList.add('alert-danger');
        document.getElementById('scanResult').innerHTML = '❌ Terjadi kesalahan. Coba lagi.';
        btn.disabled = false;
        btn.innerText = '✅ Catat Kehadiran Saya';
    }
}
</script>
</body>
</html>