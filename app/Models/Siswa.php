<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Siswa extends Model
{
    /**
     * Tingkat kelas, urut dari junior.
     */
    public const TINGKAT = ['X', 'XI', 'XII'];

    /**
     * Jurusan, urut sesuai urutan yang dipakai sekolah.
     */
    public const JURUSAN = ['RPL', 'TKJ', 'DKV', 'LPB', 'TOI'];

    protected $fillable = [
        'nisn',
        'nama',
        'kelas',
    ];

    /**
     * Urutkan hasil query: tingkat dulu (X ke XI ke XII), lalu
     * jurusan (RPL, TKJ, DKV, LPB, TOI), lalu nomor rombel, lalu
     * nama.
     *
     * Disimpan di model, bukan di controller, karena daftar siswa
     * muncul di banyak halaman: Data Siswa, form Lapor, direktori
     * kelas, dan pencarian. Mengurutkan dari satu tempat supaya
     * urutannya tidak berbeda antar halaman.
     */
    public function scopeUrutKelas($query)
    {
        $kolom = $query->getGrammar()->wrap($query->qualifyColumn('kelas'));

        $bindings = [];

        /*
         * Tingkat dibaca dari awal teks kelas, jadi "XII-RPL" dan
         * "XII RPL 1" sama-sama dikenali sebagai XII. Urutan
         * pemeriksaan dari yang terpanjang (XII) ke terpendek (X),
         * kalau tidak "XII" akan tertangkap sebagai X.
         */
        $tingkat = 'CASE';

        foreach (array_reverse(self::TINGKAT, true) as $index => $t) {
            $tingkat .= ' WHEN SUBSTR(LOWER('.$kolom.'), 1, '.strlen($t).') = ? THEN '.$index;

            $bindings[] = mb_strtolower($t);
        }

        $tingkat .= ' ELSE 9 END';

        /*
         * Jurusan dicari dengan LIKE karena posisinya bisa bervariasi
         * dalam penulisan kelas ("X-RPL", "X RPL 1", "RPL X"),
         * dan kelas lama kadang tidak memakai tanda hubung.
         */
        $jurusan = 'CASE';

        foreach (self::JURUSAN as $index => $j) {
            $jurusan .= ' WHEN LOWER('.$kolom.') LIKE ? THEN '.$index;

            $bindings[] = '%'.mb_strtolower($j).'%';
        }

        $jurusan .= ' ELSE 9 END';

        /*
         * Kunci urut = tingkat * 10 + jurusan, jadi urutannya
         * otomatis mengikuti TINGKAT lalu JURUSAN tanpa perlu
         * menulis ulang urutan turunannya.
         *
         * Tingkat/jurusan yang tidak dikenali bernilai 9. Kelas
         * asing karena itu dapat kunci 99, selalu di belakang
         * kelas resmi (maksimum 22 untuk XII + TOI).
         */
        $raw = '('.$tingkat.' * 10 + '.$jurusan.')';

        return $query
            ->orderByRaw($raw, $bindings)
            ->orderBy('kelas')
            ->orderBy('nama');
    }

    /**
     * Urutkan daftar nama kelas (mis. hasil distinct kolom kelas)
     * dengan urutan yang sama seperti scopeUrutKelas.
     *
     * Dipakai untuk daftar pilihan kelas di halaman Lapor dan Data
     * Siswa, karena DISTINCT + ORDER BY hanya bisa mengurutkan
     * berdasarkan kolomnya sendiri, bukan urutan kustom.
     *
     * @param  iterable<int, string|null>  $kelas
     * @return Collection<int, string>
     */
    public static function urutKelasList(iterable $kelas): Collection
    {
        /*
         * Dedupe dulu berdasarkan kunci ternormalisasi, bukan
         * unique() biasa: "X-RPL" dan "x-rpl" adalah kelas yang
         * sama, dan penulisan pertama yang dipertahankan.
         */
        $kelasUnik = [];

        foreach ($kelas as $k) {
            if (! filled($k)) {
                continue;
            }

            // ??= supaya penulisan pertama menang, tidak ditimpa.
            $kelasUnik[mb_strtolower(trim((string) $k))] ??= (string) $k;
        }

        $urut = array_keys($kelasUnik);

        usort($urut, function ($a, $b) use ($kelasUnik) {
            $sa = self::kunciUrut($a);
            $sb = self::kunciUrut($b);

            return $sa === $sb
                ? strcmp($kelasUnik[$a], $kelasUnik[$b])
                : $sa <=> $sb;
        });

        return collect(array_map(fn ($kunci) => $kelasUnik[$kunci], $urut));
    }

    /**
     * Kunci urut untuk satu nilai kelas: tingkat * 10 + jurusan.
     *
     * Tingkat dan jurusan yang tidak dikenali bernilai 9, sehingga
     * kelas asing selalu urut lebih rendah dari kelas resmi.
     */
    private static function kunciUrut(string $kelas): int
    {
        $huruf = mb_strtolower(trim($kelas));

        $tingkat = 9;

        foreach (array_reverse(self::TINGKAT, true) as $index => $t) {
            if (str_starts_with($huruf, mb_strtolower($t))) {
                $tingkat = $index;
                break;
            }
        }

        $jurusan = 9;

        foreach (self::JURUSAN as $index => $j) {
            if (str_contains($huruf, mb_strtolower($j))) {
                $jurusan = $index;
                break;
            }
        }

        return $tingkat * 10 + $jurusan;
    }

    public function pelanggarans()
    {
        return $this->hasMany(Pelanggaran::class);
    }

    public function prestasis()
    {
        return $this->hasMany(Prestasi::class);
    }
}
