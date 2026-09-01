<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Tugas - {{ $course->name }}</title>
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

        .judul { text-align: center; margin: 12px 0 10px 0; }
        .judul h2 { font-size: 12px; font-weight: bold; text-decoration: underline; text-transform: uppercase; letter-spacing: 1px; }

        .info-table { width: 55%; border-collapse: collapse; margin-bottom: 12px; }
        .info-table td { padding: 2px 4px; font-size: 10.5px; }
        .info-table td:first-child { width: 130px; }
        .info-table td:nth-child(2) { width: 8px; }

        .rekap-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
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

        .check  { color: #155724; font-weight: bold; }
        .uncheck { color: #721c24; font-weight: bold; }

        .keterangan { font-size: 9px; margin-bottom: 15px; color: #444; }

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
    <a href="{{ route('assignments.recap', $course) }}" class="btn btn-back">← Kembali</a>
    <span class="tips">Ctrl+P untuk print</span>
</div>

<div class="content">
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

    <div class="judul"><h2>Rekap Pengumpulan Tugas Mahasiswa</h2></div>

    <table class="info-table">
        <tr><td>Mata Kuliah</td><td>:</td><td>{{ $course->name }} ({{ $course->code }})</td></tr>
        <tr><td>Kelas</td><td>:</td><td>{{ $course->class_name }}</td></tr>
        <tr><td>Semester</td><td>:</td><td>{{ $course->semester }}</td></tr>
        <tr><td>Dosen Pengampu</td><td>:</td><td>{{ auth()->user()->full_name }}</td></tr>
        <tr><td>Total Mahasiswa</td><td>:</td><td>{{ $students->count() }} orang</td></tr>
        <tr><td>Total Tugas</td><td>:</td><td>{{ $assignments->count() }} tugas</td></tr>
    </table>

    <div class="keterangan">
        <strong>Keterangan:</strong>
        <span class="check">✓ = Sudah dikumpulkan</span> &nbsp;|&nbsp;
        <span class="uncheck">✗ = Belum dikumpulkan</span> &nbsp;|&nbsp;
        Baris kuning = ada tugas yang belum dikumpulkan
    </div>

    <table class="rekap-table">
        <thead>
            <tr>
                <th width="25">No</th>
                <th width="80">NIM</th>
                <th>Nama Mahasiswa</th>
                @foreach($assignments as $assignment)
                    <th width="40">
                        P{{ $assignment->meeting->meeting_number }}<br>
                        <span style="font-size:8px">{{ Str::limit($assignment->title, 10) }}</span>
                    </th>
                @endforeach
                <th width="40">Total</th>
                <th width="70">Ket</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recap as $studentId => $data)
            <tr class="{{ $data['warning'] ? 'warning' : '' }}">
                <td>{{ $loop->iteration }}</td>
                <td>{{ $data['nim'] }}</td>
                <td class="left">{{ $data['name'] }}</td>
                @foreach($assignments as $assignment)
                    <td>
                        @if($data['submissions'][$assignment->id])
                            <span class="check">✓</span>
                        @else
                            <span class="uncheck">✗</span>
                        @endif
                    </td>
                @endforeach
                <td class="fw-bold">{{ $data['total'] }}/{{ $data['total_all'] }}</td>
                <td>
                    @if($data['warning'])
                        <span class="uncheck">Belum Lengkap</span>
                    @else
                        <span class="check">Lengkap</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="ttd-section">
        Pekanbaru, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br><br>
        Dosen Pengampu,<br>
        <div class="ttd-space"></div>
        <span class="ttd-name">{{ auth()->user()->full_name }}</span>
    </div>
</div>

</body>
</html>