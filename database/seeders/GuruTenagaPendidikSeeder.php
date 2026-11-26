<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder akun tenaga pendidik berdasarkan daftar resmi sekolah.
 *
 * ===== CARA MEMBACA ROLE =====
 *
 * Peta NIG di bawah adalah satu-satunya sumber kebenaran role guru
 * BK, PDS, dan pimpinan. Daftar sekolah menandai jabatan secaratekstual,
 * dan pemetaan ke role aplikasi ditulis manual di sini:
 *
 *   - "Kepala Sekolah" / "Waka ..."             -> role pimpinan
 *   - "Koordinator PDS"                        -> role pds
 *   - "Guru Bimbingan Konseling/Koordinator BK" -> role bk
 *   - "Guru Bimbingan Konseling"               -> role bk
 *   - sisanya                                  -> role guru
 *
 * Sengaja TIDAK menebak jabatan: orang yang sudah punya akun
 * dengan role tertentu (mis. Muzakir Zulkarnaen tercatat PDS di
 * aplikasi walau jabatannya "Guru Ekonomi") tetap memakai role
 * yang sudah ada di aplikasi. Role di aplikasi adalah keputusan
 * sekolah, bukan turunan dari jabatan di daftar.
 *
 * ===== PENJAGAAN ANTI DUPLIKAT =====
 *
 * 13 guru sudah punya akun (5 PDS + 8 BK). Seeder ini mencari akun
 * berdasarkan NIG dulu, lalu berdasarkan nama — sehingga akun lama
 * diperbarui (NIG ditambahkan) alih-alih dibuat ganda.
 *
 * ========== PASSWORD ==========
 *
 * Tidak diubah sama sekali. Password setiap role mengikuti
 * konvensi yang sudah berlaku:
 *   admin zidan123 | bk bk123 | pds pds123 | guru guru123
 *   walas walas123 | pimpinan pimpinan123
 * Akun contoh lama (Guru Piket Umum / gurupiket@guru.com) sudah
 * dihapus — baik dari seeder maupun lewat migration — karena tidak
 * ada di daftar guru sekolah.
 */
class GuruTenagaPendidikSeeder extends Seeder
{
    /**
     * Password per role. Sengaja sama dengan yang sudah dipakai
     * seeder lain supaya tidak ada akun yang tiba-tiba tidak bisa
     * login setelah migration ini.
     */
    private const PASSWORD = [
        'admin' => 'zidan123',
        'pimpinan' => 'pimpinan123',
        'bk' => 'bk123',
        'pds' => 'pds123',
        'guru' => 'guru123',
        'walas' => 'walas123',
    ];

    /**
     * Unsur pimpinan sekolah: Kepala Sekolah + lima Wakil Kepala Sekolah.
     *
     * Dipakai satu password terpisah supaya mudah dibedakan dari guru
     * pada umumnya saat menerima laporan — akun pimpinan sering
     * dipakai untuk oversee, bukan untuk mencatat pelanggaran.
     *
     * Password ini TIDAK bisa diganti lewat aplikasi (belum ada fitur
     * ganti password), jadi siapa pun yang tahu "pimpinan123" bisa
     * masuk sebagai pimpinan. Kalau sudah tidak berlaku, ganti
     * nilainya di sini.
     */
    private const PIMPINAN = [
        '0250186083', // Sri Mildawati — Kepala Sekolah
        '0170194006', // Amirul Hadid Ramadan — Waka Kurikulum
        '0180193040', // Septia Sundari — Waka Hubungan Masyarakat
        '0170188005', // Nugrah Dwi Saptaji — Waka Hubungan Industri
        '0190195044', // Hanifan Nurfauzi — Waka Kesiswaan
        '0170167004', // Rohajon — Waka Sarana Prasarana
    ];

    private const PASSWORD_PIMPINAN = 'pimpinan123';

