<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder akun Tim BK (role: bk).
 */
class BkUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bkNames = [
            'Rakhma Dhania',
            'Sholehudin Aditya Utama',
            'R. Dodi Setiadi',
            'Anita Lestari',
            'Anisah Dwi Rahayu',
            'Ariska Dwi Saputri',
            'Wilda Septiriani',
            '',
        ];

        foreach ($bkNames as $name) {
            // Generate email: lowercase, hanya huruf dan angka
            $emailPrefix = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));

            User::firstOrCreate(
                ['email' => $emailPrefix . '@bk.com'],
                [
                    'name' => $name,
                    'email' => $emailPrefix . '@bk.com',
                    'password' => Hash::make('bk123'),
                    'role' => 'bk',
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
