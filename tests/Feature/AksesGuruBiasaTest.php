<?php

namespace Tests\Feature;

use App\Models\Pelanggaran;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Guru biasa tidak boleh melihat data pribadi siswa.
 *
 * Yang dibatasi: Data Pelanggaran (daftar + rincian) dan dua papan
 * leaderboard yang isinya diturunkan dari pelanggaran — "Top
 * Pelanggaran" dan "Saldo Poin".
 *
 * Yang TIDAK dibatasi: Top Prestasi, Data Siswa, dan portal pelaporan.
 *
 * Unsur pimpinan (kepsek + waka) punya role "guru" yang sama, jadi
 * pengecualiannya ikut diuji — kalau salah, kepsek ikut kehilangan
 * akses monitoring, atau malah sebaliknya Guru biasa dapat akses penuh.
 */
class AksesGuruBiasaTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role, ?string $jabatan = null): User
    {
        static $urut = 0;
        $urut++;

        return User::create([
            'name' => 'Uji '.$role.' '.$urut,
            'email' => $role.'-akses'.$urut.'@test.com',
            'nig' => '02502'.str_pad((string) $urut, 5, '0', STR_PAD_LEFT),
            'password' => Hash::make('rahasia123'),
            'role' => $role,
            'jabatan' => $jabatan,
            'kelas' => $role === 'walas' ? '["X RPL 1"]' : null,
            'email_verified_at' => now(),
        ]);
    }

    private function buatPelanggaran(User $pelapor): int
    {
        $siswa = Siswa::create([
            'nisn' => (string) random_int(10_000_000, 99_999_999),
            'nama' => 'Siswa Bermasalah',
            'kelas' => 'X RPL 1',
            'jk' => 'L',
        ]);

        return Pelanggaran::create([
            'siswa_id' => $siswa->id,
            'pelapor_id' => $pelapor->id,
            'tanggal' => now(),
            'jenis_pelanggaran' => 'Mengganggu Pelajaran',
            'kategori' => 'Ringan',
            'poin' => 10,
            'status' => 'diverifikasi',
        ])->id;
    }

    public function test_guru_biasa_tidak_bisa_membuka_data_pelanggaran(): void
    {
        $guru = $this->user('guru');
        $id = $this->buatPelanggaran($guru);

        $this->actingAs($guru)
            ->get(route('pelanggaran.index'))
            ->assertRedirect(route('lapor.index'))
            ->assertSessionHas('error');

        $this->actingAs($guru)
            ->get(route('pelanggaran.show', $id))
            ->assertRedirect(route('lapor.index'));
    }

    public function test_guru_biasa_tidak_lihat_top_pelanggaran_dan_saldo_poin(): void
    {
        $guru = $this->user('guru');
        $this->buatPelanggaran($guru);

        $response = $this->actingAs($guru)->get(route('leaderboard'));

        $response->assertOk();
        $response->assertSee('Top Prestasi');
        $response->assertDontSee('Top Pelanggaran');
        $response->assertDontSee('Saldo Poin');

        // Nama siswa yang bermasalah tidak boleh bocor lewat halaman ini.
        $response->assertDontSee('Siswa Bermasalah');
    }

    public function test_guru_biasa_tetap_bisa_akses_yang_tidak_dibatasi(): void
    {
        $guru = $this->user('guru');

        $this->actingAs($guru)->get(route('lapor.index'))->assertOk();
        $this->actingAs($guru)->get(route('lapor.riwayat'))->assertOk();
        $this->actingAs($guru)->get(route('siswa.index'))->assertOk();
        $this->actingAs($guru)->get(route('prestasi.index'))->assertOk();
        $this->actingAs($guru)->get(route('leaderboard'))->assertOk();
    }

    public function test_menu_data_pelanggaran_tersembunyi_untuk_guru_biasa(): void
    {
        $guru = $this->user('guru');
        $html = (string) $this->actingAs($guru)->get(route('lapor.index'))->getContent();

        $this->assertStringNotContainsString(
            'href="'.route('pelanggaran.index').'"',
            $html,
            'Menu Pelanggaran Siswa masih muncul untuk guru biasa'
        );
    }

    /**
     * Unsur commanding: role "guru", tapi tetap boleh monitoring.
     */
    public function test_unsur_pimpinan_tetap_bisa_membuka_data_pelanggaran(): void
    {
        $kepsek = $this->user('guru', 'Kepala Sekolah');
        $waka = $this->user('guru', 'Waka Urs. Kesiswaan');
        $id = $this->buatPelanggaran($kepsek);

        $this->actingAs($kepsek)->get(route('pelanggaran.index'))->assertOk();
        $this->actingAs($kepsek)->get(route('pelanggaran.show', $id))->assertOk();
        $this->actingAs($waka)->get(route('pelanggaran.index'))->assertOk();

        foreach ([$kepsek, $waka] as $user) {
            $this->assertFalse($user->isGuruBiasa(), "{$user->name} salah dikira guru biasa");
            $this->assertSame('pimpinan', $user->navKey());
        }

        $html = (string) $this->actingAs($kepsek)->get(route('pelanggaran.index'))->getContent();
        $this->assertStringContainsString(
            'href="'.route('pelanggaran.index').'"',
            $html,
            'Menu Data Pelanggaran harus tetap muncul untuk unsur pimpinan'
        );
    }

    public function test_unsur_pimpinan_tetap_lihat_semua_papan_leaderboard(): void
    {
        $kepsek = $this->user('guru', 'Kepala Sekolah');
        $this->buatPelanggaran($kepsek);

        $response = $this->actingAs($kepsek)->get(route('leaderboard'));

        $response->assertOk();
        $response->assertSee('Top Prestasi');
        $response->assertSee('Top Pelanggaran');
        $response->assertSee('Saldo Poin');
    }

    /**
     * PDS, BK, admin, dan walas tidak berubah haknya.
     */
    public function test_role_lain_tidak_berubah(): void
    {
        foreach (['pds', 'bk', 'walas', 'admin'] as $role) {
            $user = $this->user($role);

            $this->assertFalse(
                $user->isGuruBiasa(),
                "Role {$role} salah dikira guru biasa"
            );
            $this->actingAs($user)->get(route('pelanggaran.index'))->assertOk();
        }
    }

    /**
     * Guru piket (role "guru", tanpa jabatan) adalah guru biasa —
     * dia tidak punya privilege lebih.
     */
    public function test_guru_piket_ikut_terbatas(): void
    {
        $piket = $this->user('guru', 'Guru Piket');

        $this->assertTrue($piket->isGuruBiasa());
        $this->actingAs($piket)
            ->get(route('pelanggaran.index'))
            ->assertRedirect(route('lapor.index'));
    }
}
