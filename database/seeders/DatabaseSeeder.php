<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Jalankan seeder sesuai urutan dependency data.
     */
    public function run(): void
    {
        $this->call([
            // Akun pengguna
            AdminUserSeeder::class,
            PdsUserSeeder::class,
            BkUserSeeder::class,
            WalasUserSeeder::class,

            // Data referensi & master
            AturanPelanggaranSeeder::class,

            // Data operasional
            SiswaSeeder::class,
        ]);
    }
}
