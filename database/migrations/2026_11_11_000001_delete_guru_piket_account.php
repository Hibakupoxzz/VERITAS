<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

/**
 * Menghapus akun "Guru Piket (Umum)" (gurupiket@guru.com).
 *
 * Akun ini dibuat GuruUserSeeder sebagai contoh akun guru yang bisa
 * melihat semua kelas. Sejak daftar tenaga pendidik resmi sekolah
 * di-seed lengkap, akun contoh ini tidak diperlukan lagi dan malah
 * muncul sebagai user tanpa NIG di sistem.
 *
 * Seeder-nya (GuruUserSeeder) juga sudah dihapus dari DatabaseSeeder;
 * migration ini membersihkan akun yang sudah telanjur ada di
 * database yang berjalan.
 */
return new class extends Migration
{
    private const EMAIL = 'gurupiket@guru.com';

    public function up(): void
    {
        User::where('email', self::EMAIL)->delete();
    }

    public function down(): void
    {
        // Dipulihkan apa adanya seperti isi GuruUserSeeder.
        User::firstOrCreate(
            ['email' => self::EMAIL],
            [
                'name' => 'Guru Piket (Umum)',
                'password' => Hash::make('guru123'),
                'role' => 'guru',
                'kelas' => null,
                'email_verified_at' => now(),
            ]
        );
    }
};
