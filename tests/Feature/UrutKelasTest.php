<?php

namespace Tests\Feature;

use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Urutan kelas di Data Siswa dan semua dropdown siswa harus
 * mengikuti jenjang X → XI → XII, lalu jurusan RPL → TKJ → DKV →
 * LPB → TOI.
 *
 * Test ini sengaja memakai data berformat "X DKV 1" (ada nomor
 * rombel) karena itulah format yang tersimpan di database, bukan
 * kode kelas polos seperti "X-DKV".
 */
class UrutKelasTest extends TestCase
{
    use RefreshDatabase;

    private function seedSiswa(string ...$kelas): void
    {
        foreach ($kelas as $i => $k) {
            Siswa::create([
                'nama' => 'Siswa '.$i,
                'nisn' => (string) (90000 + $i),
                'kelas' => $k,
            ]);
        }
    }

    public function test_scope_mengurutkan_kelas_resmi(): void
    {
        // Sengaja diacak, semua memakai format dengan nomor rombel.
        $this->seedSiswa(
            'XII DKV 1', 'X RPL 1', 'XI TOI 2', 'X DKV 1', 'XII RPL 1',
            'X TKJ 1', 'XI RPL 1', 'XII LPB 1', 'X LPB 1', 'XI DKV 1',
            'X TOI 1', 'XII TKJ 1', 'XI TKJ 1', 'XI LPB 1',
        );

        $this->assertSame([
            'X RPL 1', 'X TKJ 1', 'X DKV 1', 'X LPB 1', 'X TOI 1',
            'XI RPL 1', 'XI TKJ 1', 'XI DKV 1', 'XI LPB 1', 'XI TOI 2',
            'XII RPL 1', 'XII TKJ 1', 'XII DKV 1', 'XII LPB 1',
        ], Siswa::urutKelas()->pluck('kelas')->all());
    }

    public function test_kelas_non_resmi_ditaruh_di_belakang(): void
    {
        $this->seedSiswa('Z-ABC', 'X RPL 1', 'A-XXX');

        $this->assertSame(
            ['X RPL 1', 'A-XXX', 'Z-ABC'],
            Siswa::urutKelas()->pluck('kelas')->all()
        );
    }

    public function test_format_kelas_tidak_konsisten_masih_terkena(): void
    {
        // Data hasil import bisa formatnya berbeda-beda: huruf
        // kecil, tanpa tanda hubung, dan dengan nomor rombel.
        $this->seedSiswa('x-rpl-2', 'X RPL 1', 'xi-tkj', 'XII LPB 1');

        $hasil = Siswa::urutKelas()->pluck('kelas')->all();

        // 'x-rpl-2' dan 'X RPL 1' tetap menempati posisi X-RPL,
        // yaitu sebelum XI-TKJ dan XII-LPB. Urutan di dalam satu
        // tingkat/jurusan tidak diuji di sini karena itu murni
        // konsekuensi dari pengurutan kolom kelas.
        $this->assertContains('x-rpl-2', $hasil);

        $posXtkj = array_search('xi-tkj', $hasil, true);
        $posXii = array_search('XII LPB 1', $hasil, true);

        $this->assertLessThan($posXtkj, array_search('X RPL 1', $hasil, true));
        $this->assertLessThan($posXii, array_search('x-rpl-2', $hasil, true));
    }

    public function test_urut_kelas_list_mengikuti_urutan_resmi(): void
    {
        $hasil = Siswa::urutKelasList([
            'XII DKV 1', 'X RPL 1', 'Z-ABC', 'X DKV 1', 'XII RPL 1',
        ]);

        $this->assertSame(
            ['X RPL 1', 'X DKV 1', 'XII RPL 1', 'XII DKV 1', 'Z-ABC'],
            $hasil->all()
        );
    }

    public function test_urut_kelas_list_membuang_kosong_dan_duplikat(): void
    {
        $hasil = Siswa::urutKelasList(['X-RPL', '', null, 'x-rpl', 'X DKV 1']);

        $this->assertSame(['X-RPL', 'X DKV 1'], $hasil->all());
    }

    public function test_urut_kelas_list_menampilkan_semua_kelas_resmi(): void
    {
        $hasil = Siswa::urutKelasList([
            'XII LPB 1', 'X TOI 1', 'XI DKV 1', 'X TKJ 1',
            'XI TOI 1', 'XII RPL 1', 'X RPL 1', 'XI RPL 1',
            'XII TKJ 1', 'XII DKV 1', 'X DKV 1', 'X LPB 1', 'XI TKJ 1', 'XI LPB 1',
        ]);

        $tingkat = ['X', 'XI', 'XII'];

        foreach ($tingkat as $t) {
            $jurusan = array_values(array_filter(
                Siswa::JURUSAN,
                fn ($j) => $hasil->contains(fn ($k) => str_starts_with($k, "$t $j"))
            ));

            // Hanya tingkat XII yang memang tidak punya TOI.
            $harapan = $t === 'XII'
                ? ['RPL', 'TKJ', 'DKV', 'LPB']
                : Siswa::JURUSAN;

            $this->assertSame($harapan, $jurusan, "Urutan jurusan tingkat $t salah");
        }
    }
}
