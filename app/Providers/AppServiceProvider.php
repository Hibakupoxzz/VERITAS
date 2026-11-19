<?php

namespace App\Providers;

use App\Models\Pelanggaran;
use App\Models\Siswa;
use App\Observers\SiswaObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(SiswaObserver $siswaObserver): void
    {
        // Setiap data siswa (baru/import/edit) otomatis punya akun login.
        Siswa::observe($siswaObserver);

        View::composer('*', function ($view) {
            $view->with('totalSiswa', Siswa::count());
            $view->with('totalPelanggaran', Pelanggaran::count());
        });
    }
}
