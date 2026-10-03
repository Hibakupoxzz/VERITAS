<?php

namespace Tests\Feature;

use App\Exports\PelanggaranHarianExport;
use App\Exports\PelanggaranMingguanExport;
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
     * STORE
     * ================================================================
     */
    public function test_store_menyimpan_kategori_dan_poin_dari_aturan(): void
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

    public function test_store_menolak_aturan_tidak_aktif(): void
    {
        $this->login('guru');

        $aturan = AturanPelanggaran::create([
            'kode' => 'C1', 'kategori' => 'Ringan', 'nama' => 'Nonaktif',
            'poin' => 10, 'aktif' => false,
        ]);

        $response = $this->post(route('lapor.store'), [
            'tanggal' => '2026-09-28',
            'aturan_pelanggaran_id' => $aturan->id,
        ]);

        $response->assertSessionHasErrors('aturan_pelanggaran_id');
        $this->assertDatabaseCount('pelanggarans', 0);
    }

    public function test_store_wajib_memilih_aturan(): void
    {
        $this->login('guru');

        $response = $this->post(route('lapor.store'), [
            'tanggal' => '2026-09-28',
            'aturan_pelanggaran_id' => '',
        ]);

        $response->assertSessionHasErrors('aturan_pelanggaran_id');
        $this->assertDatabaseCount('pelanggarans', 0);
    }

    /**
     * Tidak ada lagi input manual: nilai harus selalu
     * mengikuti master aturan,walaupun pun request memaksa
     * mengirim poin / kategori / nama sendiri.
     */
    public function test_store_mengabaikan_input_kategori_dan_poin(): void
    {
        $this->login('guru');

        $aturan = AturanPelanggaran::create([
            'kode' => 'D1', 'kategori' => 'Ringan', 'nama' => 'Terbawa Aturan',
            'poin' => 15, 'aktif' => true,
        ]);

        $response = $this->post(route('lapor.store'), [
            'tanggal' => '2026-09-28',
            'aturan_pelanggaran_id' => $aturan->id,
            // Field lama dari mode manual, harus diabaikan.
            'jenis_pelanggaran_manual' => 'Nama Palsu',
            'kategori_manual' => 'Luar Biasa',
            'poin_manual' => 999,
        ]);

        $response->assertRedirect(route('lapor.index'));
        $response->assertSessionDoesntHaveErrors();

        $this->assertDatabaseHas('pelanggarans', [
            'jenis_pelanggaran' => 'Terbawa Aturan',
            'kategori' => 'Ringan',
            'poin' => 15,
        ]);
    }

    public function test_store_menerima_siswa_kosong(): void
    {
        $user = $this->login('guru');

        $aturan = AturanPelanggaran::create([
            'kode' => 'E1', 'kategori' => 'Ringan', 'nama' => 'Laporan Umum Kelas',
            'poin' => 5, 'aktif' => true,
        ]);

        $response = $this->post(route('lapor.store'), [
            'tanggal' => '2026-09-28',
            'aturan_pelanggaran_id' => $aturan->id,
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

        $aturan = AturanPelanggaran::create([
            'kode' => 'F1', 'kategori' => 'Ringan', 'nama' => 'X',
            'poin' => 5, 'aktif' => true,
        ]);

        $response = $this->post(route('lapor.store'), [
            'siswa_id' => 99999,
            'tanggal' => '2026-09-28',
            'aturan_pelanggaran_id' => $aturan->id,
        ]);

        $response->assertSessionHasErrors('siswa_id');
    }

    public function test_store_menolak_tanggal_kosong(): void
    {
        $this->login('guru');

        $aturan = AturanPelanggaran::create([
            'kode' => 'G1', 'kategori' => 'Ringan', 'nama' => 'X',
            'poin' => 5, 'aktif' => true,
        ]);

        $response = $this->post(route('lapor.store'), [
            'aturan_pelanggaran_id' => $aturan->id,
        ]);

        $response->assertSessionHasErrors('tanggal');
    }

    public function test_store_menyimpan_foto_bukti(): void
    {
        Storage::fake('public');

        $this->login('guru');

        $aturan = AturanPelanggaran::create([
            'kode' => 'H1', 'kategori' => 'Sedang', 'nama' => 'Ada Foto',
            'poin' => 20, 'aktif' => true,
        ]);

        $this->post(route('lapor.store'), [
            'tanggal' => '2026-09-28',
            'aturan_pelanggaran_id' => $aturan->id,
            'foto_bukti' => UploadedFile::fake()->image('bukti.png'),
        ]);

        $path = Pelanggaran::latest('id')->first()->foto_bukti;

        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_store_menolak_foto_bukan_gambar(): void
    {
        $this->login('guru');

        $aturan = AturanPelanggaran::create([
            'kode' => 'I1', 'kategori' => 'Ringan', 'nama' => 'Dokumen Palsu',
            'poin' => 0, 'aktif' => true,
        ]);

        $response = $this->post(route('lapor.store'), [
            'tanggal' => '2026-09-28',
            'aturan_pelanggaran_id' => $aturan->id,
            'foto_bukti' => UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('foto_bukti');
    }

    public function test_store_menolak_file_terlalu_besar(): void
    {
        $this->login('guru');

        $aturan = AturanPelanggaran::create([
            'kode' => 'J1', 'kategori' => 'Ringan', 'nama' => 'Gambar Besar',
            'poin' => 0, 'aktif' => true,
        ]);

        $response = $this->post(route('lapor.store'), [
            'tanggal' => '2026-09-28',
            'aturan_pelanggaran_id' => $aturan->id,
            'foto_bukti' => UploadedFile::fake()->image('besar.jpg')->size(6000),
        ]);

        $response->assertSessionHasErrors('foto_bukti');
    }

    /**
     * ================================================================
     * RINCIAN LAPORAN DI HALAMAN PENDING
     * ================================================================
     */
    public function test_halaman_data_pelanggaran_terurut_berdasarkan_tanggal_kejadian(): void
    {
        $this->login('pds');

        $pelapor = $this->makeUser('walas', 'X-RPL-1');
        $siswa = Siswa::create(['nama' => 'Siswa Urut', 'nisn' => '9201', 'kelas' => 'X-RPL-1']);

        // Dibuat belakangan, tapi tanggal kejadian paling lama.
        // Kalau diurutkan created_at, ini akan muncul paling atas.
        Pelanggaran::create([
            'siswa_id' => $siswa->id, 'pelapor_id' => $pelapor->id,
            'tanggal' => now()->subDays(30), 'jenis_pelanggaran' => 'Paling Lama',
            'poin' => 10, 'status' => 'diverifikasi',
        ]);

        Pelanggaran::create([
            'siswa_id' => $siswa->id, 'pelapor_id' => $pelapor->id,
            'tanggal' => today(), 'jenis_pelanggaran' => 'Paling Baru',
            'poin' => 5, 'status' => 'diverifikasi',
        ]);

        $response = $this->get(route('pelanggaran.index'));

        $response->assertOk();
        $response->assertViewHas('pelanggarans', function ($list) {
            return $list->count() === 2
                && $list->first()->jenis_pelanggaran === 'Paling Baru'
                && $list->last()->jenis_pelanggaran === 'Paling Lama';
        });
    }

    public function test_halaman_data_pelanggaran_hanya_menampilkan_yang_diverifikasi(): void
    {
        $this->login('pds');

        $pelapor = $this->makeUser('walas', 'X-RPL-1');
        $siswa = Siswa::create(['nama' => 'Siswa Filter', 'nisn' => '9202', 'kelas' => 'X-RPL-1']);

        foreach (['pending', 'ditolak'] as $status) {
            Pelanggaran::create([
                'siswa_id' => $siswa->id, 'pelapor_id' => $pelapor->id,
                'tanggal' => today(), 'jenis_pelanggaran' => 'Status '.$status,
                'poin' => 5, 'status' => $status,
            ]);
        }

        Pelanggaran::create([
            'siswa_id' => $siswa->id, 'pelapor_id' => $pelapor->id,
            'tanggal' => today(), 'jenis_pelanggaran' => 'Status diverifikasi',
            'poin' => 5, 'status' => 'diverifikasi',
        ]);

        $response = $this->get(route('pelanggaran.index'));

        $response->assertOk();
        $response->assertSee('Status diverifikasi');
        $response->assertDontSee('Status pending');
        $response->assertDontSee('Status ditolak');
    }

    public function test_halaman_data_pelanggaran_menampilkan_kategori_dan_pelapor(): void
    {
        $this->login('pds');

        $pelapor = $this->makeUser('walas', 'X-RPL-1');
        $siswa = Siswa::create(['nama' => 'Siswa Kolom', 'nisn' => '9203', 'kelas' => 'X-RPL-1']);

        Pelanggaran::create([
            'siswa_id' => $siswa->id, 'pelapor_id' => $pelapor->id,
            'tanggal' => today(), 'jenis_pelanggaran' => 'Bikin Rusuh',
            'kategori' => 'Berat', 'poin' => 40,
            'poin_sebelum' => 100, 'poin_sesudah' => 60,
            'status' => 'diverifikasi',
        ]);

        $response = $this->get(route('pelanggaran.index'));

        $response->assertOk();
        $response->assertSee('Berat');
        $response->assertSee($pelapor->name);
        $response->assertSee('Wali Kelas');
        $response->assertSee('100', false);
        $response->assertSee('60', false);
    }

    /**
     * ================================================================
     * EXPORT
     * ================================================================
     */
    public function test_export_hanya_mengambil_laporan_diverifikasi(): void
    {
        $this->login('pds');

        $pelapor = $this->makeUser('walas', 'X-RPL-1');
        $siswa = Siswa::create(['nama' => 'Siswa Export', 'nisn' => '9204', 'kelas' => 'X-RPL-1']);

        Pelanggaran::create([
            'siswa_id' => $siswa->id, 'pelapor_id' => $pelapor->id,
            'tanggal' => today(), 'jenis_pelanggaran' => 'Ekspor Pending',
            'poin' => 5, 'status' => 'pending',
        ]);
        Pelanggaran::create([
            'siswa_id' => $siswa->id, 'pelapor_id' => $pelapor->id,
            'tanggal' => today(), 'jenis_pelanggaran' => 'Ekspor Ditolak',
            'poin' => 5, 'status' => 'ditolak',
        ]);
        Pelanggaran::create([
            'siswa_id' => $siswa->id, 'pelapor_id' => $pelapor->id,
            'tanggal' => today(), 'jenis_pelanggaran' => 'Ekspor Diverifikasi',
            'poin' => 5, 'status' => 'diverifikasi',
        ]);

        foreach ([PelanggaranHarianExport::class, PelanggaranMingguanExport::class] as $exportClass) {
            $nama = (new $exportClass)->collection()->pluck('Pelanggaran')->all();

            $this->assertContains('Ekspor Diverifikasi', $nama, $exportClass);
            $this->assertNotContains('Ekspor Pending', $nama, $exportClass);
            $this->assertNotContains('Ekspor Ditolak', $nama, $exportClass);
        }
    }

    public function test_pending_menampilkan_rincian_laporan_lengkap(): void
    {
        $this->login('pds');

        $pelapor = $this->makeUser('walas', 'X-RPL-1');
        $siswa = Siswa::create(['nama' => 'Siswa Rincian', 'nisn' => '9001', 'kelas' => 'X-RPL-1']);

        $aturan = AturanPelanggaran::create([
            'kode' => 'Z9', 'kategori' => 'Berat', 'nama' => 'Bikin Rusuh',
            'poin' => 40, 'aktif' => true,
        ]);

        $pelanggaran = Pelanggaran::create([
            'siswa_id' => $siswa->id,
            'pelapor_id' => $pelapor->id,
            'aturan_pelanggaran_id' => $aturan->id,
            'tanggal' => '2026-09-28',
            'jenis_pelanggaran' => 'Bikin Rusuh',
            'kategori' => 'Berat',
            'poin' => 40,
            'keterangan' => 'Nyaris keributan di koridor.',
            'status' => 'pending',
        ]);

        $response = $this->get(route('pelanggaran.pending'));

        $response->assertOk();

        // Kartu ringkas
        $response->assertSee('Siswa Rincian');
        $response->assertSee('X-RPL-1');
        $response->assertSee('Bikin Rusuh');
        $response->assertSee('-40 Poin');

        // Rincian lengkap TIDAK lagi ditampilkan inline
        $response->assertDontSee('Riwayat Teguran 90 Hari Terakhir');

        // Link ke halaman rincian
        $response->assertSee('Lihat Rincian Laporan');
        $response->assertSee(route('pelanggaran.show', ['pelanggaran' => $pelanggaran->id, 'from' => 'pending']), false);

        // Data konteks yang dikirim controller
        $response->assertViewHas('ringkasanSiswa');
        $response->assertViewHas('riwayatPerSiswa');
    }

    public function test_halaman_rincian_menampilkan_data_lengkap(): void
    {
        $this->login('pds');

        $pelapor = $this->makeUser('walas', 'X-RPL-1');
        $siswa = Siswa::create(['nama' => 'Siswa Detail', 'nisn' => '9101', 'kelas' => 'X-RPL-1']);

        $aturan = AturanPelanggaran::create([
            'kode' => 'Y7', 'kategori' => 'Sedang', 'nama' => 'Bikin Rusuh',
            'poin' => 40, 'aktif' => true,
        ]);

        $pelanggaran = Pelanggaran::create([
            'siswa_id' => $siswa->id,
            'pelapor_id' => $pelapor->id,
            'aturan_pelanggaran_id' => $aturan->id,
            'tanggal' => '2026-09-28',
            'jenis_pelanggaran' => 'Bikin Rusuh',
            'kategori' => 'Sedang',
            'poin' => 40,
            'keterangan' => 'Nyaris keributan di koridor.',
            'status' => 'pending',
        ]);

        // Teguran sebelumnya -> harus muncul di riwayat + saldo
        Pelanggaran::create([
            'siswa_id' => $siswa->id, 'pelapor_id' => $pelapor->id,
            'tanggal' => now()->subDays(7), 'jenis_pelanggaran' => 'Terlambat Masuk',
            'poin' => 10, 'status' => 'diverifikasi',
        ]);

        $response = $this->get(route('pelanggaran.show', $pelanggaran->id));

        $response->assertOk();

        // Identitas
        $response->assertSee('Siswa Detail');
        $response->assertSee('9101');
        $response->assertSee('X-RPL-1');

        // Ringkasan: saldo 100 - 10 = 90
        $response->assertSee('90 Poin');
        $response->assertSee('Riwayat Teguran 90 Hari Terakhir');
        $response->assertSee('Terlambat Masuk');

        // Isi laporan
        $response->assertSee('Y7');
        $response->assertSee('Bikin Rusuh');
        $response->assertSee('Nyaris keributan di koridor.');
        $response->assertSee('Menunggu Verifikasi');

        $response->assertViewHas('ringkasan');
        $response->assertViewHas('riwayat');
    }

    public function test_halaman_rincian_menampilkan_catatan_verifikasi(): void
    {
        $this->login('pds');

        $pelapor = $this->makeUser('walas', 'X-RPL-1');
        $siswa = Siswa::create(['nama' => 'Siswa Catatan', 'nisn' => '9102', 'kelas' => 'X-RPL-1']);

        $pds = auth()->user();

        $pelanggaran = Pelanggaran::create([
            'siswa_id' => $siswa->id,
            'pelapor_id' => $pelapor->id,
            'tanggal' => now(),
            'jenis_pelanggaran' => 'Sudah Diverifikasi',
            'poin' => 5,
            'poin_sebelum' => 100,
            'poin_sesudah' => 95,
            'status' => 'diverifikasi',
            'diverifikasi_oleh' => $pds->id,
            'catatan_verifikasi' => 'Bukti foto sudah jelas dan cukup.',
        ]);

        $response = $this->get(route('pelanggaran.show', $pelanggaran->id));

        $response->assertOk();
        $response->assertSee('Terverifikasi');
        $response->assertSee('100 → 95 Poin');
        $response->assertSee($pds->name);
        $response->assertSee('Bukti foto sudah jelas dan cukup');
    }

    public function test_halaman_rincian_tanpa_siswa_tidak_menampilkan_riwayat(): void
    {
        $this->login('pds');

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

        $response = $this->get(route('pelanggaran.show', $pelanggaran->id));

        $response->assertOk();
        $response->assertSee('Laporan Umum (Tanpa Siswa)');
        $response->assertSee('Tidak terkait aturan master');
        $response->assertSee('Belum dihitung (menunggu verifikasi)');
        $response->assertDontSee('Riwayat Teguran 90 Hari Terakhir');

        $response->assertViewHas('ringkasan', fn ($r) => $r === null);
        $response->assertViewHas('riwayat', fn ($r) => $r->isEmpty());
    }

    public function test_pending_menghitung_saldo_poin_hanya_dari_yang_diverifikasi(): void
    {
        $this->login('pds');

        $pelapor = $this->makeUser('walas', 'X-RPL-1');
        $siswa = Siswa::create(['nama' => 'Siswa Saldo', 'nisn' => '9002', 'kelas' => 'X-RPL-1']);

        // Terverifikasi: 10 poin -> saldo 90
        Pelanggaran::create([
            'siswa_id' => $siswa->id, 'pelapor_id' => $pelapor->id,
            'tanggal' => now()->subDays(5), 'jenis_pelanggaran' => 'Terverifikasi',
            'poin' => 10, 'status' => 'diverifikasi',
        ]);

        // Pending: harus TIDAK ikut dihitung
        Pelanggaran::create([
            'siswa_id' => $siswa->id, 'pelapor_id' => $pelapor->id,
            'tanggal' => now(), 'jenis_pelanggaran' => 'Masih Pending',
            'poin' => 25, 'status' => 'pending',
        ]);

        // Ditolak: harus TIDAK ikut dihitung
        Pelanggaran::create([
            'siswa_id' => $siswa->id, 'pelapor_id' => $pelapor->id,
            'tanggal' => now()->subDays(2), 'jenis_pelanggaran' => 'Ditolak',
            'poin' => 30, 'status' => 'ditolak',
        ]);

        $response = $this->get(route('pelanggaran.pending'));

        $response->assertOk();

        $response->assertViewHas('ringkasanSiswa', function ($map) use ($siswa) {
            $ringkasan = $map->get($siswa->id);

            return $ringkasan
                && $ringkasan->jumlah_pelanggaran === 1
                && $ringkasan->saldo_poin === 90;
        });
    }

    public function test_pending_menampilkan_riwayat_teguran_terakhir(): void
    {
        $this->login('pds');

        $pelapor = $this->makeUser('walas', 'X-RPL-1');
        $siswa = Siswa::create(['nama' => 'Siswa Riwayat', 'nisn' => '9003', 'kelas' => 'X-RPL-1']);

        Pelanggaran::create([
            'siswa_id' => $siswa->id, 'pelapor_id' => $pelapor->id,
            'tanggal' => now()->subDays(10), 'jenis_pelanggaran' => 'Terlambat Masuk',
            'poin' => 5, 'status' => 'diverifikasi',
        ]);

        Pelanggaran::create([
            'siswa_id' => $siswa->id, 'pelapor_id' => $pelapor->id,
            'tanggal' => now()->subDays(200), 'jenis_pelanggaran' => 'Di Luar Jendela 90 Hari',
            'poin' => 7, 'status' => 'diverifikasi',
        ]);

        Pelanggaran::create([
            'siswa_id' => $siswa->id, 'pelapor_id' => $pelapor->id,
            'tanggal' => now(), 'jenis_pelanggaran' => 'Laporan Baru Menunggu',
            'poin' => 3, 'status' => 'pending',
        ]);

        $response = $this->get(route('pelanggaran.pending'));

        $response->assertOk();

        $response->assertViewHas('riwayatPerSiswa', function ($map) use ($siswa) {
            $riwayat = $map->get($siswa->id);

            // Hanya yang terverifikasi DAN dalam 90 hari terakhir.
            return $riwayat !== null
                && $riwayat->count() === 1
                && $riwayat->first()->jenis_pelanggaran === 'Terlambat Masuk';
        });
    }

    public function test_pending_tidak_menghitung_konteks_untuk_laporan_tanpa_siswa(): void
    {
        $this->login('pds');

        $pelapor = $this->makeUser('walas', 'X-RPL-1');

        Pelanggaran::create([
            'siswa_id' => null,
            'pelapor_id' => $pelapor->id,
            'tanggal' => now(),
            'jenis_pelanggaran' => 'Laporan Umum Kelas',
            'kategori' => 'Ringan',
            'poin' => 0,
            'status' => 'pending',
        ]);

        $response = $this->get(route('pelanggaran.pending'));

        $response->assertOk();
        $response->assertViewHas('ringkasanSiswa', fn ($map) => $map->isEmpty());
        $response->assertViewHas('riwayatPerSiswa', fn ($map) => $map->isEmpty());
        $response->assertSee('Laporan Umum (Tanpa Siswa)');
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
