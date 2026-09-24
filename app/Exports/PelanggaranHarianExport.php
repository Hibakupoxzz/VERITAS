<?php

namespace App\Exports;

use App\Models\Pelanggaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PelanggaranHarianExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    public function collection()
    {
        return Pelanggaran::with('siswa')
            ->whereDate('tanggal', today())
            ->latest('id')
            ->get()
            ->map(function ($item, $index) {
                return [
                    'No' => $index + 1,
                    'Tanggal' => $item->tanggal ? date('d/m/Y', strtotime($item->tanggal)) : '-',
                    'NISN' => $item->siswa?->nisn ?? '-',
                    'Nama Siswa' => $item->siswa?->nama ?? 'Siswa Tidak Ditemukan',
                    'Kelas' => $item->siswa?->kelas ?? '-',
                    'Pelanggaran' => $item->jenis_pelanggaran ?? ($item->aturanPelanggaran?->nama ?? '-'),
                    'Poin' => $item->poin,
                    'Keterangan' => $item->keterangan ?? '-',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'NISN',
            'Nama Siswa',
            'Kelas',
            'Pelanggaran',
            'Poin',
            'Keterangan',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        // Header Styling
        $sheet->getStyle('A1:H1')->applyFromArray([
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

        if ($lastRow > 1) {
            $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B2:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C2:C{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E2:E{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G2:G{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("A1:H{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D1D5DB'],
                    ],
                ],
            ]);
        }

        return [];
    }

    public function title(): string
    {
        return 'Pelanggaran Harian '.date('d-m-Y');
    }
}
