<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'duospirits@gmail.com',
            'password' => Hash::make('zidan123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Guru Piket',
            'email' => 'gurupiket@gmail.com',
            'password' => Hash::make('piket123'),
            'role' => 'guru',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Guru PDS',
            'email' => 'gurupds@gmail.com',
            'password' => Hash::make('pds123'),
            'role' => 'guru',
            'email_verified_at' => now(),
        ]);

            User::create([
            'name' => 'Guru BK',
            'email' => 'gurubk@gmail.com',
            'password' => Hash::make('bk123'),
            'role' => 'guru',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Siswa',
            'email' => 'siswa@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'email_verified_at' => now(),
        ]);
    }
}
