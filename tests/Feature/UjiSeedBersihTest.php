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
            'admin' => 1,
            'bk' => 8,
            'guru' => 46,
            'pds' => 5,
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
        $cek('sopiyan@pds.com', 'pds123');
        $cek('rakhmadhania@bk.com', 'bk123');
        $cek('xrpl1@guru.com', 'walas123');
        $cek('gurupiket@guru.com', 'guru123');
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