    /**
     * Hak akses tambahan per NIG (lihat konstanta ACCESS_* di User).
     *
     *   full               -> semua fitur, kecuali Manajemen Akun
     *   pelanggaran_writer -> boleh menambah pelanggaran langsung
     *   verifikator        -> kelola pelanggaran penuh seperti PDS
     *
     * Hanya ditulis untuk NIG yang terdaftar di sini. Akun lain
     * dibiarkan apa adanya supaya perubahan yang dilakukan Super
     * Admin lewat Manajemen Akun tidak terhapus saat seeder dijalankan
     * ulang.
     */
    private const AKSES_KHUSUS = [
        '0190195044' => 'full',                 // Hanifan Nurfauzi — Waka Kesiswaan
        '0180193040' => 'verifikator',          // Septia Sundari — Waka Hubungan Masyarakat
    ];

    /**
     * Kelas binaan untuk akun pimpinan yang menerima pembagian kelas
     * dari Guru PDS (disimpan sebagai JSON, sama seperti kelas binaan
     * PDS/BK).
     *
     *   Hanifan Nurfauzi -> X-RPL-1, XI-RPL-2, XI-TKJ-2
     *   Septia Sundari   -> XI-RPL-1, XII-TKJ-3, XII-DKV-1
     *
     * Sopiyan yang menerima X-TKJ-1 diatur di PdsUserSeeder. Yulianie
     * sendiri sudah tidak lagi ber-role PDS dan tidak memegang kelas.
     *
     * @var array<string, list<string>>
     */
    private const BINAAN_KELAS = [
        '0190195044' => ['X-RPL-1', 'XI-RPL-2', 'XI-TKJ-2'],
        '0180193040' => ['XI-RPL-1', 'XII-TKJ-3', 'XII-DKV-1'],
    ];

    /**
     * Maksimal selisih huruf yang masih dianggap "orang yang sama"
     * saat mencocokkan nama akun lama dengan daftar sekolah.
     */
    private const AMBANG_TYPO = 2;

    /**
     * Akun yang tetap PDS walau jabatannya di daftar sekolah bukan PDS.
     *
     * Ketiga akun ini sudah ada sebagai PDS sebelum daftar resmi
     * sekolah pernah masuk ke sistem, dan keputusannya adalah
     * BIARKAN — tidak diubah jadi guru biasa.
     *
     * Di daftar resmi, jabatan ketiga orang ini memang bukan PDS:
     * Ari Zulfikar (Guru Jasmani), Fitri Rohmayasari (Guru PPKN),
     * dan Muzakir Zulkarnaen (Guru Ekonomi/Produktif). Sopiyan tidak
     * masuk daftar ini karena jabatannya sudah "Koordinator PDS" dan
     * otomatis jadi PDS.
     *
     * Yulianie Kasari sengaja tidak lagi masuk daftar ini: ia
     * dikeluarkan dari Guru PDS dan kembali menjadi guru biasa.
     *
     * @var list<string>
     */
    private const PDS_TETAP = [
        '0190191049', // Ari Zulfikar
        '0230189074', // Fitri Rohmayasari
        '0220194070', // Muzakir Zulkarnaen
    ];

