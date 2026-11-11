<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

/**
 * Menyiapkan dua akun Super Admin:
 *
 *  - duospirits@gmail.com  -> nama dikembalikan menjadi "Admin"
 *  - smkpluspnb@gmail.com  -> akun Super Admin baru
 *
 * Migrasi ini menyentuh data yang sudah ada, jadi akun baru hanya
 * dibuat kalau belum ada. Pada instalasi baru (database kosong)
 * migrasi ini tidak melakukan apa-apa; kedua akun dibuat oleh
 * AdminUserSeeder.
 */
return new class extends Migration
{
    private const EMAIL_LAMA = 'duospirits@gmail.com';

    private const EMAIL_BARU = 'smkpluspnb@gmail.com';

    public function up(): void
    {
        $adminLama = User::where('email', self::EMAIL_LAMA)
            ->where('role', 'admin')
            ->first();

        // Database baru/kosong: kedua akun dibuat oleh AdminUserSeeder,
        // bukan oleh migrasi ini.
        if ($adminLama === null) {
            return;
        }

        $adminLama->update(['name' => 'Admin']);

        if (! User::where('email', self::EMAIL_BARU)->exists()) {
            User::create([
                'name' => 'smkpluspnb',
                'email' => self::EMAIL_BARU,
                'password' => Hash::make('smkpluspnb123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        User::where('email', self::EMAIL_BARU)
            ->where('role', 'admin')
            ->delete();

        User::where('email', self::EMAIL_LAMA)
            ->where('role', 'admin')
            ->update(['name' => 'smkpluspnb']);
    }
};
