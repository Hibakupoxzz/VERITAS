<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder akun Guru PDS beserta kelas binaannya (disimpan sebagai JSON).
 */
class PdsUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pdsData = [
            [
                'name' => 'Sopiyan',
                'kelas' => [
                    'XI-TKJ-1', 
                    'XII-DKV-3', 'XII-LPB-1', 'XII-DKV-4'
                ],
            ],
            [
                'name' => 'Ari Zulfikar',
                'kelas' => [
                    'X-TOI-1', 'X-DKV-2', 'X-LPB-1', 
                    'XI-TOI-1', 'XI-LPB-1', 'XI-LPB-2', 
                    'XII-TKJ-1'
                ],
            ],
            [
                'name' => 'Fitri Rohmayasari',
                'kelas' => [
                    'X-RPL-2', 'X-TKJ-2', 'X-LPB-2', 
                    'XI-DKV-2', 
                    'XII-RPL-1', 'XII-RPL-2', 'XII-DKV-2'
                ],
            ],
            [
                'name' => 'Muzakir Zulkarnaen',
                'kelas' => [
                    'X-DKV-3', 'X-TOI-2', 'X-DKV-1', 
                    'XI-DKV-1', 'XI-DKV-3', 
                    'XII-TKJ-2', 'XII-LPB-2'
                ],
            ],
            [
                'name' => 'Yulianie Kasari',
                'kelas' => [
                    'X-RPL-1', 'X-TKJ-1', 
                    'XI-RPL-1', 'XI-RPL-2', 'XI-TKJ-2', 
                    'XII-TKJ-3', 'XII-DKV-1'
                ],
            ],
        ];

        foreach ($pdsData as $pds) {
            // Generate email: lowercase, hanya huruf dan angka
            $emailPrefix = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $pds['name']));

            User::firstOrCreate(
                ['email' => $emailPrefix . '@pds.com'],
                [
                    'name' => $pds['name'],
                    'email' => $emailPrefix . '@pds.com',
                    'password' => Hash::make('pds123'),
                    'role' => 'pds',
                    'kelas' => json_encode($pds['kelas']), // Simpan array sebagai JSON string
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
