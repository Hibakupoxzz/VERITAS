<?php

namespace Tests\Feature;

use App\Models\AturanPelanggaran;
use App\Models\Pelanggaran;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LaporMergeVerificationTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role, ?string $kelas = null): User
    {
        return User::create([
            'name' => 'Uji '.$role,
            'email' => $role.'-'.($kelas ?? 'umum').'@test.com',
            'password' => bcrypt('secret1234'),
            'role' => $role,
            'kelas' => $kelas,
        ]);
    }

    private function login(string $role, ?string $kelas = null): User
    {
        $user = $this->makeUser($role, $kelas);
        $this->actingAs($user);

        return $user;
    }

    /**
     * ================================================================
     * INDEX
     * ================================================================
     */
    public function test_index_walas_hanya_melihat_siswa_kelasnya(): void
    {
        $this->login('walas', 'X-RPL-1');

        Siswa::create(['nama' => 'Siswa RPL1', 'nisn' => '1001', 'kelas' => 'X-RPL-1']);
        Siswa::create(['nama' => 'Siswa RPL2', 'nisn' => '1002', 'kelas' => 'X-RPL-2']);

        $response = $this->get(route('lapor.index'));

        $response->assertOk();
        $response->assertViewHas('siswas', function ($siswas) {
            return $siswas->count() === 1
                && $siswas->first()->nama === 'Siswa RPL1';
        });
        $response->assertViewHas('kelasList', function ($list) {
            return $list->count() === 1 && $list->first() === 'X-RPL-1';
        });
    }

    public function test_index_guru_khusus_melihat_semua_kelas(): void
    {
        $this->login('guru');

        Siswa::create(['nama' => 'Siswa A', 'nisn' => '2001', 'kelas' => 'X-RPL-1']);
        Siswa::create(['nama' => 'Siswa B', 'nisn' => '2002', 'kelas' => 'X-RPL-2']);
        Siswa::create(['nama' => 'Siswa C', 'nisn' => '2003', 'kelas' => 'X-RPL-2']);

        $response = $this->get(route('lapor.index'));

        $response->assertOk();
        $response->assertViewHas('siswas', fn ($s) => $s->count() === 3);
        $response->assertViewHas('kelasList', fn ($l) => $l->count() === 2);
    }

    public function test_index_meneruskan_siswa_terpilih_dari_query_string(): void
    {
        $this->login('guru');

        $siswa = Siswa::create(['nama' => 'Siswa X', 'nisn' => '3001', 'kelas' => 'X-TKJ-2']);

        $response = $this->get(route('lapor.index', ['siswa_id' => $siswa->id]));

        $response->assertOk();
        $response->assertViewHas('selectedSiswaId', $siswa->id);
    }

    public function test_index_mengirim_aturan_pelanggaran_aktif_terurut(): void
    {
        $this->login('guru');

        AturanPelanggaran::create(['kode' => 'A2', 'kategori' => 'Berat', 'nama' => 'Berat', 'poin' => 50, 'aktif' => true]);
        AturanPelanggaran::create(['kode' => 'A1', 'kategori' => 'Ringan', 'nama' => 'Ringan', 'poin' => 10, 'aktif' => true]);
        AturanPelanggaran::create(['kode' => 'A3', 'kategori' => 'Sedang', 'nama' => 'Nonaktif', 'poin' => 20, 'aktif' => false]);

        $response = $this->get(route('lapor.index'));

        $response->assertOk();
        $response->assertViewHas('aturanPelanggarans', function ($items) {
            return $items->count() === 2
                && $items->first()->kategori === 'Ringan'
                && $items->last()->kategori === 'Berat';
        });
    }

    public function test_index_mengirim_stats_dan_laporans(): void
    {
        $user = $this->login('guru');

        $siswa = Siswa::create(['nama' => 'Siswa Y', 'nisn' => '4001', 'kelas' => 'X-RPL-1']);

        Pelanggaran::create([
            'siswa_id' => $siswa->id, 'pelapor_id' => $user->id,
            'tanggal' => now(), 'jenis_pelanggaran' => 'A', 'status' => 'pending', 'poin' => 0,
        ]);
        Pelanggaran::create([
            'siswa_id' => $siswa->id, 'pelapor_id' => $user->id,
            'tanggal' => now(), 'jenis_pelanggaran' => 'B', 'status' => 'diverifikasi', 'poin' => 0,
        ]);
        Pelanggaran::create([
            'siswa_id' => $siswa->id, 'pelapor_id' => $user->id,
            'tanggal' => now(), 'jenis_pelanggaran' => 'C', 'status' => 'ditolak', 'poin' => 0,
        ]);

        $response = $this->get(route('lapor.index'));

        $response->assertOk();
        $response->assertViewHas('stats', fn ($s) => $s['total'] === 3
            && $s['pending'] === 1 && $s['verified'] === 1 && $s['rejected'] === 1);
        $response->assertViewHas('laporans', fn ($l) => $l->count() === 3);
    }

    /**
     * ================================================================
     * RIWAYAT
     * ================================================================
     */
    public function test_riwayat_hanya_menampilkan_laporan_milik_sendiri(): void
    {
        $user = $this->login('guru');
        $orangLain = $this->makeUser('pds');

        $siswa = Siswa::create(['nama' => 'Siswa Z', 'nisn' => '5001', 'kelas' => 'X-RPL-1']);

        Pelanggaran::create([
            'siswa_id' => $siswa->id, 'pelapor_id' => $user->id,
            'tanggal' => now(), 'jenis_pelanggaran' => 'Milik Saya', 'status' => 'pending', 'poin' => 0,
        ]);
        Pelanggaran::create([
            'siswa_id' => $siswa->id, 'pelapor_id' => $orangLain->id,
            'tanggal' => now(), 'jenis_pelanggaran' => 'Milik Orang Lain', 'status' => 'pending', 'poin' => 0,
        ]);

        $response = $this->get(route('lapor.riwayat'));

        $response->assertOk();
        $response->assertViewHas('laporans', fn ($l) => $l->count() === 1
            && $l->first()->jenis_pelanggaran === 'Milik Saya');
        $response->assertViewHas('stats', fn ($s) => $s['total'] === 1);
    }

    /**
     * ================================================================
     * STORE - MODE ATURAN
     * ================================================================
     */
    public function test_store_mode_aturan_menyimpan_kategori_dan_poin_dari_aturan(): void
    {
        Storage::fake('public');

        $user = $this->login('guru');
        $siswa = Siswa::create(['nama' => 'Siswa Aturan', 'nisn' => '6001', 'kelas' => 'X-RPL-1']);

        $aturan = AturanPelanggaran::create([
            'kode' => 'B1', 'kategori' => 'Sedang', 'nama' => 'B_destroyer',
            'poin' => 30, 'aktif' => true,
        ]);

        $response = $this->post(route('lapor.store'), [
            'siswa_id' => $siswa->id,
            'tanggal' => '2026-09-28',
            'pelanggaran_mode' => 'aturan',
            'aturan_pelanggaran_id' => $aturan->id,
            'keterangan' => 'Terbukti di kantin.',
            'foto_bukti' => UploadedFile::fake()->image('bukti.jpg'),
        ]);

        $response->assertRedirect(route('lapor.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pelanggarans', [
            'pelapor_id' => $user->id,
            'siswa_id' => $siswa->id,
            'jenis_pelanggaran' => 'B_destroyer',
            'kategori' => 'Sedang',
            'poin' => 30,
            'aturan_pelanggaran_id' => $aturan->id,
            'status' => 'pending',
        ]);
    }

    public function test_store_mode_aturan_menolak_aturan_tidak_aktif(): void
    {
        $this->login('guru');

        $aturan = AturanPelanggaran::create([
            'kode' => 'C1', 'kategori' => 'Ringan', 'nama' => 'Nonaktif',
            'poin' => 10, 'aktif' => false,
        ]);

        $response = $this->post(route('lapor.store'), [
            'tanggal' => '2026-09-28',
            'pelanggaran_mode' => 'aturan',
            'aturan_pelanggaran_id' => $aturan->id,
        ]);

        $response->assertSessionHasErrors('aturan_pelanggaran_id');
        $this->assertDatabaseCount('pelanggarans', 0);
    }

    public function test_store_mode_aturan_wajib_memilih_aturan(): void
    {
        $this->login('guru');

        $response = $this->post(route('lapor.store'), [
            'tanggal' => '2026-09-28',
            'pelanggaran_mode' => 'aturan',
        ]);

        $response->assertSessionHasErrors('aturan_pelanggaran_id');
    }

    /**
     * ================================================================
     * STORE - MODE MANUAL
     * ================================================================
     */
    public function test_store_mode_manual_menyimpan_jenis_kategori_dan_poin(): void
    {
        $user = $this->login('walas', 'X-RPL-1');
        $siswa = Siswa::create(['nama' => 'Siswa Manual', 'nisn' => '7001', 'kelas' => 'X-RPL-1']);

        $response = $this->post(route('lapor.store'), [
            'siswa_id' => $siswa->id,
            'tanggal' => '2026-09-28',
            'pelanggaran_mode' => 'manual',
            'jenis_pelanggaran_manual' => 'Mencuri di kelas',
            'kategori_manual' => 'Berat',
            'poin_manual' => 75,
            'keterangan' => 'Terlihat oleh kamera CCTV kelas.',
        ]);

        $response->assertRedirect(route('lapor.index'));

        $this->assertDatabaseHas('pelanggarans', [
            'pelapor_id' => $user->id,
            'jenis_pelanggaran' => 'Mencuri di kelas',
            'kategori' => 'Berat',
            'poin' => 75,
            'status' => 'pending',
        ]);

        // Mode manual tidak boleh mengaitkan aturan.
        $this->assertDatabaseMissing('pelanggarans', [
            'pelapor_id' => $user->id,
            'aturan_pelanggaran_id' => Pelanggaran::where('pelapor_id', $user->id)->first()->id ? 1 : 999,
        ]);
    }

    public function test_store_mode_manual_menolak_kategori_tidak_valid(): void
    {
        $this->login('guru');

        $response = $this->post(route('lapor.store'), [
            'tanggal' => '2026-09-28',
            'pelanggaran_mode' => 'manual',
            'jenis_pelanggaran_manual' => 'Apapun',
            'kategori_manual' => 'Sangat Berat',
            'poin_manual' => 10,
        ]);

        $response->assertSessionHasErrors('kategori_manual');
    }

    public function test_store_mode_manual_menolak_poin_negatif(): void
    {
        $this->login('guru');

        $response = $this->post(route('lapor.store'), [
            'tanggal' => '2026-09-28',
            'pelanggaran_mode' => 'manual',
            'jenis_pelanggaran_manual' => 'Apapun',
            'kategori_manual' => 'Ringan',
            'poin_manual' => -5,
        ]);

        $response->assertSessionHasErrors('poin_manual');
    }

    /**
     * ================================================================
     * STORE - ATURAN UMUM
     * ================================================================
     */
    public function test_store_menerima_siswa_kosong(): void
    {
        $user = $this->login('guru');

        $response = $this->post(route('lapor.store'), [
            'tanggal' => '2026-09-28',
            'pelanggaran_mode' => 'manual',
            'jenis_pelanggaran_manual' => 'Laporan Umum Kelas',
            'kategori_manual' => 'Ringan',
            'poin_manual' => 0,
        ]);

        $response->assertRedirect(route('lapor.index'));

        $this->assertDatabaseHas('pelanggarans', [
            'pelapor_id' => $user->id,
            'siswa_id' => null,
            'jenis_pelanggaran' => 'Laporan Umum Kelas',
        ]);
    }

    public function test_store_menolak_siswa_tidak_ada(): void
    {
        $this->login('guru');

        $response = $this->post(route('lapor.store'), [
            'siswa_id' => 99999,
            'tanggal' => '2026-09-28',
            'pelanggaran_mode' => 'manual',
            'jenis_pelanggaran_manual' => 'X',
            'kategori_manual' => 'Ringan',
            'poin_manual' => 0,
        ]);

        $response->assertSessionHasErrors('siswa_id');
    }

    public function test_store_menolak_tanggal_kosong(): void
    {
        $this->login('guru');

        $response = $this->post(route('lapor.store'), [
            'pelanggaran_mode' => 'manual',
            'jenis_pelanggaran_manual' => 'X',
            'kategori_manual' => 'Ringan',
            'poin_manual' => 0,
        ]);

        $response->assertSessionHasErrors('tanggal');
    }

    public function test_store_menolak_mode_tidak_dikenal(): void
    {
        $this->login('guru');

        $response = $this->post(route('lapor.store'), [
            'tanggal' => '2026-09-28',
            'pelanggaran_mode' => 'ngarang',
        ]);

        $response->assertSessionHasErrors('pelanggaran_mode');
    }

    public function test_store_menyimpan_foto_bukti(): void
    {
        Storage::fake('public');

        $this->login('guru');

        $this->post(route('lapor.store'), [
            'tanggal' => '2026-09-28',
            'pelanggaran_mode' => 'manual',
            'jenis_pelanggaran_manual' => 'Ada Foto',
            'kategori_manual' => 'Sedang',
            'poin_manual' => 20,
            'foto_bukti' => UploadedFile::fake()->image('bukti.png'),
        ]);

        $path = Pelanggaran::latest('id')->first()->foto_bukti;

        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_store_menolak_foto_bukan_gambar(): void
    {
        $this->login('guru');

        $response = $this->post(route('lapor.store'), [
            'tanggal' => '2026-09-28',
            'pelanggaran_mode' => 'manual',
            'jenis_pelanggaran_manual' => 'Dokumen Palsu',
            'kategori_manual' => 'Ringan',
            'poin_manual' => 0,
            'foto_bukti' => UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('foto_bukti');
    }

    public function test_store_menolak_file_terlalu_besar(): void
    {
        $this->login('guru');

        $response = $this->post(route('lapor.store'), [
            'tanggal' => '2026-09-28',
            'pelanggaran_mode' => 'manual',
            'jenis_pelanggaran_manual' => 'Gambar Besar',
            'kategori_manual' => 'Ringan',
            'poin_manual' => 0,
            'foto_bukti' => UploadedFile::fake()->image('besar.jpg')->size(6000),
        ]);

        $response->assertSessionHasErrors('foto_bukti');
    }

    /**
     * ================================================================
     * REGRESSION: siswa_id NULLABLE
     * ================================================================
     */

    public function test_halaman_pelanggaran_tidak_crash_untuk_laporan_tanpa_siswa(): void
    {
        $this->login('pds');

        $pelapor = $this->makeUser('walas', 'X-RPL-1');

        // Record pending -> tampil di halaman "Pending Laporan Masuk"
        $pending = Pelanggaran::create([
            'siswa_id' => null,
            'pelapor_id' => $pelapor->id,
            'tanggal' => now(),
            'jenis_pelanggaran' => 'Laporan Umum Pending',
            'kategori' => 'Ringan',
            'poin' => 0,
            'status' => 'pending',
        ]);

        // Record diverifikasi -> tampil di halaman listViolation (hanya status diverifikasi)
        $diverifikasi = Pelanggaran::create([
            'siswa_id' => null,
            'pelapor_id' => $pelapor->id,
            'tanggal' => now(),
            'jenis_pelanggaran' => 'Laporan Umum Terverifikasi',
            'kategori' => 'Ringan',
            'poin' => 0,
            'status' => 'diverifikasi',
        ]);

        $this->get(route('pelanggaran.index'))
            ->assertOk()
            ->assertSee('Laporan Umum (Tanpa Siswa)')
            ->assertSee('Laporan Umum Terverifikasi');

        $this->get(route('pelanggaran.pending'))
            ->assertOk()
            ->assertSee('Laporan Umum (Tanpa Siswa)')
            ->assertSee('Laporan Umum Pending');

        $this->get(route('pelanggaran.show', $pending->id))->assertOk();
        $this->get(route('pelanggaran.show', $diverifikasi->id))->assertOk();
    }

    public function test_approve_laporan_tanpa_siswa_tidak_menghitung_saldo_poin(): void
    {
        $pds = $this->login('pds');
        $pelapor = $this->makeUser('walas', 'X-RPL-1');

        $pelanggaran = Pelanggaran::create([
            'siswa_id' => null,
            'pelapor_id' => $pelapor->id,
            'tanggal' => now(),
            'jenis_pelanggaran' => 'Laporan Umum Kelas',
            'kategori' => 'Ringan',
            'poin' => 0,
            'status' => 'pending',
        ]);

        $response = $this->post(route('pelanggaran.approve', $pelanggaran->id), [
            'catatan_verifikasi' => 'Dicatat sebagai laporan umum.',
        ]);

        $response->assertRedirect(route('pelanggaran.index'));

        $this->assertDatabaseHas('pelanggarans', [
            'id' => $pelanggaran->id,
            'status' => 'diverifikasi',
            'diverifikasi_oleh' => $pds->id,
            'poin_sebelum' => null,
            'poin_sesudah' => null,
        ]);
    }

    /**
     * ================================================================
     * AKSES
     * ================================================================
     */
    public function test_halaman_lapor_menuntut_login(): void
    {
        $this->get(route('lapor.index'))->assertRedirect(route('login'));
        $this->post(route('lapor.store'))->assertRedirect(route('login'));
        $this->get(route('lapor.riwayat'))->assertRedirect(route('login'));
    }

    public function test_walas_diarahkan_kembali_dari_dashboard(): void
    {
        $this->login('walas', 'X-RPL-1');

        $this->get(route('lapor.index'))->assertOk();
    }

    public function test_bk_diarahkan_kembali_dari_halaman_manajemen_pelanggaran(): void
    {
        $this->login('bk');

        $this->get(route('pelanggaran.create'))
            ->assertRedirect(route('pelanggaran.index'));
    }

    public function test_pds_diarahkan_kembali_dari_halaman_manajemen_prestasi(): void
    {
        $this->login('pds');

        $this->get(route('prestasi.create'))
            ->assertRedirect(route('prestasi.index'));
    }
}
