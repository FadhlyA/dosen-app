<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Tidak Valid</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5 text-center">
            <div style="font-size:4rem">❌</div>
            <h4 class="fw-bold mt-3">QR Code Tidak Valid</h4>
            <p class="text-muted">
                QR Code sudah kadaluarsa atau sudah digunakan maksimal 2 kali.
                Minta dosen untuk menampilkan QR Code terbaru.
            </p>
            <a href="{{ route('student.index') }}" class="btn btn-primary">🏠 Kembali ke Portal</a>
        </div>
    </div>
</div>
</body>
</html>