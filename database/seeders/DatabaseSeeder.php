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
            GuruUserSeeder::class,

            // Data referensi & master
            AturanPelanggaranSeeder::class,
        ]);

        /*
        | Data siswa tidak lagi di-seed.
        | Gunakan import Excel (SiswaController::import / SiswaImport)
        | atau tambah manual lewat menu Data Siswa.
        */
    }
}
