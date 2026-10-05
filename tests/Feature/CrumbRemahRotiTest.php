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

/**
 * Remah roti topbar punya tiga level: Portal > Grup > Halaman.
 *
 * Yang dijaga di sini:
 * - setiap halaman punya grup yang benar;
 * - grup dan portal jadi tautan kalau user boleh membukanya;
 * - level yang tidak boleh dibuka menjadi teks, bukan link mati;
 * - halaman terakhir ditandai aria-current.
 */
class CrumbRemahRotiTest extends TestCase
{
    use RefreshDatabase;

    private function buatSiswa(): Siswa
    {
        return Siswa::create(['nama' => 'Siswa Crumb', 'nisn' => '880001', 'kelas' => 'X RPL 1']);
    }

    private function buatPelanggaran(Siswa $siswa, User $pelapor): Pelanggaran
    {
        $aturan = AturanPelanggaran::create([
            'kode' => 'C1', 'kategori' => 'Ringan', 'nama' => 'Aturan Crumb',
            'poin' => 5, 'aktif' => true,
        ]);

        return Pelanggaran::create([
            'siswa_id' => $siswa->id,
            'pelapor_id' => $pelapor->id,
            'aturan_pelanggaran_id' => $aturan->id,
            'tanggal' => today(),
            'jenis_pelanggaran' => 'Pelanggaran Crumb',
            'kategori' => 'Ringan',
            'poin' => 5,
            'status' => 'diverifikasi',
        ]);
    }

    private function buatPrestasi(Siswa $siswa): Prestasi
    {
        return Prestasi::create([
            'siswa_id' => $siswa->id,
            'tanggal' => today(),
            'jenis_prestasi' => 'Prestasi Crumb',
            'tingkat' => 'Kota',
            'poin' => 10,
        ]);
    }

    private function crumb(string $html): string
    {
        $start = strpos($html, 'class="topbar-crumb"');
        $this->assertNotFalse($start, 'Remah roti tidak ditemukan');

        $end = strpos($html, '</nav>', $start);

        return substr($html, $start, $end - $start);
    }

    private function html(string $route, User $user, array $param = []): string
    {
        $response = $this->actingAs($user)->get(route($route, $param));
        $response->assertOk();

        return $this->crumb((string) $response->getContent());
    }

    private function user(string $role): User
    {
        return User::create([
            'name' => 'Uji '.$role,
            'email' => $role.'-crumb@test.com',
            'password' => bcrypt('secret1234'),
            'role' => $role,
            'kelas' => $role === 'walas' ? 'X RPL 1' : null,
        ]);
    }

    #[DataProvider('halamanProvider')]
    public function test_grup_dan_judul_muncul(string $role, string $route, string $grup, string $judul, array $param): void
    {
        $crumb = $this->html($route, $this->user($role), $param);

        $this->assertStringContainsString($grup, $crumb, "Grup '{$grup}' tidak muncul di {$route}");
        $this->assertStringContainsString($judul, $crumb, "Judul '{$judul}' tidak muncul di {$route}");
    }

    public static function halamanProvider(): array
    {
        return [
            'dashboard tanpa grup' => ['admin', 'dashboard', 'Administrator', 'Dashboard', []],
            'data pelanggaran' => ['admin', 'pelanggaran.index', 'Data Pelanggaran', 'Data Pelanggaran', []],
            'tambah pelanggaran' => ['pds', 'pelanggaran.create', 'Data Pelanggaran', 'Tambah Pelanggaran', []],
            'pending' => ['pds', 'pelanggaran.pending', 'Data Pelanggaran', 'Pending Laporan', []],
            'form lapor' => ['guru', 'lapor.index', 'Pelaporan', 'Portal Lapor Pelanggaran', []],
            'data siswa' => ['admin', 'siswa.index', 'Data Siswa', 'Data Siswa', []],
            'tambah siswa' => ['admin', 'siswa.create', 'Data Siswa', 'Tambah Siswa', []],
            'data prestasi' => ['admin', 'prestasi.index', 'Data Prestasi', 'Data Prestasi', []],
            'tambah prestasi' => ['admin', 'prestasi.create', 'Data Prestasi', 'Tambah Prestasi', []],
            'leaderboard tanpa induk' => ['admin', 'leaderboard', 'Leaderboard', 'Leaderboard', []],
        ];
    }

