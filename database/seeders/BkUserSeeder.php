<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder akun Tim BK (role: bk) beserta kelas binaannya (disimpan sebagai JSON).
 */
class BkUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bkData = [
            [
                'name' => 'Rakhma Dhania',
                'kode' => null, // Koordinator BK
                'kelas' => [],
            ],
            [
                'name' => 'R. Dodi Setiadi',
                'kode' => '29',
                'kelas' => [
                    'XI-RPL-1', 'XI-TOI-1', 'X-TOI-2', 'X-TKJ-2',
                ],
            ],
            [
                'name' => 'Sholehudin Aditya Utama',
                'kode' => '27',
                'kelas' => [
                    'XI-RPL-2', 'XI-LPB-2', 'X-RPL-2', 'X-TKJ-1',
                ],
            ],
            [
                'name' => 'Anisah Dwi Rahayu',
                'kode' => '33',
                'kelas' => [
                    'XI-TKJ-1', 'X-DKV-1', 'X-DKV-3',
                ],
            ],
            [
                'name' => 'Anita Lestari',
                'kode' => '34',
                'kelas' => [
                    'XI-DKV-1', 'XI-TKJ-2', 'X-LPB-1', 'X-TOI-1',
                ],
            ],
            [
                'name' => 'Ariska Dwi Saputri',
                'kode' => '35',
                'kelas' => [],
            ],
            [
                'name' => 'Wilda Septiarini',
                'kode' => '38',
                'kelas' => [
                    'XI-DKV-2', 'XI-LPB-1', 'X-LPB-2',
                ],
            ],
            [
                'name' => 'Ardi Sukma',
                'kode' => '39',
                'kelas' => [
                    'XI-DKV-3', 'X-DKV-2', 'X-RPL-1',
                ],
            ],
        ];

        // Bersihkan placeholder lama jika ada
        User::where('role', 'bk')->whereIn('email', ['@bk.com', 'wildaseptiriani@bk.com'])->delete();

        foreach ($bkData as $bk) {
            // Generate email: lowercase, hanya huruf dan angka
            $emailPrefix = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $bk['name']));

            User::updateOrCreate(
                ['email' => $emailPrefix . '@bk.com'],
                [
                    'name' => $bk['name'],
                    'email' => $emailPrefix . '@bk.com',
                    'password' => Hash::make('bk123'),
                    'role' => 'bk',
                    'kelas' => ! empty($bk['kelas']) ? json_encode($bk['kelas']) : null,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
