<?php

namespace Tests\Feature;

use App\Models\AturanPelanggaran;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Menjalankan DatabaseSeeder dari nol harus menghasilkan akun yang
 * sama persis dengan database yang sudah berjalan.
 *
 * Ini yang menguji pertanyaan "semua ada di seeder?": kalau ada
 * akun yang hanya dibuat lewat perintah manual, `migrate:fresh
 * --seed` di server baru akanynyillis dan akun itu hilang.
 */
class UjiSeedBersihTest extends TestCase
{
    use RefreshDatabase;

    private function seedSemua(): void
    {
        $this->seed(DatabaseSeeder::class);
    }

    public function test_jumlah_akun_setelah_seed_bersih(): void
    {
        $this->seedSemua();

        $this->assertSame(106, User::count());
    }

    public function test_pembagian_role_setelah_seed_bersih(): void
    {
        $this->seedSemua();

        $hitung = User::selectRaw('role, count(*) as j')
            ->groupBy('role')
            ->pluck('j', 'role')
            ->all();

        $this->assertSame([
            'admin' => 2,
            'bk' => 8,
            'guru' => 40,
            'pds' => 4,
            'pimpinan' => 6,
            'walas' => 46,
        ], $hitung);
    }

    public function test_semua_58_guru_punya_nig(): void
    {
        $this->seedSemua();

        $this->assertSame(58, User::whereNotNull('nig')->count());
        $this->assertSame(0, User::whereNotNull('nig')->count() - User::distinct()->count('nig'));
    }

    /**
     * Password harus sesuai daftar, termasuk yang sudah punya akun
     * sebelum seeding (PdsUserSeeder/BkUserSeeder).
     */
    public function test_password_sesuai_daftar_setelah_seed_bersih(): void
    {
        $this->seedSemua();

        $cek = function (string $email, string $password) {
            $user = User::where('email', $email)->first();

            $this->assertNotNull($user, "Akun {$email} tidak ada");
            $this->assertTrue(
                Hash::check($password, $user->password),
                "Password {$email} bukan {$password}"
            );
        };

        $cek('duospirits@gmail.com', 'zidan123');
        $cek('smkpluspnb@gmail.com', 'smkpluspnb123');
        $cek('sopiyan@pds.com', 'pds123');
        $cek('rakhmadhania@bk.com', 'bk123');
        $cek('xrpl1@guru.com', 'walas123');
    }

    /**
     * Akun contoh lama gurupiket@guru.com sudah dihapus dan tidak boleh
     * muncul lagi setelah seed bersih.
     */
    public function test_akun_guru_piket_umum_sudah_tidak_ada(): void
    {
        $this->seedSemua();

        $this->assertNull(User::where('email', 'gurupiket@guru.com')->first());
    }

    /**
     * Dua akun pimpinan dengan hak akses khusus harus tetap mendapat
     * tanda yang benar setelah seed bersih.
     */
    public function test_akses_khusus_dua_pimpinan(): void
    {
        $this->seedSemua();

        $hanifi = User::where('nig', '0190195044')->first();  // Waka Kesiswaan
        $septia = User::where('nig', '0180193040')->first();  // Waka Humas

        // Hanifan: semua fitur, setara Super Admin.
        $this->assertTrue($hanifi->hasFullAccess(), 'Waka Kesiswaan harus punya akses penuh');
        $this->assertTrue($hanifi->canVerify(), 'Waka Kesiswaan harus bisa verifikasi seperti PDS');
        $this->assertTrue($hanifi->canManagePrestasi(), 'Waka Kesiswaan harus bisa kelola prestasi seperti BK');
        $this->assertTrue($hanifi->canDirectAddPelanggaran());

        // Septia: tambah & verifikasi pelanggaran seperti PDS, tanpa prestasi.
        $this->assertTrue($septia->isPelanggaranWriter(), 'Waka Humas harus boleh menambah pelanggaran');
        $this->assertTrue($septia->canVerify(), 'Waka Humas harus bisa memverifikasi seperti PDS');
        $this->assertFalse($septia->canManagePrestasi());
        $this->assertFalse($septia->hasFullAccess());
    }

    public function test_dua_super_admin_punya_nama_dan_email_benar(): void
    {
        $this->seedSemua();

        $this->assertSame(
            'Admin',
            User::where('email', 'duospirits@gmail.com')->first()->name
        );

        $this->assertSame(
            'smkpluspnb',
            User::where('email', 'smkpluspnb@gmail.com')->first()->name
        );

        $this->assertSame(2, User::where('role', 'admin')->count());
    }

    public function test_nig_bisa_dipakai_login_setelah_seed_bersih(): void
    {
        $this->seedSemua();

        $this->post(route('login'), ['nig' => '0170179003', 'password' => 'pds123']);

        $this->assertAuthenticated();
        $this->assertSame('pds', auth()->user()->role);
    }

    /**
     * Kelas bingeunan PDS/BK harus tetap ada — ini yang paling rawan
     * hilang kalau seeder menimpa akun yang sudah ada.
     */
    public function test_kelas_binaan_tetap_ada_setelah_seed_bersih(): void
    {
        $this->seedSemua();

        $sopiyan = User::where('email', 'sopiyan@pds.com')->first();
        $kelas = json_decode($sopiyan->kelas, true);

        $this->assertIsArray($kelas);
        $this->assertContains('XI-TKJ-1', $kelas);
        $this->assertContains('XII-DKV-3', $kelas);

        $dodi = User::where('email', 'rdodisetiadi@bk.com')->first();
        $this->assertContains('XI-RPL-1', json_decode($dodi->kelas, true));
    }

    /**
     * Kelas binaan Yulianie Kasari dibagi ke Hanifan, Septia, dan
     * Sopiyan. Pembagian ini harus ikut terbentuk pada seed bersih.
     */
    public function test_pembagian_kelas_binaan_yulianie(): void
    {
        $this->seedSemua();

        $kelas = function (string $nig): array {
            $user = User::where('nig', $nig)->firstOrFail();

            return json_decode($user->kelas, true) ?? [];
        };

        $this->assertSame(
            ['X-RPL-1', 'XI-RPL-2', 'XI-TKJ-2'],
            $kelas('0190195044'),
            'Kelas Hanifan Nurfauzi tidak sesuai'
        );

        $this->assertSame(
            ['XI-RPL-1', 'XII-TKJ-3', 'XII-DKV-1'],
            $kelas('0180193040'),
            'Kelas Septia Sundari tidak sesuai'
        );

        $this->assertSame(
            ['X-TKJ-1', 'XI-TKJ-1', 'XII-DKV-3', 'XII-LPB-1', 'XII-DKV-4'],
            $kelas('0170179003'),
            'Kelas Sopiyan tidak sesuai'
        );

        $this->assertSame([], $kelas('0250189080'), 'Yulianie tidak boleh lagi memegang kelas');
    }

    public function test_aturan_pelanggaran_juga_terisi(): void
    {
        $this->seedSemua();

        $this->assertGreaterThan(50, AturanPelanggaran::count());
    }

    /**
     * Menjalankan DatabaseSeeder dua kali tidak boleh menggandakan
     * akun — ini yang terjadi kalau seeder tidak idempoten.
     */
    public function test_seeder_bersih_bisa_dijalankan_berulang(): void
    {
        $this->seedSemua();
        $sebelum = User::count();

        $this->seedSemua();

        $this->assertSame($sebelum, User::count());
    }
}
