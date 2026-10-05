<?php

namespace Tests\Feature;

use App\Models\AturanPelanggaran;
use App\Models\Pelanggaran;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Blok "Saldo Poin Tercatat" di halaman Rincian Laporan pernah
 * ditulis satu baris, sehingga Blade gagal mengompilasi
 * direktifnya dan kode mentah tampil ke pengguna:
 * "if($isPending) Belum dihitung (menunggu verifikasi)@else ...".
 *
 * Test ini mengunci keempat cabangnya sekaligus memastikan tidak
 * ada direktif Blade yang bocor ke HTML.
 */
class SaldoPoinTercatatTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Siswa $siswa;

    private AturanPelanggaran $aturan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Uji',
            'email' => 'saldo-poin@t.test',
            'password' => bcrypt('secret1234'),
            'role' => 'admin',
        ]);

        $this->siswa = Siswa::create([
            'nama' => 'Budi Santoso',
            'nisn' => '990001',
            'kelas' => 'X RPL 1',
        ]);

        $this->aturan = AturanPelanggaran::create([
            'kode' => 'R1',
            'kategori' => 'Ringan',
            'nama' => 'Terlambat masuk kelas',
            'poin' => 5,
            'aktif' => true,
        ]);
    }

    private function laporan(array $atribut = []): Pelanggaran
    {
        return Pelanggaran::create(array_merge([
            'pelapor_id' => $this->admin->id,
            'aturan_pelanggaran_id' => $this->aturan->id,
            'tanggal' => now(),
            'jenis_pelanggaran' => 'Terlambat masuk kelas',
            'kategori' => 'Ringan',
            'poin' => 5,
        ], $atribut));
    }

    private function html(Pelanggaran $p): string
    {
        $response = $this->actingAs($this->admin)
            ->get(route('pelanggaran.show', $p->id));

        $response->assertOk();

        return (string) $response->getContent();
    }

    public function test_saldo_tercatat_ditampilkan_jika_sudah_dihitung(): void
    {
        $html = $this->html($this->laporan([
            'siswa_id' => $this->siswa->id,
            'poin_sebelum' => 100,
            'poin_sesudah' => 95,
            'status' => 'diverifikasi',
        ]));

        $this->assertStringContainsString('100 → 95 Poin', $html);
    }

    public function test_laporan_pending_dikatakan_menunggu_verifikasi(): void
    {
        $html = $this->html($this->laporan([
            'siswa_id' => $this->siswa->id,
            'poin_sebelum' => null,
            'poin_sesudah' => null,
            'status' => 'pending',
        ]));

        $this->assertStringContainsString('Belum dihitung (menunggu verifikasi)', $html);
        $this->assertStringNotContainsString('Tidak dihitung (tanpa siswa)', $html);
    }

    public function test_laporan_umum_tanpa_siswa_dikatakan_tanpa_siswa(): void
    {
        $html = $this->html($this->laporan([
            'siswa_id' => null,
            'poin_sebelum' => null,
            'poin_sesudah' => null,
            'status' => 'diverifikasi',
        ]));

        $this->assertStringContainsString('Tidak dihitung (tanpa siswa)', $html);
    }

    public function test_laporan_terverifikasi_tanpa_catatan_saldo_dikatakan_tidak_tercatat(): void
    {
        // Kasus ini yang bocor dulu: saldo null, sudah diverifikasi,
        // tapi bedakan dari laporan umum tanpa siswa.
        $html = $this->html($this->laporan([
            'siswa_id' => $this->siswa->id,
            'poin_sebelum' => null,
            'poin_sesudah' => null,
            'status' => 'diverifikasi',
        ]));

        $this->assertStringContainsString('Tidak tercatat', $html);
    }

    public function test_tidak_ada_direktif_blade_yang_bocor_ke_html(): void
    {
        $html = $this->html($this->laporan([
            'siswa_id' => null,
            'poin_sebelum' => null,
            'poin_sesudah' => null,
            'status' => 'pending',
        ]));

        // Pola "Poin@elseif" membuat Blade memperlakukannya sebagai
        // teks biasa, jadi direktifnya ikut tercetak.
        foreach (['@if(', '@elseif', '@else', '@endif', '@foreach', '@php'] as $direktif) {
            $this->assertStringNotContainsString($direktif, $html, "Direktif {$direktif} bocor ke HTML");
        }

        $this->assertStringNotContainsString('if($isPending)', $html);
    }
}
