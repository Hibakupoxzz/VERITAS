<?php

namespace Tests\Feature;

use App\Models\AturanPelanggaran;
use App\Models\Pelanggaran;
use App\Models\Prestasi;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PageTitleSectionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Guard: layout hanya meng-@yield('page_title') (underscore).
     * Kalau ada view yang menulis @section('page-title') (hyphen),
     * section itu tidak pernah dirender sehingga judul halaman
     * hilang dari breadcrumb tanpa error apa pun.
     */
    #[DataProvider('halamanProvider')]
    public function test_breadcrumb_judul_halaman_muncul(string $routeName, string $harusMuncul): void
    {
        $admin = User::create([
            'name' => 'Admin Uji',
            'email' => 'admin-title@test.com',
            'password' => bcrypt('secret1234'),
            'role' => 'admin',
        ]);

        $siswa = Siswa::create([
            'nama' => 'Siswa Uji',
            'nisn' => '777001',
            'kelas' => 'X-RPL-1',
        ]);

        $aturan = AturanPelanggaran::create([
            'kode' => 'T01', 'kategori' => 'Ringan',
            'nama' => 'Aturan Uji', 'poin' => 5, 'aktif' => true,
        ]);

        $pelanggaran = Pelanggaran::create([
            'siswa_id' => $siswa->id,
            'pelapor_id' => $admin->id,
            'aturan_pelanggaran_id' => $aturan->id,
            'tanggal' => today(),
            'jenis_pelanggaran' => 'Pelanggaran Uji',
            'kategori' => 'Ringan',
            'poin' => 5,
            'status' => 'diverifikasi',
        ]);

        $prestasi = Prestasi::create([
            'siswa_id' => $siswa->id,
            'tanggal' => today(),
            'jenis_prestasi' => 'Prestasi Uji',
            'tingkat' => 'Kota',
            'poin' => 10,
        ]);

        $response = $this->actingAs($admin)->get(route($routeName, [
            'siswa' => $siswa->id,
            'pelanggaran' => $pelanggaran->id,
            'prestasi' => $prestasi->id,
        ]));

        $response->assertOk();
        $response->assertSee($harusMuncul);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function halamanProvider(): array
    {
        return [
            'detail siswa' => ['siswa.show', 'Detail Siswa'],
            'tambah prestasi' => ['prestasi.create', 'Tambah Prestasi'],
            'edit prestasi' => ['prestasi.edit', 'Edit Prestasi'],
            'leaderboard' => ['leaderboard', 'Leaderboard'],
            'detail prestasi' => ['prestasi.show', 'Detail Prestasi'],
            'data pelanggaran' => ['pelanggaran.index', 'Data Pelanggaran'],
        ];
    }

    /**
     * Guard tambahan: tidak boleh ada view yang memakai
     * 'page-title' (hyphen) sebagai satu-satunya penentu judul.
     * Layout menerima keduanya, jadi yang dicek di sini hanya
     * bahwa view menuliskan section judul dengan nama yang
     * memang di-yield layout.
     */
    public function test_tidak_ada_view_pakai_section_page_title_hyphen(): void
    {
        $views = glob(resource_path('views').'/**/*.blade.php');
        $views = array_merge($views ?: [], glob(resource_path('views').'/*.blade.php') ?: []);

        $bad = [];

        foreach (array_unique($views) as $view) {
            if (str_contains((string) file_get_contents($view), "@section('page-title'")) {
                $bad[] = str_replace(resource_path('views').'/', '', $view);
            }
        }

        $this->assertSame([], $bad, 'Pakai @section(\'page-title\') (hyphen): '.implode(', ', $bad));
    }
}
