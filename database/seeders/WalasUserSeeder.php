<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\Traits\HasKelasList;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder akun Wali Kelas berdasarkan daftar kelas.
 */
class WalasUserSeeder extends Seeder
{
    use HasKelasList;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getKelasList() as $kelas) {
            // Contoh: 'X-RPL-1' → 'xrpl1@guru.com'
            $emailPrefix = strtolower(str_replace('-', '', $kelas));

            User::firstOrCreate(
                ['email' => $emailPrefix . '@guru.com'],
                [
                    'name' => 'Walas ' . $kelas,
                    'email' => $emailPrefix . '@guru.com',
                    'password' => Hash::make('walas123'),
                    'role' => 'walas',
                    'kelas' => $kelas,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