    /**
     * Daftar tenaga pendidik: NIG, nama, jabatan, role aplikasi.
     *
     * NIG ditulis sebagai string supaya nol di depan tidak hilang.
     *
     * @var list<array{nig: string, nama: string, jabatan: string, role: string}>
     */
    private const TENAGA_PENDIDIK = [
        ['0250186083', 'Sri Mildawati, S.Pd., M.Pd', 'Kepala Sekolah', 'guru'],
        ['0170194006', 'Amirul Hadid Ramadan, S.Pd., M.Pd', 'Waka Urs. Kurikulum', 'guru'],
        ['0180193040', 'Septia Sundari, S.Pd', 'Waka Urs. Hubungan Masyarakat & Guru Pendidikan Matematika', 'guru'],
        ['0170188005', 'Nugrah Dwi Saptaji, S.Psi', 'Waka Urs. Hubungan Industri', 'guru'],
        ['0190195044', 'Hanifan Nurfauzi, M. Ag', 'Waka Urs. Kesiswaan', 'guru'],
        ['0170167004', 'Rohajon, S.T', 'Waka Urs. Sarana Prasarana', 'guru'],
        ['0170179003', 'Sopiyan, S.Hi., M.A', 'Koordinator PDS', 'pds'],
        ['0170194016', 'Akbar Maulana, S.Pd', 'Guru Pendidikan Jasmani, Olahraga dan Kesehatan', 'guru'],
        ['0170193008', 'Dita Wulandari, S.TP', 'Guru Ilmu Pengetahuan Alam dan Sosial', 'guru'],
        ['0170190009', 'Muhammad Ghufran Arnan, S.Pd', 'Guru Pendidikan Sejarah', 'guru'],
        ['0170193011', 'Nitta Lestari, S.Kom', 'Guru Tik/Kepala Kompetensi Keahlian', 'guru'],
        ['0170193014', 'Vivin Dwi Yudianto, S.Pd', 'Guru Pendidikan Bahasa Indonesia', 'guru'],
        ['0180192034', 'Asit Rahmah, S.Pd', 'Guru Matematika', 'guru'],
        ['0180183021', 'Diana Rosita, S.Pd.I', 'Guru Pendidikan Agama Islam', 'guru'],
        ['0180192037', 'Farida Awalia, S.Pd', 'Guru Pendidikan Bahasa Inggris', 'guru'],
        ['0180195029', 'Ika Sartika, S.Pd', 'Guru Pendidikan Bahasa Inggris', 'guru'],
        ['0180186028', 'Karti Suminar, M.Pd', 'Guru Pendidikan Bahasa Inggris', 'guru'],
        ['0180177022', 'Mieke Rahmawaty Eliyaningrum, S.Kom', 'Guru TIK/Kepala kompetensi keahlian TKJ', 'guru'],
        ['0180194026', 'Putri Ngawirabaya, S.S', 'Guru Pendidikan Bahasa Inggris', 'guru'],
        ['0180192027', 'Rakhma Dhania, S.Pd', 'Guru Bimbingan Konseling/Koordinator BK', 'bk'],
        ['0180195023', 'Rizni Fauziah, S.Tr.Akun', 'Guru Ekonomi/Produktif Perbankan', 'guru'],
        ['0180179036', 'Septianto Raharso, S.Kom, M.Pd', 'Guru Teknologi Informasi dan Komunikasi', 'guru'],
        ['0180196039', 'Suci Romadini, S.Kom', 'Guru Teknologi Informasi dan Komunikasi', 'guru'],
        ['0190184041', 'Erfan Bayu Saputra, SE', 'Guru Ekonomi', 'guru'],
        ['0190185045', 'R. Dodi Setiadi, S.Pd.I', 'Guru Bimbingan Konseling', 'bk'],
        ['0190193042', 'Muhammad Nurcholis Majid, S.Kom', 'Guru TIK/Produktif', 'guru'],
        ['0190195043', 'Sholehudin Aditya Utama, S.Pd', 'Guru Bimbingan Konseling', 'bk'],
        ['0190197048', 'Alvi Luviani Rahayu, S.I.Kom', 'Guru Teknologi Informasi dan Komunikasi/DKV', 'guru'],
        ['0190191049', 'Ari Zulfikar, S.Pd', 'Guru Pendidikan Jasmani, Olahraga dan Kesehatan', 'guru'],
        ['0200179052', 'Alfi Hardian, S.Sn.', 'Guru Teknologi Informasi dan Komunikasi', 'guru'],
        ['0200197053', 'Anisah Dwi Rahayu, S.Pd', 'Guru Bimbingan Konseling', 'bk'],
        ['0200197060', 'Anita Lestari, S.Pd', 'Guru Bimbingan Konseling', 'bk'],
        ['0200197061', 'Ariska Dwi Saputri, S.Pd', 'Guru Bimbingan Konseling', 'bk'],
        ['0200197062', 'Dewi Ratnawati, S.Pd.', 'Guru Pendidikan Matematika', 'guru'],
        ['0200184064', 'Mustamah, S.Pd.I, MM.', 'Guru Pendidikan Agama Islam', 'guru'],
        ['0220199064', 'Yaumil Akbaria, S.Pd', 'Guru Pendidikan Agama Islam', 'guru'],
        ['0220188066', 'Siti Kholifah, S.Pd.I', 'Guru Pendidikan Agama Islam & Qiraatul Qur\'an', 'guru'],
        ['0220198067', 'Indah Ramadhanty, S.Pd', 'Guru Pendidikan Bahasa Indonesia', 'guru'],
        ['0220185069', 'Muhamad Adib, S.E', 'Guru Ekonomi', 'guru'],
        ['0220194070', 'Muzakir Zulkarnaen, S.Ak', 'Guru Ekonomi/Produktif Perbankan Akuntansi', 'guru'],
        ['0220199071', 'Wilda Septiriani, S.Pd', 'Guru Bimbingan Konseling', 'bk'],
        ['0220194072', 'Ardi Sukma, S.H', 'Guru Bimbingan Konseling', 'bk'],
        ['0220188073', 'Nurulia Falah, S.Sos., M.Pd', 'Guru Produk Kreatif dan Kewirausahaan', 'guru'],
        ['0230189074', 'Fitri Rohmayasari, S.Pd', 'Guru PPKN', 'guru'],
        ['0230189075', 'Nani Yuliawati Ningsih, S.Pd', 'Guru Pendidikan Bahasa Indonesia', 'guru'],
        ['0240198077', 'Ashifa Ramadanty Maharddhika, S.Pd', 'Guru PPKN', 'guru'],
        ['0250198079', 'Sulthan Alawy Shihab, S.Kom', 'Guru TIK/ Produktif RPL', 'guru'],
        ['0250189080', 'Yulianie Kasari, M.Pd', 'Guru Produk Kreatif dan Kewirausahaan', 'guru'],
        ['0250196081', 'Luthfi Abdul Rahman, S.Pd', 'Guru TIK/Produktif & TKJ', 'guru'],
        ['0250198082', 'Nurwanti Ayu Mashita, S.Mat', 'Guru Pendidikan Matematika', 'guru'],
        ['0250100086', 'Dimas Maulana Ishaq, S.Kom', 'Guru Teknologi Informasi dan Komunikasi', 'guru'],
        ['0250102088', 'Annisa Febiyanti, S.I.Kom', 'Guru Teknologi Informasi dan Komunikasi / Bahasa Sunda', 'guru'],
        ['0250195089', 'Qurotta Umi Kulsum', 'Guru Produk Kreatif dan Kewirausahaan', 'guru'],
        ['0250194090', 'Abdul Choir', 'Guru TIK/Produktif TKJ', 'guru'],
        ['0250103091', 'Silfa Tsania', 'Guru Seni Budaya', 'guru'],
        ['0260103092', 'Sashi Kirana Saslsabila', 'Guru RPL', 'guru'],
        ['0260100093', 'Syahidah Muthi\'ah, S.Pd', 'Guru TOI', 'guru'],
        ['0260198095', 'Bagoes Setya Ananda', 'Guru Bahasa Jepang', 'guru'],
    ];

