<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Login memakai NIG, bukan email.
 *
 * NIG wajib diperlakukan sebagai STRING: sebagian besar diawali nol
 * ("0170194016"). Kalau storage atau query mengubahnya jadi angka,
 * nolnya hilang dan guru tidak akan pernah bisa login.
 */
class LoginNigTest extends TestCase
{
    use RefreshDatabase;

    private function buatUser(array $atribut = []): User
    {
        return User::create(array_merge([
            'name' => 'Guru Uji',
            'email' => 'guruuji@guru.com',
            'nig' => '0170194016',
            'password' => Hash::make('rahasia123'),
            'role' => 'guru',
            'kelas' => null,
            'email_verified_at' => now(),
        ], $atribut));
    }

    public function test_login_dengan_nig(): void
    {
        $this->buatUser(['nig' => '0170194016']);

        $response = $this->post(route('login'), [
            'nig' => '0170194016',
            'password' => 'rahasia123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('lapor.index'));
    }

    public function test_nig_dengan_nol_di_depan_tetap_cocok(): void
    {
        $this->buatUser(['nig' => '0170194016']);

        // Kalau NIG diubah jadi integer, 0170194016 -> 174194016 dan
        // guru yang mengetik NIG aslinya akan gagal login.
        $this->post(route('login'), [
            'nig' => '0170194016',
            'password' => 'rahasia123',
        ]);

        $this->assertAuthenticated();
    }

    public function test_password_salah_ditolak(): void
    {
        $this->buatUser();

        $response = $this->post(route('login'), [
            'nig' => '0170194016',
            'password' => 'salah',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('nig');
    }

    public function test_nig_tidak_terdaftar_ditolak(): void
    {
        $response = $this->post(route('login'), [
            'nig' => '9999999999',
            'password' => 'rahasia123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('nig');
    }

    public function test_nig_kosong_ditolak(): void
    {
        $response = $this->post(route('login'), [
            'nig' => '',
            'password' => 'rahasia123',
        ]);

        $response->assertSessionHasErrors('nig');
        $this->assertGuest();
    }

    /**
     * Akun lama (walas per-kelas) belum punya NIG. Kalau email
     * langsung ditutup, 46 akun itu terkunci selamanya.
     */
    public function test_email_tetap_diterima_sebagai_cadangan(): void
    {
        $this->buatUser(['email' => 'walasx1@guru.com', 'nig' => null]);

        $this->post(route('login'), [
            'nig' => 'walasx1@guru.com',
            'password' => 'rahasia123',
        ]);

        $this->assertAuthenticated();
    }

    public function test_admin_diarahkan_ke_dashboard(): void
    {
        $this->buatUser(['role' => 'admin', 'email' => 'admin@x.com', 'nig' => '1250186083']);

        $response = $this->post(route('login'), [
            'nig' => '1250186083',
            'password' => 'rahasia123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    public function test_form_login_menggunakan_field_nig(): void
    {
        $html = (string) $this->get(route('login'))->getContent();

        $this->assertStringContainsString('name="nig"', $html);
        $this->assertStringNotContainsString('name="email"', $html);
    }
}
