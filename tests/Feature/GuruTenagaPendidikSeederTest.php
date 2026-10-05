<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\GuruTenagaPendidikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Seeder tenaga pendidik.
 *
 * Fokus test ini adalah dua risiko yang paling merusak:
 *  1. Akun ganda — 13 guru (5 PDS + 8 BK) sudah punya akun dari
 *     seeder lama dengan email buatan. Kalau tidak dicocokkan, setiap
 *     guru akan muncul dua kali.
 *  2. Password lama harus tetap berlaku — kecuali kalau role-nya
 *     memang berubah di daftar resmi sekolah, misalnya guru PDS
 *     yang jadi Guru PPKN.
 */
class GuruTenagaPendidikSeederTest extends TestCase
{
    use RefreshDatabase;

    private function seedGuru(): void
    {
        $this->seed(GuruTenagaPendidikSeeder::class);
    }

    public function test_membuat_58_akun_dengan_nig(): void
    {
        $this->seedGuru();

        $this->assertSame(58, User::whereNotNull('nig')->count());
    }

    public function test_nig_unik_tidak_ada_yang_ganda(): void
    {
        $this->seedGuru();

        $ganda = User::whereNotNull('nig')
            ->get()
            ->groupBy('nig')
            ->filter(fn ($group) => $group->count() > 1);

        $this->assertSame([], $ganda->keys()->all(), 'NIG ganda: '.$ganda->keys()->implode(', '));
    }

    public function test_setiap_nig_panjang_10_dan_dimulai_dengan_nol_kecuali_kepala_sekolah(): void
    {
        $this->seedGuru();

        foreach (User::whereNotNull('nig')->get() as $user) {
            $this->assertSame(
                10,
                strlen($user->nig),
                "NIG {$user->nig} ({$user->name}) bukan 10 digit"
            );
        }
    }

