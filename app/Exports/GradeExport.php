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

class GradeExport implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected $course;
    protected $components;
    protected $finalGrades;
    protected $dosenName;
    protected $headerRow = 9; // baris mulai header tabel

    public function __construct(Course $course, $components, $finalGrades, $dosenName)
    {
        $this->course      = $course;
        $this->components  = $components;
        $this->finalGrades = $finalGrades;
        $this->dosenName   = $dosenName;
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
        $rows[] = ['REKAP NILAI MAHASISWA'];
        $rows[] = [''];

        // Info
        $rows[] = [
            'Mata Kuliah', ':', $this->course->name . ' (' . $this->course->code . ')',
            '', 'Dosen Pengampu', ':', $this->dosenName
        ];
        $rows[] = [
            'Kelas', ':', $this->course->class_name,
            '', 'Jumlah Mahasiswa', ':', count($this->finalGrades) . ' orang'
        ];
        $rows[] = ['Semester', ':', $this->course->semester];
        $rows[] = [''];

        // Header tabel
        $header = ['No', 'NIM', 'Nama Mahasiswa'];
        foreach ($this->components as $component) {
            $header[] = $component->name . ' (' . $component->weight . '%)';
        }
        $header[] = 'Nilai Akhir';
        $header[] = 'Nilai Huruf';
        $rows[]   = $header;

        // Data
        $no = 1;
        foreach ($this->finalGrades as $nim => $data) {
            $row = [$no++, $nim, $data['name']];
            foreach ($this->components as $component) {
                $row[] = $data['scores'][$component->id] ?? '-';
            }
            $row[] = $data['final'];
            $row[] = $data['letter'];
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
        return 'Rekap Nilai';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 15,
            'C' => 30,
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastCol  = 3 + $this->components->count() + 2;
        $lastColL = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastCol);
        $lastRow  = 12 + count($this->finalGrades) + 6;

        // Merge KOP
        $sheet->mergeCells('A1:' . $lastColL . '1');
        $sheet->mergeCells('A2:' . $lastColL . '2');
        $sheet->mergeCells('A3:' . $lastColL . '3');
        $sheet->mergeCells('A4:' . $lastColL . '4');
        $sheet->mergeCells('A6:' . $lastColL . '6');

        // Border bawah KOP
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

        // Judul
        $sheet->getStyle('A6')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12, 'underline' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Header tabel
        $headerRow = 12;
        $sheet->getStyle('A' . $headerRow . ':' . $lastColL . $headerRow)->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2d6a4f']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Data tabel
        $dataStart = $headerRow + 1;
        $dataEnd   = $headerRow + count($this->finalGrades);
        if ($dataEnd >= $dataStart) {
            $sheet->getStyle('A' . $dataStart . ':' . $lastColL . $dataEnd)->applyFromArray([
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            // Nama rata kiri
            $sheet->getStyle('C' . $dataStart . ':C' . $dataEnd)->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ]);
        }

        return [];
    }
}