<?php

namespace App\Exports;

use App\Models\Course;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AttendanceExport implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected $course;
    protected $meetings;
    protected $recap;
    protected $dosenName;

    public function __construct(Course $course, $meetings, $recap, $dosenName)
    {
        $this->course     = $course;
        $this->meetings   = $meetings;
        $this->recap      = $recap;
        $this->dosenName  = $dosenName;
    }

    public function array(): array
    {
        $rows = [];

        // KOP
        $rows[] = ['YAYASAN DHARMA BAKTI MAHAPUTRA INDONESIA'];
        $rows[] = ['AMIK MAHAPUTRA RIAU'];
        $rows[] = ['Jl. Muchtar Lutfi - Jl. S.M. Amin, Kel. Simpang Baru, Kec. Binawidya, Pekanbaru - Riau 28292'];
        $rows[] = ['Email: info@amikmahaputra.ac.id | www.amikmahaputra.ac.id | HP. 0853-7164-2326'];
        $rows[] = [''];
        $rows[] = ['REKAP KEHADIRAN MAHASISWA'];
        $rows[] = [''];

        // Info
        $rows[] = ['Mata Kuliah', ':', $this->course->name . ' (' . $this->course->code . ')', '', 'Dosen Pengampu', ':', $this->dosenName];
        $rows[] = ['Kelas', ':', $this->course->class_name, '', 'Total Pertemuan', ':', $this->meetings->count() . ' pertemuan'];
        $rows[] = ['Semester', ':', $this->course->semester, '', 'Batas Kehadiran', ':', '75%'];
        $rows[] = [''];

        // Keterangan
        $rows[] = ['Keterangan: H = Hadir | I = Izin | S = Sakit | A = Alpha | - = Belum diisi'];
        $rows[] = [''];

        // Header tabel
        $header = ['No', 'NIM', 'Nama Mahasiswa'];
        foreach ($this->meetings as $meeting) {
            $header[] = 'P' . $meeting->meeting_number;
        }
        $header[] = 'H';
        $header[] = 'I';
        $header[] = 'S';
        $header[] = 'A';
        $header[] = '% Hadir';
        $header[] = 'Keterangan';
        $rows[]   = $header;

        // Data
        $no = 1;
        foreach ($this->recap as $data) {
            $row = [$no++, $data['nim'], $data['name']];
            foreach ($this->meetings as $meeting) {
                $status = $data['detail'][$meeting->id] ?? null;
                $row[]  = $status ? strtoupper(substr($status, 0, 1)) : '-';
            }
            $row[] = $data['hadir'];
            $row[] = $data['izin'];
            $row[] = $data['sakit'];
            $row[] = $data['alpha'];
            $row[] = $data['percentage'] . '%';
            $row[] = $data['warning'] ? 'TIDAK MEMENUHI' : 'MEMENUHI';
            $rows[] = $row;
        }

        // TTD
        $rows[] = [''];
        $rows[] = ['', '', '', '', '', 'Pekanbaru, ' . \Carbon\Carbon::now()->translatedFormat('d F Y')];
        $rows[] = ['', '', '', '', '', 'Dosen Pengampu,'];
        $rows[] = [''];
        $rows[] = [''];
        $rows[] = ['', '', '', '', '', $this->dosenName];

        return $rows;
    }

    public function title(): string
    {
        return 'Rekap Absensi';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 15,
            'C' => 30,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $totalCols = 3 + $this->meetings->count() + 6;
        $lastColL  = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalCols);
        $headerRow = 14;
        $dataCount = count($this->recap);

        // Merge KOP
        $sheet->mergeCells('A1:' . $lastColL . '1');
        $sheet->mergeCells('A2:' . $lastColL . '2');
        $sheet->mergeCells('A3:' . $lastColL . '3');
        $sheet->mergeCells('A4:' . $lastColL . '4');
        $sheet->mergeCells('A6:' . $lastColL . '6');
        $sheet->mergeCells('A12:' . $lastColL . '12');

        // Border KOP
        $sheet->getStyle('A4:' . $lastColL . '4')->applyFromArray([
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_DOUBLE]]
        ]);

        // Style KOP
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('A3:A4')->applyFromArray([
            'font'      => ['size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('A6')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12, 'underline' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Header tabel
        $sheet->getStyle('A' . $headerRow . ':' . $lastColL . $headerRow)->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1a5276']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Data
        $dataStart = $headerRow + 1;
        $dataEnd   = $headerRow + $dataCount;
        if ($dataEnd >= $dataStart) {
            $sheet->getStyle('A' . $dataStart . ':' . $lastColL . $dataEnd)->applyFromArray([
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $sheet->getStyle('C' . $dataStart . ':C' . $dataEnd)->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ]);
        }

        return [];
    }
}