<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Topbar sengaja direduksi jadi dua hal: penunjuk lokasi
 * (navigasi) dan tanggal. Pencarian siswa, aksi cepat, dan lonceng
 * notifikasi tidak boleh muncul lagi di sana.
 *
 * Count=jumlah laporan pending dipakai sidebar, jadi tetap harus
 * ada di layout meski tidak lagi dipakai topbar.
 */
class TopbarMinimalTest extends TestCase
{
    use RefreshDatabase;

    private function html(): string
    {
        $admin = User::create([
            'name' => 'Admin Uji',
            'email' => 'topbar@t.test',
            'password' => bcrypt('secret1234'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();

        return (string) $response->getContent();
    }

    /** Ambil isi <header class="topbar"> saja. */
    private function topbar(string $html): string
    {
        $start = strpos($html, '<header class="topbar">');

        $this->assertNotFalse($start, 'Topbar tidak ditemukan');

        $end = strpos($html, '</header>', $start);

        return substr($html, $start, $end - $start);
    }

    public function test_topbar_memuat_navigasi_dan_tanggal(): void
    {
        $topbar = $this->topbar($this->html());

        // Navigasi: penunjuk lokasi Portal > Halaman.
        $this->assertStringContainsString('topbar-crumb', $topbar);
        $this->assertStringContainsString('Administrator', $topbar);
        $this->assertStringContainsString('Dashboard', $topbar);

        // Tanggal hari ini.
        $this->assertStringContainsString('topbar-date', $topbar);
        $this->assertStringContainsString(now()->translatedFormat('d F Y'), $topbar);
    }

    public function test_topbar_tidak_lagi_memuat_pencarian_aksi_cepat_dan_lonceng(): void
    {
        $topbar = $this->topbar($this->html());

        $hilang = [
            'topbar-search' => 'kotak pencarian siswa',
            'globalSearch' => 'input pencarian siswa',
            'searchResults' => 'daftar hasil pencarian',
            'searchToggle' => 'tombol pembuka pencarian',
            'topbar-action' => 'tombol aksi cepat',
            'notifBtn' => 'lonceng notifikasi',
            'notifPanel' => 'panel notifikasi',
            'notifBadge' => 'lencana notifikasi',
        ];

        foreach ($hilang as $needle => $keterangan) {
            $this->assertStringNotContainsString(
                $needle,
                $topbar,
                "Topbar masih memuat {$keterangan} ({$needle})"
            );
        }
    }

    public function test_script_layout_tidak_lagi_mengatur_pencarian_atau_notifikasi(): void
    {
        $html = $this->html();

        // Script layout tinggalsidebar mobile. Hook yang dihapus harus
        // ikut hilang supaya tidak mencari elemen yang sudah tidak ada.
        foreach (['notifBtn', 'notifPanel', 'globalSearch', 'searchToggle', 'veritas_notif_seen_'] as $hook) {
            $this->assertStringNotContainsString($hook, $html, "Script masih merujuk {$hook}");
        }

        // Fokus keyboard tombol menu harus tetap ada.
        $this->assertStringContainsString('mobileMenuBtn', $html);
    }

    public function test_lencana_pending_di_sidebar_tetap_ada(): void
    {
        $html = $this->html();

        // Notifikasi di topbar hilang, tapi jumlah laporan yang menunggu
        // verifikasi tetap harus terlihat lewat lencana di sidebar.
        $this->assertStringContainsString('menu-badge', $html);
        $this->assertStringContainsString('Pending Verifikasi', $html);
    }
}
