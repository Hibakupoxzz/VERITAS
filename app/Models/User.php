<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'nig', 'password', 'role', 'jabatan', 'kelas', 'access_level'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Hak Akses Tambahan (di luar role)
    |--------------------------------------------------------------------------
    |
    | Nilai kolom `access_level`. Dipakai untuk dua kebutuhan yang
    | tidak bisa diwakili role karena role dipakai bersama.
    */

    /** Semua fitur, kecuali Manajemen Akun. */
    public const ACCESS_FULL = 'full';

    /** Boleh menambah pelanggaran langsung (menu Tambah Pelanggaran). */
    public const ACCESS_PELANGGARAN_WRITER = 'pelanggaran_writer';

    /** Boleh menambah & memverifikasi pelanggaran layaknya Guru PDS. */
    public const ACCESS_VERIFIKATOR = 'verifikator';

    /*
    |--------------------------------------------------------------------------
    | Role Helpers
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Apakah user punya akses penuh (semua fitur selain Manajemen Akun)?
     *
     * Dipakai middleware EnsureCanManage untuk membuka seluruh rute
     * manajemen, dan sidebar untuk menampilkan menu selengkap admin.
     */
    public function hasFullAccess(): bool
    {
        return $this->access_level === self::ACCESS_FULL;
    }

    /**
     * Boleh menambah pelanggaran langsung lewat menu Tambah Pelanggaran?
     *
     * Berbeda dari melaporkan (yang selalu berstatus pending), ini
     * menyimpan pelanggaran sebagai sudah diverifikasi.
     */
    public function isPelanggaranWriter(): bool
    {
        return $this->hasFullAccess()
            || in_array($this->access_level, [
                self::ACCESS_PELANGGARAN_WRITER,
                self::ACCESS_VERIFIKATOR,
            ], true);
    }

    public function isWalas(): bool
    {
        return $this->role === 'walas';
    }

    public function isPds(): bool
    {
        return $this->role === 'pds';
    }

    public function isBk(): bool
    {
        return $this->role === 'bk';
    }

    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    /**
     * Apakah user termasuk unsur pimpinan sekolah (kepsek / waka)?
     *
     * Ditentukan dari jabatan resmi, BUKAN dari role — karena role
     * "guru" dipakai bersama oleh semua tenaga pendidik, termasuk
     * guru biasa yang disebut "Guru Khusus / Piket" di label role.
     */
    public function isPimpinan(): bool
    {
        $jabatan = mb_strtolower((string) $this->jabatan);

        return str_contains($jabatan, 'kepala sekolah')
            || str_contains($jabatan, 'waka');
    }

    /**
     * Apakah user berwenang memverifikasi laporan?
     */
    public function canVerify(): bool
    {
        return in_array($this->role, ['admin', 'pds'])
            || $this->hasFullAccess()
            || $this->access_level === self::ACCESS_VERIFIKATOR;
    }

    /**
     * Apakah user berwenang menambah/mengedit/menghapus prestasi?
     */
    public function canManagePrestasi(): bool
    {
        return in_array($this->role, ['admin', 'bk'])
            || $this->hasFullAccess();
    }

    /**
     * Apakah user berwenang menambah/mengedit/menghapus data pelanggaran secara langsung?
     */
    public function canDirectAddPelanggaran(): bool
    {
        return in_array($this->role, ['admin', 'pds'])
            || $this->isPelanggaranWriter();
    }

    /**
     * Apakah user berwenang melaporkan pelanggaran via form pelaporan?
     */
    public function canReportPelanggaran(): bool
    {
        return in_array($this->role, ['admin', 'walas', 'bk', 'guru']);
    }

    /**
     * Label role yang ditampilkan di UI.
     *
     * Unsur pimpinan sekolah dikembalikan dari jabatannya ("Kepala
     * Sekolah", "Waka Kesiswaan"), bukan "Guru Khusus / Piket" —
     * label itu hanya benar untuk guru piket sungguhan.
     */
    public function getRoleLabelAttribute(): string
    {
        if ($this->isPimpinan() && $this->jabatan) {
            return $this->jabatanSingkat();
        }

        return match ($this->role) {
            'admin' => 'Super Admin',
            'walas' => 'Wali Kelas',
            'pds' => 'Guru PDS',
            'bk' => 'Guru BK',
            'guru' => 'Guru Khusus / Piket',
            default => 'Guru',
        };
    }

    /**
     * Jabatan yang dipersingkat untuk tampilan sempit.
     *
     * Di daftar resmi sekolah jabatan waka ditulis panjang, misalnya
     * "Waka Urs. Hubungan Masyarakat & Guru Pendidikan Matematika".
     * Nama jabatan resmi yang utuh disimpan di kolom `jabatan`; yang
     * dipangkas hanya bagian yang tidak perlu untuk label — singkatan
     * "Urs." dan jabatan pendamping setelah tanda "&".
     */
    public function jabatanSingkat(): string
    {
        $jabatan = (string) $this->jabatan;

        if ($jabatan === '') {
            return '';
        }

        $jabatan = preg_replace('/\bUrs\.\s*/u', '', $jabatan);
        $jabatan = explode('&', $jabatan)[0];

        return trim(preg_replace('/\s+/u', ' ', $jabatan));
    }

    /**
     * Guru biasa: role "guru" yang BUKAN unsur pimpinan.
     *
     * Ini yang tidak perlu melihat data pribadi siswa: daftar
     * pelanggaran dan rangking siapa paling banyak melakukan pelanggaran.
     *
     * Unsur pimpinan sengaja dikecualikan: mereka monitoring
     * seluruh sekolah, jadi tetap perlu melihat semuanya meski role
     * aplikasinya sama-sama "guru".
     */
    public function isGuruBiasa(): bool
    {
        return $this->isGuru() && ! $this->isPimpinan();
    }

    /**
     * Kunci untuk memilih daftar route yang boleh dibuka di menu.
     *
     * HANYA untuk menu: pemblokiran URL tetap dipegang
     * EnsureCanManage, supaya ada satu tempat yang benar-benar
     * menentukan apakah sebuah request ditolak.
     *
     * Mengembalikan "pimpinan" untuk kepala sekolah dan waka supaya
     * mereka punya hak akses penuh, meski role-nya "guru".
     */
    public function navKey(): string
    {
        // Admin dan pemegang akses penuh memakai menu lengkap.
        if ($this->isAdmin() || $this->hasFullAccess()) {
            return 'admin';
        }

        return $this->isPimpinan() ? 'pimpinan' : (string) $this->role;
    }

    /**
     * Decode JSON daftar kelas binaan PDS.
     *
     * @return array<string>
     */
    public function getPdsKelasList(): array
    {
        if (! $this->isPds() || empty($this->kelas)) {
            return [];
        }

        $decoded = json_decode($this->kelas, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Decode JSON daftar kelas binaan BK.
     *
     * @return array<string>
     */
    public function getBkKelasList(): array
    {
        if (! $this->isBk() || empty($this->kelas)) {
            return [];
        }

        $decoded = json_decode($this->kelas, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Helper umum untuk mengambil daftar kelas binaan (PDS / BK).
     *
     * @return array<string>
     */
    public function getBinaanKelasList(): array
    {
        if ($this->isPds()) {
            return $this->getPdsKelasList();
        }

        if ($this->isBk()) {
            return $this->getBkKelasList();
        }

        return [];
    }
}
