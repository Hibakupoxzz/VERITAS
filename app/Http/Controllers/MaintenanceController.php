<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

/**
 * Bersihkan Website — pemeliharaan cache & file sementara.
 *
 * Menghapus cache view/config/route, file yang sudah dikompilasi,
 * dan log lama. TIDAK menyentuh data pelanggaran, prestasi, siswa,
 * maupun akun, dan sengaja tidak menghapus sesi supaya user yang
 * sedang masuk tidak ikut keluar.
 */
class MaintenanceController extends Controller
{
    public function index()
    {
        return view('maintenance.index', [
            'info' => [
                'log_size' => $this->formatBytes($this->logSize()),
                'log_files' => count($this->logFiles()),
                'view_cache' => count(glob(storage_path('framework/views/*.php')) ?: []),
                'cache_driver' => (string) config('cache.default'),
                'session_driver' => (string) config('session.driver'),
                'config_cached' => app()->configurationIsCached(),
                'route_cached' => app()->routesAreCached(),
            ],
        ]);
    }

    public function clean()
    {
        /*
         * optimize:clear menghapus cache aplikasi, cache config,
         * cache route, cache event, compiled view, dan file
         * hasil `optimize` — satu perintah untuk semuanya.
         */
        Artisan::call('optimize:clear');
        $output = trim(Artisan::output());

        // Log lama dibersihkan terpisah (tidak disentuh optimize:clear).
        $dihapus = 0;

        foreach ($this->logFiles() as $file) {
            File::put($file, '');
            $dihapus++;
        }

        $pesan = 'Website berhasil dibersihkan: cache aplikasi, config, route, view, dan '
            .$dihapus.' file log dikosongkan.';

        return back()
            ->with('success', $pesan)
            ->with('maintenance_output', $output);
    }

    /**
     * Daftar file log di storage/logs.
     *
     * @return array<int, string>
     */
    private function logFiles(): array
    {
        return glob(storage_path('logs/*.log')) ?: [];
    }

    private function logSize(): int
    {
        return array_sum(array_map(
            fn (string $file) => (int) @filesize($file),
            $this->logFiles()
        ));
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }

        $units = ['KB', 'MB', 'GB'];
        $value = $bytes / 1024;
        $i = 0;

        while ($value >= 1024 && $i < count($units) - 1) {
            $value /= 1024;
            $i++;
        }

        return round($value, 1).' '.$units[$i];
    }
}
