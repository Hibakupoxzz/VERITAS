<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder untuk akun inti aplikasi (Super Admin).
 *
 * Ada dua akun Super Admin dengan hak yang sama. Akun lama
 * (duospirits@gmail.com) tetap dipertahankan dan namanya
 * dikembalikan menjadi "Admin"; akun baru memakai nama sekolah.
 */
class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coreUsers = [
            [
                'name' => 'Admin',
                'email' => 'duospirits@gmail.com',
                'password' => Hash::make('zidan123'),
                'role' => 'admin',
            ],
            [
                'name' => 'smkpluspnb',
                'email' => 'smkpluspnb@gmail.com',
                'password' => Hash::make('smkpluspnb123'),
                'role' => 'admin',
            ],
        ];

        foreach ($coreUsers as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                array_merge($user, ['email_verified_at' => now()])
            );
        }
    }
}
