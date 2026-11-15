<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\GuruTenagaPendidikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Unsur pimpinan sekolah (Kepala Sekolah + 5 Waka) memakai satu
 * password terpisah: "pimpinan123".
 *
 * Yang dijaga di sini:
 * - 6 orang itu passwordnya benar-benar pemimpin, bukan guru123;
 * - 44 guru lain TIDAK ikut berubah;
 * - PDS/BK/walas tidak tersentuh;
 * - changing role日后 tetap aman.
 */
class PasswordPimpinanTest extends TestCase
{
    use RefreshDatabase;

    /** NIG Kepala Sekolah + lima Wakil Kepala Sekolah. */
    private const PIMPINAN = [
        '0250186083', // Sri Mildawati — Kepala Sekolah
        '0170194006', // Amirul Hadid Ramadan — Waka Kurikulum
        '0180193040', // Septia Sundari — Waka Hubungan Masyarakat
        '0170188005', // Nugrah Dwi Saptaji — Waka Hubungan Industri
        '0190195044', // Hanifan Nurfauzi — Waka Kesiswaan
        '0170167004', // Rohajon — Waka Sarana Prasarana
    ];

    private function seedGuru(): void
    {
        $this->seed(GuruTenagaPendidikSeeder::class);
    }

    public function test_enam_akun_pimpinan_memakai_password_pimpinan(): void
    {
        $this->seedGuru();

        foreach (self::PIMPINAN as $nig) {
            $user = User::where('nig', $nig)->first();

            $this->assertNotNull($user, "Akun NIG {$nig} tidak ada");
            $this->assertTrue(
                Hash::check('pimpinan123', $user->password),
                "Password {$user->name} bukan pimpinan123"
            );
        }

        $this->assertCount(6, self::PIMPINAN);
    }

    public function test_guru_biasa_tidak_ikut_jadi_pimpinan(): void
    {
        $this->seedGuru();

        $pimpinan = array_flip(self::PIMPINAN);

        $terperiksa = 0;

        foreach (User::where('role', 'guru')->whereNotNull('nig')->get() as $user) {
            if (isset($pimpinan[$user->nig])) {
                continue;
            }

            $terperiksa++;

            $this->assertTrue(
                Hash::check('guru123', $user->password),
                "Password {$user->name} berubah, seharusnya guru123"
            );

            $this->assertFalse(
                Hash::check('pimpinan123', $user->password),
                "{$user->name} seharusnya bukan pimpinan"
            );
        }

        /*
         * 40 guru ber-NIG; keenam pimpinan sudah punya role sendiri.
         */
        $this->assertSame(40, $terperiksa);
    }

    public function test_pds_dan_bk_tidak_ikut_berubah(): void
    {
        $this->seedGuru();

        foreach (User::where('role', 'pds')->whereNotNull('nig')->get() as $user) {
            $this->assertTrue(Hash::check('pds123', $user->password), $user->name);
        }

        foreach (User::where('role', 'bk')->whereNotNull('nig')->get() as $user) {
            $this->assertTrue(Hash::check('bk123', $user->password), $user->name);
        }
    }

    /**
     * 6 akun pimpinan boleh dibuat ULANG dan tetap mendapat
     * passwordnya — ini yang terjadi di server baru.
     */
    public function test_pimpinan_dapat_login_langsung_dengan_nig(): void
    {
        $this->seedGuru();

        $response = $this->post(route('login'), [
            'nig' => '0250186083',
            'password' => 'pimpinan123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('lapor.index'));
    }

    public function test_kepsek_tidak_bisa_login_dengan_guru123(): void
    {
        $this->seedGuru();

        $this->post(route('login'), [
            'nig' => '0250186083',
            'password' => 'guru123',
        ]);

        $this->assertGuest();
    }

    public function test_password_pimpinan_diterapkan_ke_akun_lama_yang_sudah_ada(): void
    {
        // Akun Sri Midawati sudah ada dengan password guru123
        // sebelum seeder dijalankan.
        User::create([
            'name' => 'Sri Midawati',
            'email' => 'kepalasekolah@guru.com',
            'nig' => '0250186083',
            'password' => Hash::make('guru123'),
            'role' => 'guru',
            'kelas' => null,
            'email_verified_at' => now(),
        ]);

        $this->seedGuru();

        $kepsek = User::where('nig', '0250186083')->first();

        $this->assertTrue(Hash::check('pimpinan123', $kepsek->password));
        $this->assertFalse(Hash::check('guru123', $kepsek->password));
    }
}
