<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

/**
 * Menambahkan role "pimpinan" untuk Kepala Sekolah dan para waka.
 *
 * Sebelumnya keenam akun ini ber-role "guru" dan baru dikenali
 * sebagai pimpinan lewat kolom jabatan. Dengan role tersendiri,
 * statusnya eksplisit dan bisa dipilih langsung dari Manajemen Akun.
 *
 * Password tidak diubah: keenam akun sudah memakai "pimpinan123".
 */
return new class extends Migration
{
    /** @var list<string> */
    private const PIMPINAN = [
        '0250186083', // Sri Mildawati — Kepala Sekolah
        '0170194006', // Amirul Hadid Ramadan — Waka Kurikulum
        '0180193040', // Septia Sundari — Waka Hubungan Masyarakat
        '0170188005', // Nugrah Dwi Saptaji — Waka Hubungan Industri
        '0190195044', // Hanifan Nurfauzi — Waka Kesiswaan
        '0170167004', // Rohajon — Waka Sarana Prasarana
    ];

    public function up(): void
    {
        User::whereIn('nig', self::PIMPINAN)
            ->update(['role' => 'pimpinan']);
    }

    public function down(): void
    {
        User::whereIn('nig', self::PIMPINAN)
            ->where('role', 'pimpinan')
            ->update(['role' => 'guru']);
    }
};
