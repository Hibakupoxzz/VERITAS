<?php

namespace App\Observers;

use App\Models\Siswa;
use App\Models\User;
use App\Services\SiswaAccountService;

class SiswaObserver
{
    public function __construct(
        private readonly SiswaAccountService $accounts
    ) {}

    public function created(Siswa $siswa): void
    {
        $this->accounts->sync($siswa);
    }

    public function updated(Siswa $siswa): void
    {
        $this->accounts->sync($siswa);
    }

    public function deleted(Siswa $siswa): void
    {
        User::where('siswa_id', $siswa->id)
            ->where('role', 'siswa')
            ->delete();
    }
}
