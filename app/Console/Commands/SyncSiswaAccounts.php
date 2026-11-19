<?php

namespace App\Console\Commands;

use App\Models\Siswa;
use App\Models\User;
use App\Services\SiswaAccountService;
use Illuminate\Console\Command;

class SyncSiswaAccounts extends Command
{
    protected $signature = 'siswa:sync-akun
                            {--reset-password : Kembalikan password semua akun siswa ke "password"}';

    protected $description = 'Buat/selaraskan akun login siswa dari data siswa yang sudah ada';

    public function handle(SiswaAccountService $accounts): int
    {
        $dibuat = 0;
        $diselaraskan = 0;
        $direset = 0;

        Siswa::query()->orderBy('id')->chunkById(500, function ($siswas) use (
            $accounts,
            &$dibuat,
            &$diselaraskan,
            &$direset
        ) {
            foreach ($siswas as $siswa) {
                $sudahAda = User::where('siswa_id', $siswa->id)->exists();

                $accounts->sync($siswa);

                if ($sudahAda) {
                    $diselaraskan++;
                } else {
                    $dibuat++;
                }

                if ($this->option('reset-password')) {
                    /** @var User $user */
                    $user = $siswa->user;
                    $accounts->resetPassword($user);
                    $direset++;
                }
            }
        });

        $this->info("Selesai: {$dibuat} akun dibuat, {$diselaraskan} akun diselaraskan.");

        if ($this->option('reset-password')) {
            $this->warn("{$direset} password dikembalikan ke 'password'.");
        }

        $this->line('Siswa login memakai NIS (nisn) dengan password awal "password".');

        return self::SUCCESS;
    }
}
