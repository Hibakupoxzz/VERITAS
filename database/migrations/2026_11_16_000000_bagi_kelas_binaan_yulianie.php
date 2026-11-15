<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

/**
 * Membagi kelas binaan Yulianie Kasari sekaligus mengeluarkannya
 * dari kelompok Guru PDS.
 *
 *   Hanifan Nurfauzi (Waka Kesiswaan) -> X-RPL-1, XI-RPL-2, XI-TKJ-2
 *   Septia Sundari   (Waka Humas)     -> XI-RPL-1, XII-TKJ-3, XII-DKV-1
 *   Sopiyan          (Koordinator PDS) -> X-TKJ-1 (ditambahkan)
 *   Yulianie Kasari                    -> role guru, tidak memegang kelas
 *
 * Nilainya sama dengan PdsUserSeeder dan
 * GuruTenagaPendidikSeeder::BINAAN_KELAS. Migration ini diperlukan
 * supaya database yang sudah berjalan ikut terbarui tanpa harus
 * menjalankan ulang seeder akun.
 *
 * Password Yulianie diselaraskan ke "guru123" supaya database yang
 * sudah berjalan sama dengan hasil seed bersih.
 */
return new class extends Migration
{
    private const NIG_YULIANIE = '0250189080';

    /** @var array<string, list<string>> */
    private const BINAAN = [
        '0190195044' => ['X-RPL-1', 'XI-RPL-2', 'XI-TKJ-2'],        // Hanifan Nurfauzi
        '0180193040' => ['XI-RPL-1', 'XII-TKJ-3', 'XII-DKV-1'],     // Septia Sundari
        '0170179003' => [                                            // Sopiyan
            'X-TKJ-1', 'XI-TKJ-1',
            'XII-DKV-3', 'XII-LPB-1', 'XII-DKV-4',
        ],
        '0250189080' => [],                                          // Yulianie Kasari
    ];

    /** @var array<string, list<string>> */
    private const BINAAN_SEBELUM = [
        '0170179003' => ['XI-TKJ-1', 'XII-DKV-3', 'XII-LPB-1', 'XII-DKV-4'],
        '0190195044' => [],
        '0180193040' => [],
        '0250189080' => [
            'X-RPL-1', 'X-TKJ-1',
            'XI-RPL-1', 'XI-RPL-2', 'XI-TKJ-2',
            'XII-TKJ-3', 'XII-DKV-1',
        ],
    ];

    public function up(): void
    {
        $this->terapkan(self::BINAAN);
        $this->ubahYulianie('guru', 'guru123');
    }

    public function down(): void
    {
        $this->terapkan(self::BINAAN_SEBELUM);
        $this->ubahYulianie('pds', 'pds123');
    }

    /**
     * @param  array<string, list<string>>  $peta
     */
    private function terapkan(array $peta): void
    {
        foreach ($peta as $nig => $kelas) {
            User::where('nig', $nig)->update([
                'kelas' => empty($kelas) ? null : json_encode($kelas),
            ]);
        }
    }

    private function ubahYulianie(string $role, string $password): void
    {
        User::where('nig', self::NIG_YULIANIE)->update([
            'role' => $role,
            'password' => Hash::make($password),
        ]);
    }
};
