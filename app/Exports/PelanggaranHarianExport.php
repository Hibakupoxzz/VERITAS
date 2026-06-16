<?php

namespace App\Exports;

use App\Models\Pelanggaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PelanggaranHarianExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Pelanggaran::with('siswa')
            ->whereDate('tanggal', today())
            ->get()
            ->map(function ($item) {
                return [
                    'Tanggal' => $item->tanggal,
                    'Nama Siswa' => $item->siswa->nama,
                    'NISN' => $item->siswa->nisn,
                    'Kelas' => $item->siswa->kelas,
                    'Pelanggaran' => $item->jenis_pelanggaran,
                    'Poin' => $item->poin,
                    'Keterangan' => $item->keterangan,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Siswa',
            'NISN',
            'Kelas',
            'Pelanggaran',
            'Poin',
            'Keterangan'
        ];
    }
}
