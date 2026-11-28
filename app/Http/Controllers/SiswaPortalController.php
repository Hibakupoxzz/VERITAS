<?php

namespace App\Http\Controllers;

use App\Models\Pelanggaran;
use App\Models\Prestasi;
use App\Models\Siswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class SiswaPortalController extends Controller
{
    /**
     * Portal siswa: hanya menampilkan ringkasan poin dan riwayat
     * pelanggaran/prestasi milik siswa yang sedang login.
     */
    public function index(Request $request)
    {
        $siswa = $this->siswaLogin($request);

        if (! $siswa) {
            return $this->keluarKarenaTidakTerhubung($request);
        }

        return view('siswa.portal', [
            'siswa' => $siswa,
            ...$this->ringkasan($siswa),
            'riwayat' => $this->riwayat($siswa),
            'sinkronPada' => now()->toIso8601String(),
        ]);
    }

    /**
     * Endpoint JSON untuk pembaruan realtime.
     *
     * Bila `since` dikirim, hanya entri yang berubah setelah waktu itu
     * yang dikembalikan. Ringkasan tetap dikirim agar kartu angka ikut
     * diperbarui tanpa memuat ulang halaman.
     */
    public function riwayatRealtime(Request $request): JsonResponse
    {
        $siswa = $this->siswaLogin($request);

        if (! $siswa) {
            return response()->json(['message' => 'Akun tidak terhubung dengan data siswa.'], 401);
        }

        $sejak = $this->parseSince($request->query('since'));

        return response()->json([
            'sinkron_pada' => now()->toIso8601String(),
            ...$this->ringkasan($siswa),
            'riwayat' => $this->riwayat($siswa, $sejak),
        ]);
    }

    private function siswaLogin(Request $request): ?Siswa
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user?->siswa;
    }

    private function keluarKarenaTidakTerhubung(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with(
            'status',
            'Akun siswa belum terhubung dengan data siswa. Silakan hubungi administrator.'
        );
    }

    /**
     * Tiga angka yang ditampilkan di portal.
     *
     * - jumlah pelanggaran: seluruh catatan (semua status)
     * - jumlah prestasi: seluruh catatan prestasi
     * - poin sekarang: 100 − poin pelanggaran terverifikasi + poin prestasi
     */
    private function ringkasan(Siswa $siswa): array
    {
        $pelanggarans = Pelanggaran::query()
            ->where('siswa_id', $siswa->id)
            ->get(['status', 'poin']);

        $prestasis = Prestasi::query()
            ->where('siswa_id', $siswa->id)
            ->get(['poin']);

        $terverifikasi = $pelanggarans->where('status', 'diverifikasi');

        $poinPelanggaran = (int) $terverifikasi->sum('poin');
        $poinPrestasi = (int) $prestasis->sum('poin');

        return [
            'jumlahPelanggaran' => $pelanggarans->count(),
            'jumlahPelanggaranTerverifikasi' => $terverifikasi->count(),
            'jumlahPrestasi' => $prestasis->count(),
            'poinPelanggaran' => $poinPelanggaran,
            'poinPrestasi' => $poinPrestasi,
            'saldoPoin' => 100 - $poinPelanggaran + $poinPrestasi,
        ];
    }

    /**
     * Riwayat gabungan pelanggaran + prestasi, terbaru di atas.
     *
     * @return array<int, array<string, mixed>>
     */
    private function riwayat(Siswa $siswa, ?Carbon $sejak = null): array
    {
        $pelanggaranQuery = Pelanggaran::query()
            ->where('siswa_id', $siswa->id)
            ->with('aturanPelanggaran');

        $prestasiQuery = Prestasi::query()
            ->where('siswa_id', $siswa->id);

        if ($sejak) {
            $pelanggaranQuery->where('updated_at', '>', $sejak);
            $prestasiQuery->where('updated_at', '>', $sejak);
        }

        $items = collect();

        foreach ($pelanggaranQuery->get() as $pelanggaran) {
            $items->push([
                'tipe' => 'pelanggaran',
                'id' => $pelanggaran->id,
                'tanggal' => $pelanggaran->tanggal?->toDateString(),
                'tanggal_label' => $pelanggaran->tanggal?->translatedFormat('d M Y'),
                'judul' => $pelanggaran->aturanPelanggaran?->nama
                    ?? $pelanggaran->jenis_pelanggaran
                    ?? 'Pelanggaran',
                'keterangan' => $pelanggaran->kategori ?: 'Pelanggaran',
                'status' => $pelanggaran->status,
                'poin' => (int) $pelanggaran->poin,
                'diperbarui' => $pelanggaran->updated_at?->toIso8601String(),
            ]);
        }

        foreach ($prestasiQuery->get() as $prestasi) {
            $items->push([
                'tipe' => 'prestasi',
                'id' => $prestasi->id,
                'tanggal' => $prestasi->tanggal?->toDateString(),
                'tanggal_label' => $prestasi->tanggal?->translatedFormat('d M Y'),
                'judul' => $prestasi->jenis_prestasi,
                'keterangan' => $prestasi->keterangan ?: ($prestasi->tingkat ?: 'Prestasi'),
                'status' => null,
                'poin' => (int) $prestasi->poin,
                'diperbarui' => $prestasi->updated_at?->toIso8601String(),
            ]);
        }

        return $items
            ->sortBy([
                fn ($a, $b) => ($b['tanggal'] ?? '') <=> ($a['tanggal'] ?? ''),
                fn ($a, $b) => ($b['diperbarui'] ?? '') <=> ($a['diperbarui'] ?? ''),
            ])
            ->take(50)
            ->values()
            ->all();
    }

    private function parseSince(?string $since): ?Carbon
    {
        if (! $since) {
            return null;
        }

        try {
            return Carbon::parse($since);
        } catch (\Throwable) {
            return null;
        }
    }
}
