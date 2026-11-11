<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

/**
 * Mengganti nama akun Super Admin menjadi "smkpluspnb".
 *
 * Nama akun masih "Admin" sejak seeder pertama. Nama barunya
 * ditetapkan sekolah. Login tetap memakai email yang sama
 * (duospirits@gmail.com) karena akun ini belum punya NIG.
 */
return new class extends Migration
{
    private const EMAIL = 'duospirits@gmail.com';

    private const NAMA_BARU = 'smkpluspnb';

    public function up(): void
    {
        User::where('email', self::EMAIL)
            ->where('role', 'admin')
            ->update(['name' => self::NAMA_BARU]);
    }

    public function down(): void
    {
        User::where('email', self::EMAIL)
            ->where('role', 'admin')
            ->update(['name' => 'Admin']);
    }
};
