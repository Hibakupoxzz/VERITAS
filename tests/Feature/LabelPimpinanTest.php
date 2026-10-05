<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\GuruTenagaPendidikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Label jabatan untuk unsur pimpinan sekolah.
 *
 * Bug aslinya: keenam unsur pimpinan punya role "guru", dan label role
 * untuk "guru" di-hardcode "Guru Khusus / Piket". Akibatnya kepsek dan
 * lima waka tampil sebagai guru piket di seluruh aplikasi.
 *
 * Perbaikannya bukan memberi role baru — hak aksesnya memang dibiarkan
 * apa adanya — melainkan menyimpan jabatan resmi di kolom `jabatan`
 * lalu menurunkan label dari situ.
 */
class LabelPimpinanTest extends TestCase
{
    use RefreshDatabase;

    private function seedGuru(): void
    {
        $this->seed(GuruTenagaPendidikSeeder::class);
    }

    public function test_unsur_pimpinan_tidak_lagi_tertampil_sebagai_guru_piket(): void
    {
        $this->seedGuru();

        $pimpinan = User::whereIn('nig', [
            '0250186083', '0170194006', '0180193040',
            '0170188005', '0190195044', '0170167004',
        ])->get();

        $this->assertCount(6, $pimpinan);

        foreach ($pimpinan as $user) {
            $this->assertNotSame(
                'Guru Khusus / Piket',
                $user->role_label,
                "{$user->name} masih tampil sebagai Guru Khusus / Piket"
            );
            $this->assertTrue($user->isPimpinan());
        }
    }

    public function test_label_pimpinan_mengikuti_jabatan_resmi(): void
    {
        $this->seedGuru();

        $harapan = [
            '0250186083' => 'Kepala Sekolah',
            '0170194006' => 'Waka Kurikulum',
            '0180193040' => 'Waka Hubungan Masyarakat',
            '0170188005' => 'Waka Hubungan Industri',
            '0190195044' => 'Waka Kesiswaan',
            '0170167004' => 'Waka Sarana Prasarana',
        ];

        foreach ($harapan as $nig => $label) {
            $this->assertSame(
                $label,
                User::where('nig', $nig)->first()->role_label,
                "Label salah untuk NIG {$nig}"
            );
        }
    }

    /**
     * Jabatan resmi yang panjang dipangkas untuk label, tapi kolomnya
     * tetap menyimpan isi utuh supaya tidak ada informasi yang hilang.
     */
    public function test_jabatan_utuh_tersimpan_saat_label_dipangkas(): void
    {
        $this->seedGuru();

        $septia = User::where('nig', '0180193040')->first();

        $this->assertStringContainsString(
            'Guru Pendidikan Matematika',
            $septia->jabatan,
            'Jabatan resmi harus utuh tersimpan di kolom jabatan'
        );
        $this->assertSame('Waka Hubungan Masyarakat', $septia->jabatanSingkat());
    }

    /**
     * Label "Guru Khusus / Piket" tetap milik guru piket sungguhan.
     */
    public function test_guru_piket_asli_tetap_berlabel_guru_khusus(): void
    {
        $this->seedGuru();

        User::create([
            'name' => 'Guru Piket (Umum)',
            'email' => 'gurupiket@guru.com',
            'password' => Hash::make('guru123'),
            'role' => 'guru',
            'jabatan' => 'Guru Piket',
            'email_verified_at' => now(),
        ]);

        $piket = User::where('email', 'gurupiket@guru.com')->first();

        $this->assertFalse($piket->isPimpinan());
        $this->assertSame('Guru Khusus / Piket', $piket->role_label);
    }

    /**
     * Guru biasa tidak boleh ikut terbaca sebagai pimpinan.
     */
    public function test_guru_biasa_bukan_pimpinan(): void
    {
        $this->seedGuru();

        foreach (User::whereNotNull('jabatan')->get() as $user) {
            $iniPimpinan = $user->isPimpinan();

            if ($user->jabatan === 'Kepala Sekolah'
                || str_contains($user->jabatan, 'Waka')) {
                $this->assertTrue($iniPimpinan, "{$user->name} seharusnya pimpinan");

                continue;
            }

            $this->assertFalse($iniPimpinan, "{$user->name} salah dihitung sebagai pimpinan");
        }
    }
}
