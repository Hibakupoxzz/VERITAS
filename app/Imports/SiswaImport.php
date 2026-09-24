<?php

namespace App\Imports;

use App\Models\Siswa;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImport implements SkipsEmptyRows, ToCollection, WithHeadingRow
{
    protected int $createdCount = 0;

    protected int $updatedCount = 0;

    protected array $failures = [];

    public function collection(Collection $rows)
    {
        $rowNumber = 1;

        foreach ($rows as $row) {
            $rowNumber++;

            $nisn = $this->extractField($row, ['nisn', 'no_nisn', 'nomor_nisn', 'nis', 'no_induk']);
            $nama = $this->extractField($row, ['nama', 'nama_lengkap', 'nama_siswa', 'nama_peserta_didik']);
            $kelas = $this->extractField($row, ['kelas', 'rombel', 'rombongan_belajar', 'tingkat_kelas']);

            if (empty($nisn) && empty($nama) && empty($kelas)) {
                continue;
            }

            if (empty($nisn)) {
                $this->failures[] = "Baris {$rowNumber}: NISN wajib diisi.";

                continue;
            }

            $nisnClean = preg_replace('/[^0-9]/', '', (string) $nisn);
            if (empty($nisnClean)) {
                $this->failures[] = "Baris {$rowNumber}: NISN '{$nisn}' tidak valid (harus berupa angka).";

                continue;
            }

            if (empty($nama)) {
                $this->failures[] = "Baris {$rowNumber}: Nama siswa wajib diisi untuk NISN {$nisnClean}.";

                continue;
            }

            if (empty($kelas)) {
                $this->failures[] = "Baris {$rowNumber}: Kelas wajib diisi untuk siswa '{$nama}'.";

                continue;
            }

            $namaClean = trim(preg_replace('/\s+/', ' ', (string) $nama));
            $kelasClean = strtoupper(trim(preg_replace('/\s+/', ' ', (string) $kelas)));

            $existing = Siswa::where('nisn', $nisnClean)->first();

            if ($existing) {
                $existing->update([
                    'nama' => $namaClean,
                    'kelas' => $kelasClean,
                ]);
                $this->updatedCount++;
            } else {
                Siswa::create([
                    'nisn' => $nisnClean,
                    'nama' => $namaClean,
                    'kelas' => $kelasClean,
                ]);
                $this->createdCount++;
            }
        }
    }

    protected function extractField($row, array $possibleKeys)
    {
        foreach ($possibleKeys as $key) {
            if (isset($row[$key]) && trim((string) $row[$key]) !== '') {
                return trim((string) $row[$key]);
            }
        }

        foreach ($row as $k => $v) {
            $normalizedKey = strtolower(str_replace([' ', '-', '_'], '', $k));
            foreach ($possibleKeys as $target) {
                $normalizedTarget = strtolower(str_replace([' ', '-', '_'], '', $target));
                if ($normalizedKey === $normalizedTarget && trim((string) $v) !== '') {
                    return trim((string) $v);
                }
            }
        }

        return null;
    }

    public function getCreatedCount(): int
    {
        return $this->createdCount;
    }

    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }

    public function getTotalSuccess(): int
    {
        return $this->createdCount + $this->updatedCount;
    }

    public function getFailures(): array
    {
        return $this->failures;
    }
}
