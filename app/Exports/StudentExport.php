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

class StudentExport implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected $course;
    protected $students;
    protected $dosenName;

    public function __construct(Course $course, $students, $dosenName)
    {
        $this->course     = $course;
        $this->students   = $students;
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
        $rows[] = ['DAFTAR MAHASISWA'];
        $rows[] = [''];

        // Info
        $rows[] = ['Mata Kuliah', ':', $this->course->name . ' (' . $this->course->code . ')', '', 'Dosen Pengampu', ':', $this->dosenName];
        $rows[] = ['Kelas', ':', $this->course->class_name, '', 'Total Mahasiswa', ':', $this->students->count() . ' orang'];
        $rows[] = ['Semester', ':', $this->course->semester];
        $rows[] = [''];

        // Header tabel
        $rows[] = ['No', 'NIM', 'Nama Mahasiswa', 'Email'];

        // Data
        $no = 1;
        foreach ($this->students as $student) {
            $rows[] = [
                $no++,
                $student->nim,
                $student->name,
                $student->email ?? '-',
            ];
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
        return 'Daftar Mahasiswa';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 15,
            'C' => 30,
            'D' => 30,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastColL  = 'G';
        $headerRow = 12;
        $dataCount = $this->students->count();

        // Merge KOP
        $sheet->mergeCells('A1:' . $lastColL . '1');
        $sheet->mergeCells('A2:' . $lastColL . '2');
        $sheet->mergeCells('A3:' . $lastColL . '3');
        $sheet->mergeCells('A4:' . $lastColL . '4');
        $sheet->mergeCells('A6:' . $lastColL . '6');

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
        $sheet->getStyle('A' . $headerRow . ':D' . $headerRow)->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1a3a5c']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Data
        $dataStart = $headerRow + 1;
        $dataEnd   = $headerRow + $dataCount;
        if ($dataEnd >= $dataStart) {
            $sheet->getStyle('A' . $dataStart . ':D' . $dataEnd)->applyFromArray([
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $sheet->getStyle('C' . $dataStart . ':D' . $dataEnd)->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ]);
        }

        return [];
    }
}