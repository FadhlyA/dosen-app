<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi - {{ $course->name }}</title>
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
        .info-table { width: 55%; border-collapse: collapse; margin-bottom: 12px; }
        .info-table td { padding: 2px 4px; font-size: 10.5px; }
        .info-table td:first-child { width: 130px; }
        .info-table td:nth-child(2) { width: 8px; }

        /* TABEL REKAP */
        .rekap-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .rekap-table th {
            border: 1px solid #000;
            padding: 4px 3px;
            text-align: center;
            font-size: 9px;
            background-color: #e0e0e0;
            font-weight: bold;
        }
        .rekap-table td {
            border: 1px solid #000;
            padding: 4px 3px;
            text-align: center;
            font-size: 9px;
        }
        .rekap-table td.left { text-align: left; }
        .rekap-table tr:nth-child(even) td { background-color: #f9f9f9; }
        .rekap-table tr.warning td { background-color: #fff3cd; }

        .status-H { color: #155724; font-weight: bold; }
        .status-I { color: #856404; font-weight: bold; }
        .status-S { color: #004085; font-weight: bold; }
        .status-A { color: #721c24; font-weight: bold; }
        .pct-ok   { color: #155724; font-weight: bold; }
        .pct-warn { color: #721c24; font-weight: bold; }

        /* KETERANGAN */
        .keterangan { font-size: 9px; margin-bottom: 15px; color: #444; }
        .keterangan span { margin-right: 10px; }

        /* TTD */
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

        @media print {
            .action-bar { display: none !important; }
            .content { margin-top: 0; }
            @page { size: A4 landscape; margin: 1.5cm; }
        }
    </style>
</head>
<body>

<div class="action-bar">
    <button class="btn btn-print" onclick="window.print()">🖨️ Print</button>
    <a href="{{ route('attendances.recap', $course) }}" class="btn btn-back">← Kembali</a>
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
    <div class="judul"><h2>Rekap Kehadiran Mahasiswa</h2></div>

    {{-- INFO --}}
    <table class="info-table">
        <tr><td>Mata Kuliah</td><td>:</td><td>{{ $course->name }} ({{ $course->code }})</td></tr>
        <tr><td>Kelas</td><td>:</td><td>{{ $course->class_name }}</td></tr>
        <tr><td>Semester</td><td>:</td><td>{{ $course->semester }}</td></tr>
        <tr><td>Dosen Pengampu</td><td>:</td><td>{{ auth()->user()->full_name }}</td></tr>
        <tr><td>Total Pertemuan</td><td>:</td><td>{{ $totalMeetings }} pertemuan</td></tr>
        <tr><td>Jumlah Mahasiswa</td><td>:</td><td>{{ $students->count() }} orang</td></tr>
        <tr><td>Batas Kehadiran</td><td>:</td><td>75%</td></tr>
    </table>

    {{-- KETERANGAN --}}
    <div class="keterangan">
        <strong>Keterangan:</strong>
        <span class="status-H">H = Hadir</span>
        <span class="status-I">I = Izin</span>
        <span class="status-S">S = Sakit</span>
        <span class="status-A">A = Alpha</span>
        <span>— = Belum diisi</span>
        | Baris kuning = kehadiran di bawah 75%
    </div>

    {{-- TABEL REKAP --}}
    <table class="rekap-table">
        <thead>
            <tr>
                <th width="25">No</th>
                <th width="80">NIM</th>
                <th>Nama</th>
                @foreach($meetings as $meeting)
                    <th width="22" title="{{ $meeting->title }}">P{{ $meeting->meeting_number }}</th>
                @endforeach
                <th width="25">H</th>
                <th width="25">I</th>
                <th width="25">S</th>
                <th width="25">A</th>
                <th width="45">%</th>
                <th width="70">Ket</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recap as $studentId => $data)
            <tr class="{{ $data['warning'] ? 'warning' : '' }}">
                <td>{{ $loop->iteration }}</td>
                <td>{{ $data['nim'] }}</td>
                <td class="left">{{ $data['name'] }}</td>
                @foreach($meetings as $meeting)
                @php $status = $data['detail'][$meeting->id] ?? null; @endphp
                <td>
                    @if($status === 'hadir')
                        <span class="status-H">H</span>
                    @elseif($status === 'izin')
                        <span class="status-I">I</span>
                    @elseif($status === 'sakit')
                        <span class="status-S">S</span>
                    @elseif($status === 'alpha')
                        <span class="status-A">A</span>
                    @else
                        <span style="color:#ccc">—</span>
                    @endif
                </td>
                @endforeach
                <td class="status-H">{{ $data['hadir'] }}</td>
                <td class="status-I">{{ $data['izin'] }}</td>
                <td class="status-S">{{ $data['sakit'] }}</td>
                <td class="status-A">{{ $data['alpha'] }}</td>
                <td class="{{ $data['warning'] ? 'pct-warn' : 'pct-ok' }}">
                    {{ $data['percentage'] }}%
                </td>
                <td>
                    @if($data['warning'])
                        <span class="pct-warn">Tidak Memenuhi</span>
                    @else
                        <span class="pct-ok">Memenuhi</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- TTD --}}
    <div class="ttd-section">
        Pekanbaru, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br><br>
        Dosen Pengampu,<br>
        <div class="ttd-space"></div>
        <span class="ttd-name">{{ auth()->user()->full_name }}</span>
    </div>
</div>

</body>
</html>