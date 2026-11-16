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
 * Kelas di akun wali kelas ditulis "X-RPL-1", sedangkan data siswa
 * memakai "X RPL 1". Pencarian dan filter kelas tidak boleh kosong
 * hanya karena perbedaan tanda hubung tersebut.
 */
class WalasKelasFormatTest extends TestCase
{
    use RefreshDatabase;

    private function walas(): User
    {
        return User::create([
            'name' => 'Walas X RPL 1',
            'email' => 'walasxrpl1@test.com',
            'nig' => '0250288888',
            'password' => Hash::make('rahasia123'),
            'role' => 'walas',
            'kelas' => 'X-RPL-1',
            'email_verified_at' => now(),
        ]);
    }

    private function siswa(string $nama, string $kelas): Siswa
    {
        return Siswa::create([
            'nisn' => (string) random_int(10_000_000, 99_999_999),
            'nama' => $nama,
            'kelas' => $kelas,
            'jk' => 'L',
        ]);
    }

    public function test_scope_kelas_menerima_format_spasi_dan_tanda_hubung(): void
    {
        $target = $this->siswa('Siswa Kelas X', 'X RPL 1');
        $lain = $this->siswa('Siswa Kelas XI', 'XI RPL 1');

        foreach (['X-RPL-1', 'X RPL 1', 'x-rpl-1'] as $kelas) {
            $hasil = Siswa::whereKelas($kelas)->pluck('nama')->all();

            $this->assertContains($target->nama, $hasil, "Format {$kelas} gagal");
            $this->assertNotContains($lain->nama, $hasil, "Format {$kelas} bocor kelas lain");
        }
    }

    public function test_pencarian_siswa_walas_menemukan_kelas_yang_sama(): void
    {
        $walas = $this->walas();
        $target = $this->siswa('Siswa Kelas X', 'X RPL 1');
        $lain = $this->siswa('Siswa Kelas XI', 'XI RPL 1');

        $response = $this->actingAs($walas)->get(route('lapor.index'));

        $response->assertOk();
        $response->assertSee($target->nama);
        $response->assertDontSee($lain->nama);
    }

    public function test_pelanggaran_dan_prestasi_walas_ikut_terfilter(): void
    {
        $walas = $this->walas();
        $target = $this->siswa('Siswa Kelas X', 'X RPL 1');
        $lain = $this->siswa('Siswa Kelas XI', 'XI RPL 1');

        Pelanggaran::create([
            'siswa_id' => $target->id,
            'pelapor_id' => $walas->id,
            'tanggal' => now(),
            'jenis_pelanggaran' => 'Pelanggaran Kelas X',
            'kategori' => 'Ringan',
            'status' => 'diverifikasi',
            'poin' => 5,
        ]);

        Pelanggaran::create([
            'siswa_id' => $lain->id,
            'pelapor_id' => $walas->id,
            'tanggal' => now(),
            'jenis_pelanggaran' => 'Pelanggaran Kelas XI',
            'kategori' => 'Ringan',
            'status' => 'diverifikasi',
            'poin' => 5,
        ]);

        Prestasi::create([
            'siswa_id' => $target->id,
            'tanggal' => now(),
            'jenis_prestasi' => 'Prestasi Kelas X',
            'tingkat' => 'Sekolah',
            'poin' => 5,
        ]);

        Prestasi::create([
            'siswa_id' => $lain->id,
            'tanggal' => now(),
            'jenis_prestasi' => 'Prestasi Kelas XI',
            'tingkat' => 'Sekolah',
            'poin' => 5,
        ]);

        $this->actingAs($walas)
            ->get(route('pelanggaran.index'))
            ->assertOk()
            ->assertSee('Pelanggaran Kelas X')
            ->assertDontSee('Pelanggaran Kelas XI');

        $this->actingAs($walas)
            ->get(route('prestasi.index'))
            ->assertOk()
            ->assertSee('Prestasi Kelas X')
            ->assertDontSee('Prestasi Kelas XI');
    }
}
