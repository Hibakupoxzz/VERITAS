<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Inti
        User::create([
            'name' => 'Admin',
            'email' => 'duospirits@gmail.com',
            'password' => Hash::make('zidan123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Guru PDS',
            'email' => 'gurupds@gmail.com',
            'password' => Hash::make('pds123'),
            'role' => 'pds',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Guru BK',
            'email' => 'gurubk@gmail.com',
            'password' => Hash::make('bk123'),
            'role' => 'bk',
            'email_verified_at' => now(),
        ]);

        // 2. Daftar Semua Kelas
        $kelas = [
            'X-RPL-1', 'X-RPL-2', 'X-RPL-3', 'X-RPL-4', 'X-RPL-5',
            'X-TOI-1', 'X-TOI-2', 'X-TOI-3', 'X-TOI-4', 'X-TOI-5',
            'X-TKJ-1', 'X-TKJ-2', 'X-TKJ-3', 'X-TKJ-4', 'X-TKJ-5',
            'X-DKV-1', 'X-DKV-2', 'X-DKV-3', 'X-DKV-4', 'X-DKV-5',
            'X-LPB-1', 'X-LPB-2', 'X-LPB-3', 'X-LPB-4', 'X-LPB-5',
            'XI-RPL-1', 'XI-RPL-2', 'XI-TOI-1', 'XI-TKJ-1', 'XI-TKJ-2',
            'XI-DKV-1', 'XI-DKV-2', 'XI-DKV-3', 'XI-LPB-1', 'XI-LPB-2',
            'XII-RPL-1', 'XII-RPL-2', 'XII-TKJ-1', 'XII-TKJ-2', 'XII-TKJ-3',
            'XII-DKV-1', 'XII-DKV-2', 'XII-DKV-3', 'XII-DKV-4', 'XII-LPB-1', 'XII-LPB-2',
        ];

        // 3. Buat Akun Walas Untuk Masing-masing Kelas
        foreach ($kelas as $k) {
            // Hapus tanda strip dan jadikan huruf kecil.
            // Contoh 'X-RPL-1' menjadi 'xrpl1'
            $emailPrefix = strtolower(str_replace('-', '', $k));

            User::create([
                'name' => 'Walas '.$k,
                'email' => $emailPrefix.'@guru.com', // Hasil: xrpl1@guru.com
                'password' => Hash::make('walas123'),
                'role' => 'walas',
                'kelas' => $k,
                'email_verified_at' => now(),
            ]);
        }
    }
}
