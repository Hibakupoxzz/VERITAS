<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Siswa;
use App\Models\Pelanggaran;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $view->with('totalSiswa', Siswa::count());
            $view->with('totalPelanggaran', Pelanggaran::count());
        });
    }
}
