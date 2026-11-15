<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Ikon situs harus terpasang di halaman login dan halaman aplikasi.
 */
class FaviconTest extends TestCase
{
    use RefreshDatabase;

    public function test_file_ikon_tersedia(): void
    {
        foreach ([
            'favicon.svg',
            'favicon.ico',
            'favicon-16x16.png',
            'favicon-32x32.png',
            'apple-touch-icon.png',
        ] as $file) {
            $this->assertFileExists(public_path($file), "Ikon {$file} tidak ada");
        }
    }

    public function test_halaman_login_memuat_ikon_situs(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('favicon.svg', false);
        $response->assertSee('apple-touch-icon.png', false);
        $response->assertSee('name="theme-color"', false);
    }

    public function test_halaman_aplikasi_memuat_ikon_situs(): void
    {
        $user = User::create([
            'name' => 'Guru Ikon',
            'email' => 'guruikon@test.com',
            'nig' => '0250299999',
            'password' => Hash::make('rahasia123'),
            'role' => 'guru',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('lapor.index'));

        $response->assertOk();
        $response->assertSee('favicon.svg', false);
        $response->assertSee('apple-touch-icon.png', false);
    }
}
