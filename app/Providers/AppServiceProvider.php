<?php

namespace App\Providers;

use App\Models\Pelanggaran;
use App\Models\Siswa;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
