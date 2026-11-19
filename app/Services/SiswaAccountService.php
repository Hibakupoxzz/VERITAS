<?php

namespace App\Services;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Satu tempat untuk membuat/menyelaraskan akun login siswa.
 *
 * Dipakai oleh SiswaObserver (saat data siswa dibuat/diubah) dan oleh
 * perintah `siswa:sync-akun` untuk data siswa yang sudah ada sebelum
 * fitur login siswa ditambahkan.
 */
class SiswaAccountService
{
    /**
     * Hash password awal dihitung sekali per proses. Import Excel bisa
     * membuat ratusan akun sekaligus; menghitung bcrypt untuk setiap
     * siswa membuat import jauh lebih lambat dari perlu.
     */
    private static ?string $passwordAwalHash = null;

    /**
     * Pastikan siswa punya akun dengan NIS = nisn dan password awal
     * "password". Akun yang sudah ada tidak password-nya ditimpa, agar
     * perubahan password oleh siswa tidak hilang setiap data di-update.
     */
    public function sync(Siswa $siswa): User
    {
        $user = User::firstOrNew(['siswa_id' => $siswa->id]);

        if (! $user->exists) {
            $user->role = 'siswa';
            $user->password = $this->passwordAwalHash();
        }

        $user->name = $siswa->nama;
        $user->nis = $siswa->nisn;
        $user->email = $this->emailUntuk($siswa);
        $user->save();

        return $user;
    }

    /**
     * Kembalikan semua password akun siswa ke password awal.
     *
     * Dipakai hanya lewat perintah `siswa:sync-akun --reset-password`
     * karena menimpa password yang sedang dipakai siswa.
     */
    public function resetPassword(User $user): void
    {
        $user->forceFill([
            'password' => $this->passwordAwalHash(),
        ])->save();
    }

    private function passwordAwalHash(): string
    {
        return self::$passwordAwalHash ??= Hash::make(Siswa::PASSWORD_AWAL);
    }

    /**
     * Kolom email users bersifat unique & wajib, jadi akun siswa
     * memakai email pengganti yang dibentuk dari NISN.
     */
    private function emailUntuk(Siswa $siswa): string
    {
        return $siswa->nisn.'@siswa.veritas.local';
    }
}
