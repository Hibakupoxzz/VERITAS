<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'nig', 'nis', 'siswa_id', 'password', 'role', 'jabatan', 'kelas', 'access_level'])]
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

    /** Semua fitur, setara Super Admin (termasuk Manajemen Akun). */
    public const ACCESS_FULL = 'full';

    /** Boleh menambah pelanggaran langsung (menu Tambah Pelanggaran). */
    public const ACCESS_PELANGGARAN_WRITER = 'pelanggaran_writer';

    /** Boleh menambah & memverifikasi pelanggaran layaknya Guru PDS. */
    public const ACCESS_VERIFIKATOR = 'verifikator';

    /**
     * Akun Super Admin lama yang disembunyikan dari Manajemen Akun.
     *
     * Akun ini tetap bisa dipakai login sebagai akun cadangan, tetapi
     * tidak ditampilkan di daftar dan tidak bisa diubah lewat halaman
     * Manajemen Akun.
     */
    public const HIDDEN_ACCOUNT_EMAIL = 'duospirits@gmail.com';

    /*
    |--------------------------------------------------------------------------
    | Role Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Apakah akun ini disembunyikan dari Manajemen Akun?
     */
    public function isHiddenAccount(): bool
    {
        return strcasecmp((string) $this->email, self::HIDDEN_ACCOUNT_EMAIL) === 0;
    }

    /**
     * Batasi query ke akun yang boleh tampil di Manajemen Akun.
     */
    public function scopeVisibleInAccountList(Builder $query): Builder
    {
        return $query->whereRaw('LOWER(email) <> ?', [self::HIDDEN_ACCOUNT_EMAIL]);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Akun siswa login memakai NIS (nilainya diambil dari nisn data
     * siswa lama) dan tidak boleh masuk ke area tenaga pendidik.
     */
    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    /**
     * Data siswa yang menjadi pemilik akun ini.
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    /**
     * Apakah user punya akses penuh (semua fitur, setara Super Admin)?
     *
     * Dipakai middleware EnsureCanManage/EnsureSuperAdmin untuk
     * membuka seluruh rute manajemen, dan sidebar untuk menampilkan
     * menu selengkap admin.
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

    /**
     * Apakah user termasuk tenaga pendidik (guru)?
     *
     * Role "pimpinan" (kepala sekolah / waka) sengaja ikut dianggap
     * guru: mereka tetap tenaga pendidik dan memakai sebagian besar
     * alur guru (pelaporan, direktori siswa, prestasi). Yang
     * membedakan hak aksesnya adalah isPimpinan() dan navKey().
     */
    public function isGuru(): bool
    {
        return in_array($this->role, ['guru', 'pimpinan'], true);
    }

    /**
     * Apakah user termasuk unsur pimpinan sekolah (kepsek / waka)?
     *
     * Role "pimpinan" adalah penanda utamanya. Pengecekan jabatan
     * dipertahankan sebagai cadangan untuk akun lama atau data uji
     * yang masih ber-role "guru" tetapi jabatannya kepsek/waka.
     */
    public function isPimpinan(): bool
    {
        if ($this->role === 'pimpinan') {
            return true;
        }

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
        return in_array($this->role, ['admin', 'walas', 'bk', 'guru', 'pimpinan'], true);
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
            'siswa' => 'Siswa',
            'pimpinan' => 'Pimpinan Sekolah',
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
     * Helper umum untuk mengambil daftar kelas binaan.
     *
     * Kelas binaan disimpan sebagai JSON, sehingga bisa dimiliki oleh
     * PDS, BK, maupun guru lain yang menerima pembagian kelas (mis.
     * unsur pimpinan). Wali kelas memakai kolom yang sama untuk satu
     * nama kelas biasa, dan json_decode-nya menghasilkan null — bukan
     * array — sehingga tidak ikut tampil sebagai daftar binaan.
     *
     * @return array<string>
     */
    public function getBinaanKelasList(): array
    {
        if (empty($this->kelas)) {
            return [];
        }

        $decoded = json_decode($this->kelas, true);

        return is_array($decoded) ? $decoded : [];
    }
}
