<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Backup satu tahun: dua sheet sekaligus (Pelanggaran & Prestasi).
 */
class BackupTahunanExport implements WithMultipleSheets
{
    public function __construct(private readonly int $tahun) {}

    /**
     * @return array<int, object>
     */
    public function sheets(): array
    {
        return [
            new BackupPelanggaranSheet($this->tahun),
            new BackupPrestasiSheet($this->tahun),
        ];
    }
}
