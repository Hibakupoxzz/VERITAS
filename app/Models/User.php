<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'kelas'])]
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
    | Role Helpers
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
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
     * Apakah user berwenang memverifikasi laporan?
     */
    public function canVerify(): bool
    {
        return in_array($this->role, ['admin', 'pds', 'bk']);
    }

    /**
     * Label role yang ditampilkan di UI.
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Super Admin',
            'walas' => 'Wali Kelas',
            'pds' => 'Guru PDS',
            'bk' => 'Guru BK',
            default => 'Guru',
        };
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
}