    public function test_grup_dan_portal_jadi_tautan(): void
    {
        $crumb = $this->html('pelanggaran.index', $this->user('admin'));

        // Grup "Data Pelanggaran" menuju daftar induknya.
        $this->assertStringContainsString(
            '<a class="crumb-link" href="'.route('pelanggaran.index').'">',
            $crumb
        );

        // Portal "Administrator" menuju dashboard.
        $this->assertStringContainsString(
            '<a class="crumb-link" href="'.route('dashboard').'">',
            $crumb
        );
    }

    public function test_halaman_terakhir_bukan_tautan_dan_ditandai_aria_current(): void
    {
        $crumb = $this->html('pelanggaran.index', $this->user('admin'));

        $this->assertStringContainsString('aria-current="page"', $crumb);

        // Halaman ini tidak boleh jadi tautan.
        $this->assertSame(
            2,
            substr_count($crumb, '<a class="crumb-link"'),
            'Remah roti harus punya tepat 2 tautan (portal + grup)'
        );
    }

    public function test_walas_tidak_dapat_kembali_ke_dashboard_lewat_crumb(): void
    {
        // Wali Kelas tidak boleh membuka /dashboard, jadi portalnya
        // harus mengarah ke halaman lapor, bukan ke dashboard.
        $crumb = $this->html('lapor.index', $this->user('walas'));

        $this->assertStringContainsString(
            '<a class="crumb-link" href="'.route('lapor.index').'">',
            $crumb
        );
        $this->assertStringNotContainsString(route('dashboard'), $crumb);
    }

    public function test_grup_dengan_induk_yang_boleh_dibuka_menjadi_tautan(): void
    {
        // Guru PDS boleh membuka /pelanggaran/index, jadi grup
        // "Data Pelanggaran" harus bisa diklik.
        $pds = $this->user('pds');

        $crumb = $this->html('pelanggaran.show', $pds, [
            'pelanggaran' => $this->buatPelanggaran($this->buatSiswa(), $pds)->id,
        ]);

        $this->assertStringContainsString('Data Pelanggaran', $crumb);
        $this->assertStringContainsString(route('pelanggaran.index'), $crumb);
    }

    /**
     * Unsur pimpinan ber-role "guru", jadi remah rotinya harus tetap
     * bisa menautkan ke Data Pelanggaran.
     */
    public function test_unsur_pimpinan_tetap_menaut_ke_data_pelanggaran(): void
    {
        $kepsek = $this->user('guru');
        $kepsek->update(['jabatan' => 'Kepala Sekolah']);

        $crumb = $this->html('pelanggaran.show', $kepsek, [
            'pelanggaran' => $this->buatPelanggaran($this->buatSiswa(), $kepsek)->id,
        ]);

        $this->assertStringContainsString('Data Pelanggaran', $crumb);
        $this->assertStringContainsString(route('pelanggaran.index'), $crumb);
    }

    public function test_halaman_detail_tetap_menampilkan_judul_yang_spesifik(): void
    {
        $admin = $this->user('admin');
        $siswa = $this->buatSiswa();
        $p = $this->buatPelanggaran($siswa, $admin);
        $pr = $this->buatPrestasi($siswa);

        $this->assertStringContainsString(
            'Detail Siswa',
            $this->html('siswa.show', $admin, ['siswa' => $siswa->id])
        );

        $this->assertStringContainsString(
            'Rincian Laporan Pelanggaran',
            $this->html('pelanggaran.show', $admin, ['pelanggaran' => $p->id])
        );

        $this->assertStringContainsString(
            'Detail Prestasi',
            $this->html('prestasi.show', $admin, ['prestasi' => $pr->id])
        );
    }
}
