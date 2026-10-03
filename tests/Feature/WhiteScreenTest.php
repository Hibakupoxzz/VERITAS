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
 * Guard white screen.
 *
 * View yang tidak punya @extends DAN tidak punya @section('content')
 * dirender Laravel tanpa error — hasilnya string kosong
 * (HTTP 200, 0 byte). Browser lalu menampilkan layar putih dan
 * tidak ada satu pun entry di log. Guard ini menutup celah itu.
 */
class WhiteScreenTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string}>
     */
    public static function halamanProvider(): array
    {
        $out = [];

        $routes = [
            'dashboard' => 'dashboard',
            'pelanggaran.index' => 'pelanggaran.index',
            'pelanggaran.create' => 'pelanggaran.create',
            'pelanggaran.pending' => 'pelanggaran.pending',
            'pelanggaran.edit' => 'pelanggaran.edit',
            'pelanggaran.show' => 'pelanggaran.show',
            'siswa.index' => 'siswa.index',
            'siswa.create' => 'siswa.create',
            'siswa.edit' => 'siswa.edit',
            'siswa.show' => 'siswa.show',
            'leaderboard' => 'leaderboard',
            'lapor.index' => 'lapor.index',
            'lapor.riwayat' => 'lapor.riwayat',
        ];

        foreach ($routes as $route => $label) {
            $out[$route] = [$route];
        }

        return $out;
    }

    #[DataProvider('halamanProvider')]
    public function test_halaman_tidak_kosong(string $route): void
    {
        $data = $this->seedFixtures();

        $url = route($route, [
            'siswa' => $data['siswa']->id,
            'pelanggaran' => $data['pelanggaran']->id,
            'id' => $data['pelanggaran']->id,
        ]);

        $response = $this->actingAs($data['admin'])->get($url);

        $response->assertOk();

        $body = (string) $response->getContent();

        $this->assertNotSame('', $body, $url.' menghasilkan body kosong (white screen)');
        $this->assertGreaterThan(
            2000,
            strlen($body),
            $url.' menghasilkan body terlalu pendek ('.strlen($body).' byte) — kemungkinan markup hilang'
        );
        $this->assertStringContainsString(
            '</html>',
            $body,
            $url.' tidak menghasilkan dokumen HTML lengkap'
        );
    }

    #[DataProvider('halamanProvider')]
    public function test_view_memiliki_extends_dan_content(string $route): void
    {
        // Hanya relevan untuk halaman yang dirender lewat layout.
        $mapping = [
            'dashboard' => 'dashboard',
            'pelanggaran.index' => 'pelanggaran/index',
            'pelanggaran.create' => 'pelanggaran/create',
            'pelanggaran.pending' => 'pelanggaran/pending',
            'pelanggaran.edit' => 'pelanggaran/edit',
            'pelanggaran.show' => 'pelanggaran/show',
            'pelanggaran.export.harian' => 'pelanggaran/index',
            'pelanggaran.export.mingguan' => 'pelanggaran/index',
            'siswa.index' => 'siswa/index',
            'siswa.create' => 'siswa/create',
            'siswa.edit' => 'siswa/edit',
            'siswa.show' => 'siswa/show',
            'siswa.template' => 'siswa/index',
            'siswa.search' => 'siswa/index',
            'leaderboard' => 'prestasi/leaderboard',
            'lapor.index' => 'lapor/index',
            'lapor.riwayat' => 'lapor/riwayat',
        ];

        if (! isset($mapping[$route])) {
            $this->assertTrue(true);

            return;
        }

        $src = file_get_contents(resource_path('views/'.$mapping[$route].'.blade.php'));

        $this->assertStringContainsString(
            "@extends('layouts.app')",
            $src,
            $mapping[$route].' tidak punya @extends — halaman akan kosong'
        );
        $this->assertStringContainsString(
            "@section('content')",
            $src,
            $mapping[$route].' tidak punya @section(\'content\') — halaman akan kosong'
        );
    }

    private function seedFixtures(): array
    {
        $admin = User::create([
            'name' => 'Admin Uji',
            'email' => 'admin-ws@test.com',
            'password' => bcrypt('secret1234'),
            'role' => 'admin',
        ]);

        $walas = User::create([
            'name' => 'Walas Uji',
            'email' => 'walas-ws@test.com',
            'password' => bcrypt('secret1234'),
            'role' => 'walas',
            'kelas' => 'X-RPL-1',
        ]);

        $siswa = Siswa::create(['nama' => 'Budi', 'nisn' => '550001', 'kelas' => 'X-RPL-1']);

        $aturan = AturanPelanggaran::create([
            'kode' => 'A01', 'kategori' => 'Ringan',
            'nama' => 'Datang Terlambat', 'poin' => 5, 'aktif' => true,
        ]);

        $pelanggaran = Pelanggaran::create([
            'siswa_id' => $siswa->id,
            'pelapor_id' => $walas->id,
            'diverifikasi_oleh' => $admin->id,
            'aturan_pelanggaran_id' => $aturan->id,
            'tanggal' => today(),
            'jenis_pelanggaran' => 'Datang Terlambat',
            'kategori' => 'Ringan',
            'poin' => 5,
            'poin_sebelum' => 100,
            'poin_sesudah' => 95,
            'status' => 'diverifikasi',
        ]);

        Prestasi::create([
            'siswa_id' => $siswa->id,
            'tanggal' => today(),
            'jenis_prestasi' => 'Juara 1',
            'tingkat' => 'Kota',
            'poin' => 20,
        ]);

        return [
            'admin' => $admin,
            'siswa' => $siswa,
            'pelanggaran' => $pelanggaran,
        ];
    }
}
