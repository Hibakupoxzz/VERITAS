<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder akun Guru Khusus / Guru Piket yang dapat mengakses semua kelas dan siswa.
 */
class GuruUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'gurupiket@guru.com'],
            [
                'name' => 'Guru Piket (Umum)',
                'email' => 'gurupiket@guru.com',
                'password' => Hash::make('guru123'),
                'role' => 'guru',
                'kelas' => null,
                'email_verified_at' => now(),
            ]
        );
    }
}
