<?php

namespace Tests\Feature;

use App\Models\Pelanggaran;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Smoke test halaman daftar laporan.
 *
 * Bug ini nyata terjadi: commit "fix: design again" menimpa
 * resources/views/lapor/riwayat.blade.php yang tadinya halaman DAFTAR
 * dengan halaman RINCINAN satu laporan. Akibatnya view memakai
 * $pelanggaran (tunggal) sementara controller mengirim $laporans
 * (kumpulan) — hasilnya "Undefined variable $pelanggaran" dan error
 * merah untuk siapa pun yang membuka menu Riwayat Laporan.
 *
 * Test ini menjaga supaya kedua variabel tidak pernah tertukar lagi,
 * dan supaya semua role benar-benar bisa membuka halamannya.
 */
class RiwayatLaporanTest extends TestCase
{
    use RefreshDatabase;

    private const ROLE = ['admin', 'pds', 'bk', 'guru', 'walas'];

    private function buatUser(string $role, int $urut): User
    {
        return User::create([
            'name' => 'Uji '.$role,
            'email' => $role.'@uji.com',
            'nig' => '02501'.str_pad((string) $urut, 5, '0', STR_PAD_LEFT),
            'password' => Hash::make('uji123'),
            'role' => $role,
            'kelas' => $role === 'walas' ? '["X RPL 1"]' : null,
            'email_verified_at' => now(),
        ]);
    }

    public function test_riwayat_lolos_untuk_semua_role(): void
    {
        $siswa = Siswa::create([
            'nisn' => '1234567890',
            'nama' => 'Siswa Uji',
            'kelas' => 'X RPL 1',
            'jk' => 'L',
        ]);

        foreach (self::ROLE as $urut => $role) {
            $user = $this->buatUser($role, $urut + 1);

            // Satu laporan dengan siswa, satu laporan umum tanpa siswa.
            Pelanggaran::create([
                'siswa_id' => $siswa->id,
                'pelapor_id' => $user->id,
                'tanggal' => now(),
                'jenis_pelanggaran' => 'Pelanggaran oleh '.$role,
                'kategori' => 'Ringan',
                'poin' => 5,
                'status' => 'pending',
            ]);

            Pelanggaran::create([
                'siswa_id' => null,
                'pelapor_id' => $user->id,
                'tanggal' => now(),
                'jenis_pelanggaran' => 'Laporan Umum dari '.$role,
                'kategori' => 'Ringan',
                'poin' => 0,
                'status' => 'diverifikasi',
            ]);

            $this->actingAs($user)->get(route('lapor.riwayat'))->assertOk();
            $this->actingAs($user)->get(route('lapor.index'))->assertOk();
        }
    }

    /**
     * Halaman ini milik pengguna sendiri, jadi tidak boleh membocorkan
     * laporan orang lain — termasuk ke budgetary atasan.
     */
    public function test_riwayat_hanya_menampilkan_laporan_milik_sendiri(): void
    {
        $saya = $this->buatUser('walas', 1);
        $orangLain = $this->buatUser('guru', 2);

        $mine = Pelanggaran::create([
            'siswa_id' => null,
            'pelapor_id' => $saya->id,
            'tanggal' => now(),
            'jenis_pelanggaran' => 'Laporan Milik Saya',
            'kategori' => 'Ringan',
            'poin' => 0,
            'status' => 'pending',
        ]);

        $bukanMilikSaya = Pelanggaran::create([
            'siswa_id' => null,
            'pelapor_id' => $orangLain->id,
            'tanggal' => now(),
            'jenis_pelanggaran' => 'Laporan Orang Lain',
            'kategori' => 'Ringan',
            'poin' => 0,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($saya)->get(route('lapor.riwayat'));

        $response->assertOk();
        $response->assertSee('Laporan Milik Saya');
        $response->assertDontSee('Laporan Orang Lain');
    }

    public function test_riwayat_kosong_tidak_membuat_error(): void
    {
        $user = $this->buatUser('walas', 1);

        $this->actingAs($user)
            ->get(route('lapor.riwayat'))
            ->assertOk();
    }
}
