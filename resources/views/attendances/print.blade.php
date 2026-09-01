<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Hadir - Pertemuan {{ $meeting->meeting_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; background: #fff; }

        .action-bar {
            background: #343a40;
            padding: 8px 15px;
            display: flex;
            gap: 8px;
            align-items: center;
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 999;
        }
        .action-bar .btn {
            padding: 5px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 11px;
            font-family: Arial, sans-serif;
            text-decoration: none;
            display: inline-block;
        }
        .btn-print { background: #0d6efd; color: white; }
        .btn-back  { background: #6c757d; color: white; }
        .tips      { color: #adb5bd; font-size: 10px; margin-left: 5px; }

        .content { padding: 20px 25px; margin-top: 45px; }

        /* KOP */
        .kop {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }
        .kop img { width: 75px; height: 75px; object-fit: contain; flex-shrink: 0; }
        .kop-text { flex: 1; text-align: center; padding: 0 10px; }
        .kop-text .yayasan { font-size: 10.5px; font-weight: bold; text-transform: uppercase; }
        .kop-text .kampus  { font-size: 22px; font-weight: bold; text-transform: uppercase; line-height: 1.1; }
        .kop-text .alamat  { font-size: 9px; margin-top: 4px; color: #222; }
        .kop-spacer        { width: 75px; flex-shrink: 0; }

        /* JUDUL */
        .judul { text-align: center; margin: 12px 0 10px 0; }
        .judul h2 { font-size: 12px; font-weight: bold; text-decoration: underline; text-transform: uppercase; letter-spacing: 1px; }

        /* INFO */
        .info-table { width: 60%; border-collapse: collapse; margin-bottom: 12px; }
        .info-table td { padding: 2px 4px; font-size: 10.5px; }
        .info-table td:first-child { width: 130px; }
        .info-table td:nth-child(2) { width: 8px; }

        /* TABEL HADIR */
        .hadir-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .hadir-table th {
            border: 1px solid #000;
            padding: 5px 4px;
            text-align: center;
            font-size: 10px;
            background-color: #e0e0e0;
            font-weight: bold;
        }
        .hadir-table td {
            border: 1px solid #000;
            padding: 5px 4px;
            text-align: center;
            font-size: 10px;
        }
        .hadir-table td.left { text-align: left; }
        .hadir-table tr:nth-child(even) td { background-color: #f9f9f9; }

        /* Status badge */
        .status-hadir { color: #155724; font-weight: bold; }
        .status-izin  { color: #856404; font-weight: bold; }
        .status-sakit { color: #004085; font-weight: bold; }
        .status-alpha { color: #721c24; font-weight: bold; }

        /* Kolom tanda tangan mahasiswa */
        .ttd-mhs { height: 35px; }

        /* TTD DOSEN */
        .ttd-section { margin-top: 20px; text-align: right; font-size: 11px; }
        .ttd-space { height: 55px; }
        .ttd-name {
            font-weight: bold;
            border-top: 1px solid #000;
            display: inline-block;
            padding-top: 3px;
            min-width: 160px;
            text-align: center;
        }

        /* Ringkasan */
        .ringkasan { margin-bottom: 15px; font-size: 10px; }
        .ringkasan span { margin-right: 15px; }

        @media print {
            .action-bar { display: none !important; }
            .content { margin-top: 0; }
            @page { size: A4 portrait; margin: 1.5cm; }
        }
    </style>
</head>
<body>

<div class="action-bar">
    <button class="btn btn-print" onclick="window.print()">🖨️ Print</button>
    <a href="{{ route('attendances.index', [$course, $meeting]) }}" class="btn btn-back">← Kembali</a>
    <span class="tips">Ctrl+P untuk print</span>
</div>

<div class="content">
    {{-- KOP --}}
    @php $inst = $course->getInstitutionInfo(); @endphp
    <div class="kop">
        <img src="{{ $inst['logo'] }}" alt="Logo {{ $inst['name'] }}">
        <div class="kop-text">
            <div class="kampus">{{ strtoupper($inst['name']) }}</div>
            <div class="alamat">
                {{ $inst['address'] }}<br>
                @if($inst['email'])Email: {{ $inst['email'] }}@endif
                @if($inst['website'])} | {{ $inst['website'] }}@endif
                @if($inst['phone'])} | HP. {{ $inst['phone'] }}@endif
            </div>
        </div>
        <div class="kop-spacer"></div>
    </div>

    {{-- JUDUL --}}
    <div class="judul"><h2>Daftar Hadir Mahasiswa</h2></div>

    {{-- INFO --}}
    <table class="info-table">
        <tr><td>Mata Kuliah</td><td>:</td><td>{{ $course->name }} ({{ $course->code }})</td></tr>
        <tr><td>Kelas</td><td>:</td><td>{{ $course->class_name }}</td></tr>
        <tr><td>Semester</td><td>:</td><td>{{ $course->semester }}</td></tr>
        <tr><td>Pertemuan Ke-</td><td>:</td><td>{{ $meeting->meeting_number }}</td></tr>
        <tr><td>Topik</td><td>:</td><td>{{ $meeting->title }}</td></tr>
        <tr><td>Tanggal</td><td>:</td><td>{{ \Carbon\Carbon::parse($meeting->meeting_date)->translatedFormat('d F Y') }}</td></tr>
        <tr><td>Dosen Pengampu</td><td>:</td><td>{{ auth()->user()->full_name }}</td></tr>
        <tr><td>Jumlah Mahasiswa</td><td>:</td><td>{{ $students->count() }} orang</td></tr>
    </table>

    {{-- Ringkasan --}}
    @php
        $hadirCount = $attendances->where('status', 'hadir')->count();
        $izinCount  = $attendances->where('status', 'izin')->count();
        $sakitCount = $attendances->where('status', 'sakit')->count();
        $alphaCount = $attendances->where('status', 'alpha')->count();
    @endphp
    <div class="ringkasan">
        <span>✅ Hadir: <strong>{{ $hadirCount }}</strong></span>
        <span>📝 Izin: <strong>{{ $izinCount }}</strong></span>
        <span>🏥 Sakit: <strong>{{ $sakitCount }}</strong></span>
        <span>❌ Alpha: <strong>{{ $alphaCount }}</strong></span>
    </div>

    {{-- TABEL DAFTAR HADIR --}}
    <table class="hadir-table">
        <thead>
            <tr>
                <th width="25">No</th>
                <th width="85">NIM</th>
                <th>Nama Mahasiswa</th>
                <th width="60">Status</th>
                <th width="120">Tanda Tangan</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $i => $student)
            @php
                $att    = $attendances->get($student->id);
                $status = $att ? $att->status : null;
                $note   = $att ? $att->note : null;
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $student->nim }}</td>
                <td class="left">{{ $student->name }}</td>
                <td>
                    @if($status === 'hadir')
                        <span class="status-hadir">Hadir</span>
                    @elseif($status === 'izin')
                        <span class="status-izin">Izin</span>
                    @elseif($status === 'sakit')
                        <span class="status-sakit">Sakit</span>
                    @elseif($status === 'alpha')
                        <span class="status-alpha">Alpha</span>
                    @else
                        <span style="color:#999">-</span>
                    @endif
                </td>
                <td class="ttd-mhs"></td>
                <td class="left">{{ $note ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- TTD DOSEN --}}
    <div class="ttd-section">
        Pekanbaru, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br><br>
        Dosen Pengampu,<br>
        <div class="ttd-space"></div>
        <span class="ttd-name">{{ auth()->user()->full_name }}</span>
    </div>
</div>

</body>
</html>