<?php

namespace Tests\Feature;

use App\Models\Pelanggaran;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PelanggaranViewTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Regression guard: CSS variable yang dipakai halaman Data
     * Pelanggaran HARUS terdefinisi. Sebelumnya
     * --color-primary-gray & --color-secondary-red hanya ada di
     * auth/login.blade.php, sehingga thead & badge poin jadi
     * putih-di-atas-putih (tidak terlihat) di halaman ini.
     */
    public function test_semua_css_var_yang_dipakai_terdefinisi(): void
    {
        $pds = User::create(['name' => 'PDS', 'email' => 'p@t.com', 'password' => bcrypt('x'), 'role' => 'pds']);
        $w = User::create(['name' => 'Walas', 'email' => 'w@t.com', 'password' => bcrypt('x'), 'role' => 'walas', 'kelas' => 'X-RPL-1']);
        $s = Siswa::create(['nama' => 'Budi', 'nisn' => '1', 'kelas' => 'X-RPL-1']);
        Pelanggaran::create(['siswa_id' => $s->id, 'pelapor_id' => $w->id, 'tanggal' => today(),
            'jenis_pelanggaran' => 'Telat', 'kategori' => 'Ringan', 'poin' => 5, 'poin_sebelum' => 100,
            'poin_sesudah' => 95, 'keterangan' => 'Keterangan panjang sekali. ', 'status' => 'diverifikasi']);

        $html = $this->actingAs($pds)->get(route('pelanggaran.index'))->getContent();

        // Kumpulkan definisi CSS var dari seluruh <style> di halaman
        preg_match_all('/(--[a-z0-9-]+)\s*:\s*[^;}]+/i', $html, $defs);
        $defined = array_unique($defs[1]);

        // Kumpulkan pemakaian
        preg_match_all('/var\((--[a-z0-9-]+)/i', $html, $uses);
        $used = array_unique($uses[1]);

        $undef = array_values(array_diff($used, $defined));

        $this->assertEmpty($undef, 'CSS var tak terdefinisi: '.implode(', ', $undef));
    }
}
