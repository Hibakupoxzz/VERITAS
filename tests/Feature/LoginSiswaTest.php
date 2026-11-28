<?php

namespace Tests\Feature;

use App\Models\Pelanggaran;
use App\Models\Prestasi;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Siswa login memakai NIS (nilai nisn pada data siswa lama) dan
 * password awal "password". Akun dibuat otomatis dari data siswa.
 */
class LoginSiswaTest extends TestCase
{
    use RefreshDatabase;

    private function buatSiswa(array $atribut = []): Siswa
    {
        return Siswa::create(array_merge([
            'nama' => 'Siswa Uji',
            'nisn' => '26271007213',
            'kelas' => 'X RPL 1',
        ], $atribut));
    }

    public function test_akun_siswa_dibuat_otomatis_dengan_nis_dan_password_awal(): void
    {
        $siswa = $this->buatSiswa();

        $this->assertDatabaseHas('users', [
            'siswa_id' => $siswa->id,
            'nis' => '26271007213',
            'role' => 'siswa',
        ]);

        $user = User::where('siswa_id', $siswa->id)->firstOrFail();

        $this->assertTrue(Hash::check('password', $user->password));
        $this->assertSame('Siswa Uji', $user->name);
    }

    public function test_siswa_bisa_login_memakai_nis(): void
    {
        $siswa = $this->buatSiswa();

        $response = $this->post(route('login'), [
            'nig' => '26271007213',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($siswa->user);
        $response->assertRedirect(route('siswa.portal'));
    }

    public function test_password_salah_ditolak(): void
    {
        $this->buatSiswa();

        $response = $this->post(route('login'), [
            'nig' => '26271007213',
            'password' => 'salah',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('nig');
    }

    public function test_nis_tidak_terdaftar_ditolak(): void
    {
        $response = $this->post(route('login'), [
            'nig' => '99999999999',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('nig');
    }

    public function test_update_nisn_siswa_menyelaraskan_nis_akun(): void
    {
        $siswa = $this->buatSiswa();

        $siswa->update([
            'nama' => 'Siswa Uji Update',
            'nisn' => '26271007999',
        ]);

        $this->assertDatabaseHas('users', [
            'siswa_id' => $siswa->id,
            'nis' => '26271007999',
            'name' => 'Siswa Uji Update',
        ]);
    }

    public function test_siswa_diarahkan_ke_portal_dari_halaman_guru(): void
    {
        $siswa = $this->buatSiswa();

        $this->actingAs($siswa->user)
            ->get(route('lapor.index'))
            ->assertRedirect(route('siswa.portal'));

        $this->actingAs($siswa->user)
            ->get(route('dashboard'))
            ->assertRedirect(route('siswa.portal'));

        $this->actingAs($siswa->user)
            ->get('/')
            ->assertRedirect(route('siswa.portal'));
    }

    public function test_portal_hanya_menampilkan_pelanggaran_dan_prestasi_siswa_sendiri(): void
    {
        $saya = $this->buatSiswa();
        $lain = $this->buatSiswa([
            'nama' => 'Siswa Lain',
            'nisn' => '26271007999',
        ]);

        Pelanggaran::create([
            'siswa_id' => $saya->id,
            'tanggal' => today(),
            'jenis_pelanggaran' => 'Pelanggaran Unik Saya',
            'kategori' => 'Ringan',
            'poin' => 5,
            'status' => 'diverifikasi',
        ]);

        Pelanggaran::create([
            'siswa_id' => $lain->id,
            'tanggal' => today(),
            'jenis_pelanggaran' => 'Pelanggaran Rahasia Siswa Lain',
            'kategori' => 'Ringan',
            'poin' => 20,
            'status' => 'diverifikasi',
        ]);

        Prestasi::create([
            'siswa_id' => $saya->id,
            'tanggal' => today(),
            'jenis_prestasi' => 'Prestasi Unik Saya',
            'tingkat' => 'Kota',
            'poin' => 10,
        ]);

        Prestasi::create([
            'siswa_id' => $lain->id,
            'tanggal' => today(),
            'jenis_prestasi' => 'Prestasi Rahasia Siswa Lain',
            'tingkat' => 'Kota',
            'poin' => 50,
        ]);

        $response = $this->actingAs($saya->user)
            ->get(route('siswa.portal'));

        $response->assertOk();
        $response->assertSee('Pelanggaran Unik Saya');
        $response->assertSee('Prestasi Unik Saya');
        $response->assertDontSee('Pelanggaran Rahasia Siswa Lain');
        $response->assertDontSee('Prestasi Rahasia Siswa Lain');
        $response->assertDontSee('Siswa Lain');
        $response->assertDontSee('26271007999');

        $riwayat = collect($response->viewData('riwayat'));

        $this->assertTrue(
            $riwayat->every(fn (array $item) => in_array($item['judul'], ['Pelanggaran Unik Saya', 'Prestasi Unik Saya'], true)),
            'Portal menampilkan riwayat milik siswa lain.'
        );

        // 100 - 5 (pelanggaran terverifikasi) + 10 (prestasi) = 105.
        $response->assertSee('105');
        $this->assertSame(105, $response->viewData('saldoPoin'));
    }

    public function test_endpoint_riwayat_hanya_mengembalikan_data_siswa_sendiri(): void
    {
        $saya = $this->buatSiswa();
        $lain = $this->buatSiswa([
            'nama' => 'Siswa Lain',
            'nisn' => '26271007999',
        ]);

        Pelanggaran::create([
            'siswa_id' => $saya->id,
            'tanggal' => today(),
            'jenis_pelanggaran' => 'Pelanggaran Unik Saya',
            'kategori' => 'Ringan',
            'poin' => 5,
            'status' => 'diverifikasi',
        ]);

        Pelanggaran::create([
            'siswa_id' => $lain->id,
            'tanggal' => today(),
            'jenis_pelanggaran' => 'Pelanggaran Rahasia Siswa Lain',
            'kategori' => 'Ringan',
            'poin' => 20,
            'status' => 'diverifikasi',
        ]);

        $json = $this->actingAs($saya->user)
            ->getJson(route('siswa.portal.riwayat'))
            ->assertOk()
            ->json();

        $this->assertSame(1, $json['jumlahPelanggaran']);
        $this->assertSame(0, $json['jumlahPrestasi']);
        $this->assertSame(95, $json['saldoPoin']);
        $this->assertCount(1, $json['riwayat']);
        $this->assertSame('Pelanggaran Unik Saya', $json['riwayat'][0]['judul']);
        $this->assertNotEmpty($json['sinkron_pada']);
    }

    public function test_endpoint_riwayat_bisa_menyaring_perubahan_sejak_waktu_tertentu(): void
    {
        $siswa = $this->buatSiswa();

        $lama = Pelanggaran::create([
            'siswa_id' => $siswa->id,
            'tanggal' => today()->subDay(),
            'jenis_pelanggaran' => 'Catatan Lama',
            'kategori' => 'Ringan',
            'poin' => 5,
            'status' => 'diverifikasi',
        ]);
        $lama->forceFill(['updated_at' => now()->subHour()])->saveQuietly();

        $json = $this->actingAs($siswa->user)
            ->getJson(route('siswa.portal.riwayat', ['since' => now()->subMinutes(5)->toIso8601String()]))
            ->assertOk()
            ->json();

        $this->assertCount(0, $json['riwayat']);
        $this->assertSame(1, $json['jumlahPelanggaran']);
    }

    public function test_riwayat_menggabungkan_pelanggaran_dan_prestasi_terbaru_di_atas(): void
    {
        $siswa = $this->buatSiswa();

        Pelanggaran::create([
            'siswa_id' => $siswa->id,
            'tanggal' => today()->subDays(3),
            'jenis_pelanggaran' => 'Pelanggaran Lama',
            'kategori' => 'Ringan',
            'poin' => 5,
            'status' => 'diverifikasi',
        ]);

        Prestasi::create([
            'siswa_id' => $siswa->id,
            'tanggal' => today(),
            'jenis_prestasi' => 'Prestasi Baru',
            'tingkat' => 'Kota',
            'poin' => 10,
        ]);

        $riwayat = $this->actingAs($siswa->user)
            ->getJson(route('siswa.portal.riwayat'))
            ->assertOk()
            ->json('riwayat');

        $this->assertSame('Prestasi Baru', $riwayat[0]['judul']);
        $this->assertSame('prestasi', $riwayat[0]['tipe']);
        $this->assertSame('Pelanggaran Lama', $riwayat[1]['judul']);
        $this->assertSame('pelanggaran', $riwayat[1]['tipe']);
    }

    public function test_akun_siswa_tidak_dikelola_lewat_manajemen_akun(): void
    {
        $siswa = $this->buatSiswa();

        $admin = User::create([
            'name' => 'Admin Uji',
            'email' => 'admin-siswa@test.com',
            'password' => bcrypt('rahasia123'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get(route('account.index'))
            ->assertOk()
            ->assertDontSee('26271007213@siswa.veritas.local');

        $this->actingAs($admin)
            ->get(route('account.edit', $siswa->user))
            ->assertNotFound();
    }

    public function test_perintah_sync_akun_membuat_akun_untuk_data_lama(): void
    {
        $siswa = $this->buatSiswa();

        // Simulasikan data siswa lama yang belum punya akun login.
        User::where('siswa_id', $siswa->id)->delete();

        $this->artisan('siswa:sync-akun')->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'siswa_id' => $siswa->id,
            'nis' => '26271007213',
            'role' => 'siswa',
        ]);
    }
}
