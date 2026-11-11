<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

/**
 * Menetapkan hak akses tambahan untuk dua akun pimpinan yang sudah ada.
 *
 *   Hanifan Nurfauzi (Waka Kesiswaan)   -> akses penuh, kecuali Manajemen Akun
 *   Septia Sundari  (Waka Humas)        -> boleh menambah pelanggaran
 *
 * Nilainya sama dengan AKSES_KHUSUS di GuruTenagaPendidikSeeder.
 * Migration ini diperlukan supaya database yang sudah berjalan ikut
 * terbarui tanpa harus menjalankan ulang seeder akun.
 */
return new class extends Migration
{
    /** @var array<string, string> */
    private const AKSES = [
        '0190195044' => User::ACCESS_FULL,                    // Hanifan Nurfauzi
        '0180193040' => User::ACCESS_VERIFIKATOR,             // Septia Sundari
    ];

    public function up(): void
    {
        foreach (self::AKSES as $nig => $level) {
            User::where('nig', $nig)->update(['access_level' => $level]);
        }
    }

    public function down(): void
    {
        User::whereIn('nig', array_keys(self::AKSES))->update(['access_level' => null]);
    }
};