    public function run(): void
    {
        $dipakaiEmail = [];

        /*
         * Akun yang sudah "dipesan" entri lain lewat kunci yang pasti
         * (NIG persis, email lama, atau nama persis) tidak boleh diambil
         * oleh jaring salah ketik.
         *
         * Tanpa ini, "Nitta Lestari" akan merebut akun "Anita Lestari"
         * (jarak 1 huruf) dan mencuri NIG-nya — bukan karena jaringnya
         * salah, tapi karena "Anita Lestari" belum diproses pada saat
         * "Nitta Lestari" dilewati.
         */
        $terpakai = $this->akunTerikat();

        foreach (self::TENAGA_PENDIDIK as [$nig, $nama, $jabatan, $role]) {
            $namaBersih = $this->namaTanpaJabatan($nama);
            /*
             * Akun PDS lama dikembalikan ke role PDS, apa pun
             * jabatannya di daftar resmi sekolah. Daftar NIG-nya
             * ada di konstanta PDS_TETAP.
             *
             * Sengaja di-override di sini, bukan ditulis ulang di
             * dalam TABEL TENAGA_PENDIDIK, supaya tabel itu tetap
             * salinan apa adanya dari daftar sekolah dan setiap
             * devsinya kelihatan jelas di satu tempat.
             */
            if (in_array($nig, self::PDS_TETAP, true)) {
                $role = 'pds';
            }

            /*
             * Kepala Sekolah dan para waka memakai role "pimpinan".
             * Ini penanda eksplisit, bukan lagi menumpang role "guru".
             */
            if (in_array($nig, self::PIMPINAN, true)) {
                $role = 'pimpinan';
            }

            /*
             * Cari akun yang sudah ada, urut dari yang paling yakin:
             1. NIG persis   -> akun pernah di-seed dari daftar ini
             * 2. email induk  -> akun lama dari seeder PDS/BK
             * 3. nama bersih  -> kunci cadangan bila email berubah
             * 4. nama mirip   -> jaring pengaman terakhir, lihat catatan di bawah
             *
             * Poin 2 dan 3 itulah yang mencegah akun ganda: 13 guru
             * BK/PDS yang sudah ada punya email buatan (mis.
             * sopiyan@pds.com) dan nama "Sopiyan", bukan NIG.
             *
             * Poin 4 ada karena daftar resmi sekolah dan data lama
             * tidak selalu eja sama: "Wilda Septiarini" (lama)
             * vs "Wilda Septiriani" (baru). Tanpa jaring ini, akun
             * lamanya jadi yatim dan muncul akun ganda.
             *
             * Perhatikan: yang DICOCOKKAN tetap nama tanpa gelar,
             * karena nama di database lama belum memakai gelar.
             * Yang disimpan justru nama LENGKAP (pakai gelar).
             */
            $user = User::where('nig', $nig)->first()
                ?? User::where('email', $this->emailLama($namaBersih, $role))->first()
                ?? User::where('name', $namaBersih)->first()
                ?? User::where('name', $nama)->first()
                ?? $this->cariNamaMirip($namaBersih, $terpakai);

            if ($user) {
                $terpakai[$user->id] = true;
            }

            $email = $user?->email ?? $this->emailBaru($nig, $dipakaiEmail);

            $atribut = [
                // Nama disimpan lengkap dengan gelar, sesuai daftar resmi
                // sekolah ("Sri Midawati, S.Pd, M.Pd").
                'name' => $nama,
                'email' => $email,
                'nig' => $nig,
                'role' => $role,
                // Jabatan resmi apa adanya dari daftar sekolah, dipakai
                // untuk label UI (mis. "Kepala Sekolah") supaya akun
                // mereka tidak terlihat sebagai "Guru Khusus".
                'jabatan' => $jabatan,
                'email_verified_at' => $user?->email_verified_at ?? now(),
            ];

            // Hak akses tambahan hanya untuk NIG yang terdaftar.
            if (isset(self::AKSES_KHUSUS[$nig])) {
                $atribut['access_level'] = self::AKSES_KHUSUS[$nig];
            }

            // Kelas binaan untuk akun pimpinan yang menerima pembagian
            // kelas dari Guru PDS.
            if (isset(self::BINAAN_KELAS[$nig])) {
                $atribut['kelas'] = json_encode(self::BINAAN_KELAS[$nig]);
            }

            if ($user) {
                /*
                 * Jangan sentuh password, dengan satu-satunya
                 * pengecualian: unsur pimpinan, yang memang memakai
                 * password terpisah.
                 *
                 * Role boleh berubah (mis. PDS lama tetap PDS), tapi
                 * password lama tetap berlaku — mengubahnya diam-diam
                 * bisa mengunci orang yang tidak sedang ada di sekolah.
                 * Kalau ada kebutuhan mengganti password, itu urusan
                 * seederPassword, bukan run() di sini.
                 *
                 * Kelas binaan PDS/BK tetap dipertahankan, kecuali dua
                 * akun pimpinan yang pembagian kelasnya ditetapkan di
                 * BINAAN_KELAS.
                 */
                if (in_array($nig, self::PIMPINAN, true)) {
                    $atribut['password'] = Hash::make(self::PASSWORD_PIMPINAN);
                }

                $user->update($atribut);

                continue;
            }

            $baru = User::create($atribut + [
                'password' => Hash::make(
                    in_array($nig, self::PIMPINAN, true)
                        ? self::PASSWORD_PIMPINAN
                        : self::PASSWORD[$role]
                ),
                'kelas' => null,
                'access_level' => self::AKSES_KHUSUS[$nig] ?? null,
            ]);

            /*
             * Akun yang baru dibuat baris ini juga harus dikunci,
             * kalau tidak entri berikutnya bisa merebutnya lewat jaring
             * salah ketik — misalnya "Anita Lestari" (0200197060)
             * diambil alih oleh entri "Nitta Lestari" (0170193011).
             */
            $terpakai[$baru->id] = true;
        }
    }