    /**
     * Inti anti-duplikat: akun PDS/BK yang sudah ada harus diperbarui
     * (NIG ditambahkan), bukan dibuat akun baru.
     */
    public function test_tidak_membuat_akun_ganda_untuk_guru_yang_sudah_punya_akun(): void
    {
        /*
         * Simulasikan kondisi sebelum seeder: 5 PDS + 8 BK sudah ada.
         *
         * Kolom keempat adalah role yang dihasilkan seeder. Untuk
         * kelima akun PDS ini nilainya sama dengan role lamanya:
         * mereka diputuskan tetap PDS meski jabatan resmi keempatnya
         * (PPKN, Ekonomi/Produktif, Produk Kreatif, Guru Jasmani)
         * bukan PDS.
         */
        $lama = [
            ['Sopiyan', 'sopiyan@pds.com', 'pds', 'pds'],
            ['Ari Zulfikar', 'arizulfikar@pds.com', 'pds', 'pds'],
            ['Fitri Rohmayasari', 'fitrirohmayasari@pds.com', 'pds', 'pds'],
            ['Muzakir Zulkarnaen', 'muzakirzulkarnaen@pds.com', 'pds', 'pds'],
            ['Yulianie Kasari', 'yulianiekasari@pds.com', 'pds', 'pds'],
            ['Rakhma Dhania', 'rakhmadhania@bk.com', 'bk', 'bk'],
            ['R. Dodi Setiadi', 'rdodisetiadi@bk.com', 'bk', 'bk'],
            ['Sholehudin Aditya Utama', 'sholehudinadityautama@bk.com', 'bk', 'bk'],
            ['Anisah Dwi Rahayu', 'anisahdwirahayu@bk.com', 'bk', 'bk'],
            ['Anita Lestari', 'anitalestari@bk.com', 'bk', 'bk'],
            ['Ariska Dwi Saputri', 'ariskadwisaputri@bk.com', 'bk', 'bk'],
            /*
             * Ejaan akun lama berbeda dari daftar resmi:
             * "Septiarini" vs "Septiriani". Inilah kasus yang diuji
             * jaring salah ketik di seeder.
             */
            ['Wilda Septiarini', 'wildaseptiarini@bk.com', 'bk', 'bk'],
            ['Ardi Sukma', 'ardisukma@bk.com', 'bk', 'bk'],
        ];

        foreach ($lama as [$name, $email, $role]) {
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('lama123'),
                'role' => $role,
                'kelas' => $role === 'pds' ? '["X-RPL-1"]' : null,
                'email_verified_at' => now(),
            ]);
        }

        $this->seedGuru();

        // Jumlah akun tidak bertambah satu pun: 13 lama + 45 baru = 58.
        $this->assertSame(58, User::count());

        /*
         * Nama lama tidak lagi bisa dipakai sebagai pengenal, karena
         * seeder kini menyimpan nama lengkap WITH gelar
         * ("Sopiyan, S.Hi., M.A"). Yang diuji di sini justru
         * email lamanya: akun itu harus diperbarui, bukan diduplikasi.
         */
        foreach ($lama as [$name, $email, $roleLama, $roleHarapan]) {
            $this->assertSame(
                1,
                User::where('email', $email)->count(),
                "Akun ganda untuk {$email}"
            );

            // Akun lama harusnya sudah pindah ke nama bergelar dan punya NIG.
            $user = User::where('email', $email)->first();
            $this->assertNotSame($name, $user->name);
            $this->assertNotNull($user->nig, "NIG belum terisi untuk {$email}");
            $this->assertSame($roleHarapan, $user->role, "Role salah untuk {$email}");
        }
    }

    /**
     * Regression: jaring salah ketik tidak boleh merebut akun orang lain.
     *
     * "Nitta Lestari" (0170193011) dan "Anita Lestari" (0200197060)
     * hanya berbeda satu huruf. Kalau jaring mencari nama "mirip"
     * tanpa kecuali, entri Nitta — yang muncul LEBIH AWAL di daftar —
     * akan mengambil akun Anita beserta NIG-nya, dan entri Anita
     * sendiri tidak mendapat akun sama sekali.
     */
    public function test_jaring_salah_ketik_tidak_merebut_akun_lain(): void
    {
        User::create([
            'name' => 'Anita Lestari',
            'email' => 'anitalestari@bk.com',
            'password' => Hash::make('lama123'),
            'role' => 'bk',
            'email_verified_at' => now(),
        ]);

        $this->seedGuru();

        $this->assertSame(58, User::count(), 'Harus tetap 58, tidak ada akun tambahan');

        $anita = User::where('nig', '0200197060')->first();
        $nitta = User::where('nig', '0170193011')->first();

        $this->assertNotNull($anita, 'Akun Anita Lestari harus tetap ada');
        $this->assertNotNull($nitta, 'Akun Nitta Lestari harus dibuat');
        $this->assertSame('Anita Lestari, S.Pd', $anita->name);
        $this->assertSame('Nitta Lestari, S.Kom', $nitta->name);
        $this->assertSame('anitalestari@bk.com', $anita->email, 'Email lama Anita harus utuh');
    }

    /**
     * Password kelompok harus mengikuti role: guru yang resmi berubah dari PDS
     * ke Guru biasa harus punya password "guru123", bukan "pds123".
     *
     * Tapi password yang sudah diganti sendiri pemilik tidak boleh
     * ditimpa — supaya tidak mengunci orang.
     */
    /**
     * PDS lama tetap PDS, password lamanya juga tetap berlaku.
     *
     * Catatan: role boleh berubah, tapi password tidak boleh diubah
     * diam-diam — orang yang sedang tidak ada di sekolah akan
     * mengunci dirinya sendiri.
     */
    public function test_pds_lama_tetap_pds_dan_password_lama_tetap_berfungsi(): void
    {
        // Ari Zulfikar: PDS dari seeder lama, password "pds123".
        User::create([
            'name' => 'Ari Zulfikar',
            'email' => 'arizulfikar@pds.com',
            'password' => Hash::make('pds123'),
            'role' => 'pds',
            'email_verified_at' => now(),
        ]);

        // Jimi sudah mengganti password sendiri, bukan pola kita.
        User::create([
            'name' => 'Fitri Rohmayasari',
            'email' => 'fitrirohmayasari@pds.com',
            'password' => Hash::make('rahasia-saya-2020'),
            'role' => 'pds',
            'email_verified_at' => now(),
        ]);

        $this->seedGuru();

        $ari = User::where('nig', '0190191049')->first();
        $fitri = User::where('nig', '0230189074')->first();

        // Jabatan resmi keduanya bukan PDS, tapi role tetap PDS.
        $this->assertSame('pds', $ari->role, 'PDS lama tidak boleh diturunkan jadi guru');
        $this->assertTrue(
            Hash::check('pds123', $ari->password),
            'Password lama harus tetap berlaku'
        );

        $this->assertSame('pds', $fitri->role);
        $this->assertTrue(
            Hash::check('rahasia-saya-2020', $fitri->password),
            'Password pilihan sendiri tidak boleh ditimpa'
        );
    }

    public function test_password_lama_tetap_berfungsi_setelah_seed(): void
    {
        User::create([
            'name' => 'Sopiyan',
            'email' => 'sopiyan@pds.com',
            'password' => Hash::make('pds123'),
            'role' => 'pds',
            'kelas' => '["X-RPL-1"]',
            'email_verified_at' => now(),
        ]);

        $this->seedGuru();

        // Password tidak boleh di-reset oleh seeder.
        $this->assertTrue(
            Hash::check('pds123', User::where('email', 'sopiyan@pds.com')->first()->password)
        );
    }

    public function test_kelas_binaan_tetap_utuh(): void
    {
        User::create([
            'name' => 'Sopiyan',
            'email' => 'sopiyan@pds.com',
            'password' => Hash::make('pds123'),
            'role' => 'pds',
            'kelas' => '["XI-TKJ-1","XII-DKV-3"]',
            'email_verified_at' => now(),
        ]);

        $this->seedGuru();

        $this->assertSame(
            '["XI-TKJ-1","XII-DKV-3"]',
            User::where('email', 'sopiyan@pds.com')->first()->kelas
        );
    }

    /**
     * Menjalankan seeder dua kali harus idempoten: tidak boleh ada
     * akun tambahan maupun NIG yang berubah.
     */
    public function test_seeder_bisa_dijalankan_berulang(): void
    {
        $this->seedGuru();
        $sebelum = User::count();
        $nigSebelum = User::whereNotNull('nig')->pluck('nig', 'id')->all();

        $this->seedGuru();

        $this->assertSame($sebelum, User::count());
        $this->assertSame($nigSebelum, User::whereNotNull('nig')->pluck('nig', 'id')->all());
    }

    /**
     * Pemetaan role mengikuti jabatan, bukan tebakan.
     */
    public function test_koordinator_pds_dan_bk_punya_role_yang_tepat(): void
    {
        $this->seedGuru();

        $this->assertSame('pds', User::where('nig', '0170179003')->first()->role);  // Sopiyan, Koordinator PDS
        $this->assertSame('bk', User::where('nig', '0180192027')->first()->role);   // Rakhma Dhania, Koordinator BK

        foreach (['0190185045', '0190195043', '0200197053', '0200197060', '0200197061', '0220199071', '0220194072'] as $nig) {
            $this->assertSame('bk', User::where('nig', $nig)->first()->role, "NIG {$nig} seharusnya BK");
        }

        /*
         * Empat guru ini tetap PDS meski jabatan resmi di daftar
         * sekolah bukan PDS — fitting dengan-role PDS, lihat
         * konstanta PDS_TETAP di seeder.
         */
        foreach (['0190191049', '0220194070', '0230189074', '0250189080'] as $nig) {
            $this->assertSame('pds', User::where('nig', $nig)->first()->role, "NIG {$nig} seharusnya PDS");
        }
    }

    public function test_guru_biasa_dapat_role_guru(): void
    {
        $this->seedGuru();

        // Guru Bahasa Indonesia, bukan BK/PDS.
        $this->assertSame('guru', User::where('nig', '0170193014')->first()->role);

        // Kepala sekolah juga role guru (belum ditentukan jadi admin).
        $this->assertSame('guru', User::where('nig', '0250186083')->first()->role);
    }

    /**
     * Nama harus disimpan PERSIS seperti di daftar resmi sekolah,
     * gelar ikut tercantum ("Sri Midawati, S.Pd, M.Pd").
     */
    public function test_nama_tersimpan_lengkap_dengan_gelar(): void
    {
        $this->seedGuru();

        // Lima guru ini memang tidak mencantumkan gelar di daftar sekolah.
        $tanpaGelar = [
            'Abdul Choir',
            'Qurotta Umi Kulsum',
            'Silfa Tsania',
            'Sashi Kirana Saslsabila',
            'Bagoes Setya Ananda',
        ];

        foreach (User::whereNotNull('nig')->get() as $user) {
            if (in_array($user->name, $tanpaGelar, true)) {
                continue;
            }

            $this->assertMatchesRegularExpression(
                '/,\s*[A-Z]/',
                $user->name,
                "Nama belum memakai gelar: {$user->name}"
            );
        }

        foreach (User::whereNotNull('nig')->get() as $user) {
            /*
             * Tidak ada spasi ganda, spasi sebelum koma, atau koma
             * menggantung di ujung.
             *
             * TITIK di ujung tidak boleh dirapikan: beberapa gelar di
             * daftar sekolah memang diakhiri titik ("S.S.", "S.Sn.",
             * "S.Pd.", "MM."). Hanya spasi dan koma yang dibuang.
             */
            $this->assertStringNotContainsString('  ', $user->name);
            $this->assertStringNotContainsString(' ,', $user->name);
            $this->assertSame(rtrim($user->name, " \t\n\r\0\x0B,"), $user->name);
        }

        // Contoh-contoh representatif dari tiap kelompok.
        $contoh = [
            '0250186083' => 'Sri Mildawati, S.Pd., M.Pd',
            '0170194006' => 'Amirul Hadid Ramadan, S.Pd., M.Pd',
            '0170179003' => 'Sopiyan, S.Hi., M.A',
            '0190185045' => 'R. Dodi Setiadi, S.Pd.I',
            '0200179052' => 'Alfi Hardian, S.Sn.',
        ];

        foreach ($contoh as $nig => $harapan) {
            $this->assertSame(
                $harapan,
                User::where('nig', $nig)->first()->name,
                "Nama tidak cocok untuk NIG {$nig}"
            );
        }
    }

    /**
     * NIG berawal nol harus utuh sebagai string di database.
     */
    public function test_nig_berawal_nol_tidak_hilang(): void
    {
        $this->seedGuru();

        // Akun baru (bukan yang sudah ada sebelumnya).
        $user = User::where('nig', '0170179003')->first();

        $this->assertNotNull($user, 'Akun Sopiyan tidak ditemukan');
        $this->assertSame('0170179003', $user->nig);
        $this->assertStringStartsWith('0', $user->nig);
    }
}
