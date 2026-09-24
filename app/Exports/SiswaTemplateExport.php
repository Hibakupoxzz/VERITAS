<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SiswaTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithColumnFormatting, WithTitle
{
    public function array(): array
    {
        return [
            [
                'no' => 1,
                'nisn' => '0071234501',
                'nama' => 'Ahmad Fauzan Pratama',
                'kelas' => 'X PPLG 1',
            ],
            [
                'no' => 2,
                'nisn' => '0071234502',
                'nama' => 'Bunga Citra Lestari',
                'kelas' => 'X PPLG 1',
            ],
            [
                'no' => 3,
                'nisn' => '0061234503',
                'nama' => 'Dimas Arya Nugraha',
                'kelas' => 'XI TKJ 2',
            ],
            [
                'no' => 4,
                'nisn' => '0061234504',
                'nama' => 'Eka Putri Rahmawati',
                'kelas' => 'XI TKJ 2',
            ],
            [
                'no' => 5,
                'nisn' => '0051234505',
                'nama' => 'Farhan Maulana Akbar',
                'kelas' => 'XII RPL 1',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'NISN',
            'Nama Lengkap',
            'Kelas',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
                'name' => 'Calibri',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '6D1408'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(28);

        $sheet->getStyle('A2:A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B2:B6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D2:D6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C2:C6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->getStyle('A1:D6')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB'],
                ],
            ],
        ]);

        return [];
    }

    public function title(): string
    {
        return 'Template Data Siswa';
    }
}