    /**
     * Kumpulkan id akun yang sudah pasti milik salah satu entri daftar
     * sekolah, lewat kunci yang tidak mungkin keliru: NIG, email lama,
     * atau nama persis.
     *
     * @return array<int, true>
     */
    private function akunTerikat(): array
    {
        $terpakai = [];

        foreach (User::query()->get() as $user) {
            foreach (self::TENAGA_PENDIDIK as [$nig, $nama, $jabatan, $role]) {
                $bersih = $this->namaTanpaJabatan($nama);

                if ($user->nig === $nig
                    || $user->email === $this->emailLama($bersih, $role)
                    || $user->name === $bersih
                    || $user->name === $nama) {
                    $terpakai[$user->id] = true;

                    break;
                }
            }
        }

        return $terpakai;
    }

    /**
     * Jaring pengaman terakhir: cari akun yang namanya "mendek"
     * dengan nama dari daftar resmi.
     *
     * Kenapa perlu: daftar resmi sekolah dan data lama tidak selalu
     * eja sama. Contoh nyatnya di data ini —
     *
     *   data lama  : "Wilda Septiarini"  (akun dari BkUserSeeder)
     *   daftar baru: "Wilda Septiriani, S.Pd"
     *
     * Keduanya orang yang sama, tapi nama bersih maupun slug email-nya
     * berbeda, jadi tiga kunci sebelumnya gagal dan akunnya tercipta
     * dua kali.
     *
     * Syaratnya sengaja ketat agar tidak merebut orang yang salah:
     * - jarak Levenshtein maksimal AMBANG (2 huruf),
     * - panjang nama minimal 6 huruf, supaya nama pendek seperti
     *   "Sopiyan" tidak tertukar dengan yang lain,
     * - hanya akun yang TIDAK sudah dipesan entri lain lewat kunci
     *   pasti (lihat akunTerikat()) — inilah yang mencegah
     *   "Nitta Lestari" merebut "Anita Lestari",
     * - kandidat dengan jarak terkecil harus satu, bukan beberapa.
     *
     * @param  array<int, true>  $terpakai  id_user yang sudah dikunci entri lain
     */
    private function cariNamaMirip(string $namaBersih, array $terpakai): ?User
    {
        if (mb_strlen($namaBersih) < 6) {
            return null;
        }

        $kandidat = [];

        foreach (User::query()->get() as $user) {
            if (isset($terpakai[$user->id])) {
                continue;
            }

            $lama = $this->namaTanpaJabatan($user->name);

            if (mb_strlen($lama) < 6 || abs(mb_strlen($lama) - mb_strlen($namaBersih)) > self::AMBANG_TYPO) {
                continue;
            }

            if (mb_strtolower($lama) === mb_strtolower($namaBersih)) {
                continue;
            }

            $jarak = levenshtein(mb_strtolower($lama), mb_strtolower($namaBersih));

            if ($jarak > 0 && $jarak <= self::AMBANG_TYPO) {
                $kandidat[$user->id] = $jarak;
            }
        }

        if ($kandidat === []) {
            return null;
        }

        $terdekat = min($kandidat);

        // Lebih dari satu kandidat(sejaraknya sama) -> jangan nebak.
        if (count(array_filter($kandidat, fn ($d) => $d === $terdekat)) > 1) {
            return null;
        }

        return User::find(array_search($terdekat, $kandidat, true));
    }

    /**
     * Buang gelar dari nama agar cocok dengan akun yang sudah ada.
     *
     * Daftar sekolah menulis "Sopiyan, S.Hi., M.A", sedangkan akun
     * yang sudah ada namanya "Sopiyan". Tanpa langkah ini setiap
     * guru akan tercipta dua kali.
     */
    private function namaTanpaJabatan(string $nama): string
    {
        // Buang tanda baca menggantung di ujung ("Afti Hardian, S.Sn.,").
        $nama = trim(preg_replace('/[\s,.]+$/u', '', $nama));

        // Normalisasi spasi ganda sebelum dipotong, supaya koma di
        // dalam gelar rapat ("S.Pd, M.Pd") tidak menyisakan spasi.
        $nama = preg_replace('/\s*,\s*/', ', ', $nama);

        /*
         * Ambil bagian sebelum koma pertama.
         *
         * Di daftar sekolah, koma pada kolom Nama SELALU memisahkan
         * nama dengan gelar — dicek seluruh 58 baris dan tidak ada
         * satu pun koma yang berdiri sendiri di dalam nama. Koma
         * dalam nama (bila nanti muncul) ditulis sebagai inisial
         * bergaya "R. Dodi Setiadi", bukan koma.
         *
         * Dipotong di koma pertama, bukan dengan regex daftar gelar,
         * karena singkatan di sekolah sudah punya tiga varian untuk
         * huruf S saja (S.Pd, S.Sn, S.Kom, S.ST) dan akan terus
         * bertambah — regexnya cepat basi, pemotongan ini tidak.
         */
        return trim(explode(',', $nama, 2)[0]);
    }

    /**
     * Email akun lama dari seeder PDS/BK, mis. "Sopiyan" -> "sopiyan@pds.com".
     */
    private function emailLama(string $nama, string $role): string
    {
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $nama));

        return $slug.'@'.$role.'.com';
    }

    /**
     * Email untuk akun baru. Memakai NIG sebagai basis supaya pasti
     * unik antar guru yang namanya mirip, dan tidak pernah bentrok
     * dengan akun lama yang beremail "namasekolah@guru.com".
     */
    private function emailBaru(string $nig, array &$dipakai): string
    {
        $email = 'nig'.$nig.'@guru.com';

        // Jaring pengaman: kalau NIG ternyata ganda di daftar, tambahkan sufiks.
        $suffix = 2;

        while (in_array($email, $dipakai, true)) {
            $email = 'nig'.$nig.'-'.$suffix.'@guru.com';
            $suffix++;
        }

        $dipakai[] = $email;

        return $email;
    }
}
