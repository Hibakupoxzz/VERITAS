<?php

namespace App\Http\Controllers;

use App\Exports\PelanggaranHarianExport;
use App\Exports\PelanggaranMingguanExport;
use App\Models\AturanPelanggaran;
use App\Models\Pelanggaran;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PelanggaranController extends Controller
{
    /**
     * Menampilkan daftar pelanggaran
     */
    public function index()
    {
        /** @var User $user */
        $user = auth()->user();

        $query = Pelanggaran::with([
            'siswa',
            'pelapor',
            'aturanPelanggaran',
        ])
            ->verified()
            /*
             * Urut berdasarkan tanggal kejadian, bukan created_at.
             * Laporan yang baru diverifikasi tapi tanggal
             * kejadiannya sudah lama (mis. rapor) tetap tampil di
             * posisi yang sesuai tanggalnya.
             * id dipakai sebagai penentu agar tanggal yang sama
             * tetap punya urutan stabil (tidak lompat-lompat antar halaman).
             */
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        // Jika walas, hanya tampilkan pelanggaran siswa di kelasnya
        if ($user->isWalas() && $user->kelas) {
            $query->whereHas('siswa', function ($q) use ($user) {
                $q->where('kelas', $user->kelas);
            });
        }

        // PDS dan BK kini dapat melihat semua kelas (Global)

        $pelanggarans = $query->get();

        $aturanPelanggarans = AturanPelanggaran::where('aktif', true)
            ->orderByRaw("
                CASE kategori
                    WHEN 'Ringan' THEN 1
                    WHEN 'Sedang' THEN 2
                    WHEN 'Berat' THEN 3
                    WHEN 'Luar Biasa' THEN 4
                    ELSE 5
                END
            ")
            ->orderBy('kode')
            ->get();

        return view('pelanggaran.index', compact(
            'pelanggarans',
            'aturanPelanggarans'
        ));
    }

    /**
     * Menampilkan daftar pending laporan dari Walas
     */
    public function pending()
    {
        $pendingLaporans = Pelanggaran::with([
            'siswa',
            'pelapor',
            'aturanPelanggaran',
        ])
            ->pending()
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Konteks siswa untuk membantu proses verifikasi
        |--------------------------------------------------------------------------
        */

        [$ringkasanSiswa, $riwayatPerSiswa] = $this->konteksSiswa(
            $pendingLaporans->pluck('siswa_id')
        );

        $aturanPelanggarans = AturanPelanggaran::where('aktif', true)
            ->orderByRaw("
                CASE kategori
                    WHEN 'Ringan' THEN 1
                    WHEN 'Sedang' THEN 2
                    WHEN 'Berat' THEN 3
                    WHEN 'Luar Biasa' THEN 4
                    ELSE 5
                END
            ")
            ->orderBy('kode')
            ->get();

        return view('pelanggaran.pending', compact(
            'pendingLaporans',
            'aturanPelanggarans',
            'ringkasanSiswa',
            'riwayatPerSiswa',
        ));
    }

    /**
     * Konteks siswa untuk membantu proses verifikasi.
     *
     * Verifikator perlu melihat saldo poin dan riwayat teguran
     * sebelum menyetujui laporan. Semua data diambil lewat query
     * terpisah (bukan per-laporan) supaya tidak N+1.
     *
     * Mengembalikan tuple [ringkasan per siswa_id, riwayat per siswa_id].
     *
     * @param  Collection<int, mixed>  $siswaIds
     * @return array{0: Collection, 1: Collection}
     */
    private function konteksSiswa($siswaIds): array
    {
        $siswaIds = $siswaIds
            ->filter()
            ->unique()
            ->values();

        if ($siswaIds->isEmpty()) {
            return [collect(), collect()];
        }

        /*
         * Total pelanggaran terverifikasi, total prestasi,
         * lalu saldo poin memakai rumus yang sama dengan
         * DashboardController: 100 - pelanggaran + prestasi.
         */
        $ringkasan = Siswa::whereIn('id', $siswaIds)
            ->withCount([
                'pelanggarans as jumlah_pelanggaran' => fn ($q) => $q
                    ->where('status', 'diverifikasi'),
            ])
            ->withSum([
                'pelanggarans as total_poin_pelanggaran' => fn ($q) => $q
                    ->where('status', 'diverifikasi'),
            ], 'poin')
            ->withSum('prestasis as total_poin_prestasi', 'poin')
            ->get()
            ->each(function ($siswa) {
                $siswa->saldo_poin =
                    100
                    - (int) ($siswa->total_poin_pelanggaran ?? 0)
                    + (int) ($siswa->total_poin_prestasi ?? 0);
            })
            ->keyBy('id');

        /*
         * Riwayat teguran terakhir per siswa.
         * Dibatasi 90 hari supaya query tetap ringan
         * walau siswa sudah punya riwayat panjang.
         */
        $riwayat = Pelanggaran::where('status', 'diverifikasi')
            ->whereIn('siswa_id', $siswaIds)
            ->where('tanggal', '>=', now()->subDays(90)->startOfDay())
            ->orderByDesc('id')
            ->get([
                'siswa_id',
                'tanggal',
                'jenis_pelanggaran',
                'kategori',
                'poin',
            ])
            ->groupBy('siswa_id')
            ->map(fn ($rows) => $rows->take(5)->values());

        return [$ringkasan, $riwayat];
    }

    /**
     * Menampilkan form tambah pelanggaran
     */
    public function create()
    {
        /** @var User $user */
        $user = auth()->user();

        $query = Siswa::orderBy('nama');

        // PDS dan BK kini dapat melihat semua siswa (Global)

        $siswas = $query->get();

        $aturanPelanggarans = AturanPelanggaran::where('aktif', true)
            ->orderByRaw("
                CASE kategori
                    WHEN 'Ringan' THEN 1
                    WHEN 'Sedang' THEN 2
                    WHEN 'Berat' THEN 3
                    WHEN 'Luar Biasa' THEN 4
                    ELSE 5
                END
            ")
            ->orderBy('kode')
            ->get();

        return view(
            'pelanggaran.create',
            compact('siswas', 'aturanPelanggarans')
        );
    }

    /**
     * Menyimpan data pelanggaran
     */
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'tanggal' => 'required|date',

            // Boleh kosong
            'aturan_pelanggaran_id' => 'nullable',

            'jenis_pelanggaran_custom' => 'nullable|string|max:255',
            'poin_custom' => 'nullable|numeric|min:0',

            'keterangan' => 'nullable|string',

            'foto_bukti' => 'nullable|image|max:5120',
        ]);

        /** @var User $user */
        $user = auth()->user();

        return DB::transaction(function () use ($request, $user) {

            /*
            |--------------------------------------------------------------------------
            | Tentukan apakah menggunakan aturan resmi atau pelanggaran lainnya
            |--------------------------------------------------------------------------
            */

            $aturan = null;
            $namaPelanggaran = null;
            $poin = 0;
            $kategori = null;

            if (
                $request->aturan_pelanggaran_id &&
                $request->aturan_pelanggaran_id !== 'custom'
            ) {
                /*
                 * PELANGGARAN RESMI
                 *
                 * Poin TIDAK boleh diambil dari input user.
                 * Poin selalu berasal dari master 62 aturan.
                 */

                $aturan = AturanPelanggaran::where('id', $request->aturan_pelanggaran_id)
                    ->where('aktif', true)
                    ->firstOrFail();

                $namaPelanggaran = $aturan->nama;
                $poin = (int) $aturan->poin;
                $kategori = $aturan->kategori;
            } elseif ($request->aturan_pelanggaran_id === 'custom') {
                /*
                 * PELANGGARAN LAINNYA
                 *
                 * Nama dan poin boleh dimasukkan manual.
                 */

                if (! $request->jenis_pelanggaran_custom) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'jenis_pelanggaran_custom' => 'Nama pelanggaran lainnya wajib diisi.',
                        ]);
                }

                if ($request->poin_custom === null) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'poin_custom' => 'Poin pelanggaran lainnya wajib diisi.',
                        ]);
                }

                $namaPelanggaran = $request->jenis_pelanggaran_custom;
                $poin = (int) $request->poin_custom;

                // Kategori boleh kosong untuk pelanggaran custom
                $kategori = $request->kategori;
            }

            /*
            |--------------------------------------------------------------------------
            | Hitung saldo poin siswa
            |--------------------------------------------------------------------------
            |
            | Saldo awal setiap siswa = 100
            |
            | Contoh:
            | 100 - 5  = 95
            | 95  - 10 = 85
            |
            */

            $pelanggaranTerakhir = Pelanggaran::where('siswa_id', $request->siswa_id)
                ->where('status', 'diverifikasi')
                ->latest('id')
                ->first();

            $poinSebelum = $pelanggaranTerakhir
                ? (int) $pelanggaranTerakhir->poin_sesudah
                : 100;

            $poinSesudah = max(0, $poinSebelum - $poin);

            /*
            |--------------------------------------------------------------------------
            | Upload foto
            |--------------------------------------------------------------------------
            */

            $foto = null;

            if ($request->hasFile('foto_bukti')) {
                $foto = $request->file('foto_bukti')
                    ->store('bukti', 'public');
            }

            /*
            |--------------------------------------------------------------------------
            | Simpan
            |--------------------------------------------------------------------------
            */

            Pelanggaran::create([
                'siswa_id' => $request->siswa_id,
                'pelapor_id' => $user->id, // Ditambahkan oleh BK/PDS sendiri

                'aturan_pelanggaran_id' => $aturan?->id,

                'tanggal' => $request->tanggal,

                'jenis_pelanggaran' => $namaPelanggaran,

                'kategori' => $kategori,

                // Tetap simpan angka positif di database
                'poin' => $poin,

                // Saldo
                'poin_sebelum' => $poinSebelum,
                'poin_sesudah' => $poinSesudah,

                // Jika aturan resmi memiliki tahap, gunakan tahap I
                'sanksi_tahap' => $aturan ? 1 : null,

                'status' => 'diverifikasi',
                'diverifikasi_oleh' => $user->id, // Langsung diverifikasi oleh pembuat (BK/PDS)

                'keterangan' => $request->keterangan,

                'foto_bukti' => $foto,
            ]);

            return redirect()
                ->route('pelanggaran.index')
                ->with(
                    'success',
                    "Pelanggaran berhasil ditambahkan. Saldo siswa sekarang {$poinSesudah} poin."
                );
        });
    }

    /**
     * Detail pelanggaran
     */
    public function show(string $id)
    {
        $pelanggaran = Pelanggaran::with([
            'siswa',
            'pelapor',
            'verifikator',
            'aturanPelanggaran',
        ])->findOrFail($id);

        /*
        | Saldo poin & riwayat teguran membantu verifikator
        | dan guru BK menilai laporan sebelum memutuskan.
        */
        [$ringkasanSiswa, $riwayatPerSiswa] = $this->konteksSiswa(
            collect([$pelanggaran->siswa_id])
        );

        $ringkasan = $pelanggaran->siswa_id
            ? $ringkasanSiswa->get($pelanggaran->siswa_id)
            : null;

        $riwayat = $pelanggaran->siswa_id
            ? ($riwayatPerSiswa->get($pelanggaran->siswa_id) ?? collect())
            : collect();

        return view('pelanggaran.show', compact(
            'pelanggaran',
            'ringkasan',
            'riwayat',
        ));
    }

    /**
     * Form edit
     */
    public function edit(string $id)
    {
        $pelanggaran = Pelanggaran::with('aturanPelanggaran')
            ->findOrFail($id);

        $siswas = Siswa::orderBy('nama')->get();

        $aturanPelanggarans = AturanPelanggaran::where('aktif', true)
            ->orderByRaw("
                CASE kategori
                    WHEN 'Ringan' THEN 1
                    WHEN 'Sedang' THEN 2
                    WHEN 'Berat' THEN 3
                    WHEN 'Luar Biasa' THEN 4
                    ELSE 5
                END
            ")
            ->orderBy('kode')
            ->get();

        return view(
            'pelanggaran.edit',
            compact(
                'pelanggaran',
                'siswas',
                'aturanPelanggarans'
            )
        );
    }

    /**
     * Update data
     */
    public function update(Request $request, string $id)
    {
        $pelanggaran = Pelanggaran::findOrFail($id);

        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'tanggal' => 'required|date',
            'jenis_pelanggaran' => 'nullable|string',
            'poin' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'foto_bukti' => 'nullable|image|max:5120',
        ]);

        /*
         * Untuk sementara update hanya mengubah data dasar.
         *
         * Perhitungan ulang saldo/edit poin akan kita buat
         * setelah sistem apresiasi selesai supaya saldo tetap konsisten.
         */

        $data = [
            'siswa_id' => $request->siswa_id,
            'tanggal' => $request->tanggal,
            'jenis_pelanggaran' => $request->jenis_pelanggaran,
            'keterangan' => $request->keterangan,
        ];

        if ($request->hasFile('foto_bukti')) {
            $data['foto_bukti'] = $request->file('foto_bukti')
                ->store('bukti', 'public');
        }

        $pelanggaran->update($data);

        return redirect()
            ->route('pelanggaran.index')
            ->with('success', 'Data berhasil diperbarui');
    }

    /**
     * Hapus data
     */
    public function destroy(string $id)
    {
        $pelanggaran = Pelanggaran::findOrFail($id);

        $pelanggaran->delete();

        return redirect()
            ->route('pelanggaran.index')
            ->with('success', 'Data berhasil dihapus');
    }

    /**
     * Rekap poin siswa
     */
    public function rekap()
    {
        $rekap = Siswa::with([
            'pelanggarans' => function ($query) {
                $query->latest('id');
            },
        ])
            ->orderBy('nama')
            ->get();

        return view('pelanggaran.rekap', compact('rekap'));
    }

    /**
     * Export harian
     */
    public function exportHarian()
    {
        return Excel::download(
            new PelanggaranHarianExport,
            'pelanggaran-harian.xlsx'
        );
    }

    /**
     * Export mingguan
     */
    public function exportMingguan()
    {
        return Excel::download(
            new PelanggaranMingguanExport,
            'pelanggaran-mingguan.xlsx'
        );
    }

    /**
     * Approve (Verifikasi) laporan dari Walas.
     */
    public function approve(Request $request, string $id)
    {
        $pelanggaran = Pelanggaran::where('status', 'pending')
            ->findOrFail($id);

        $request->validate([
            /*
             * Aturan WAJIB dipilih: tanpa itu poin tidak ada sumbernya
             * dan laporan bisa terverifikasi dengan poin 0 tanpa warning.
             */
            'aturan_pelanggaran_id' => 'required',
            'jenis_pelanggaran_custom' => 'nullable|string|max:255',
            'poin_custom' => 'nullable|numeric|min:0',
            'kategori' => 'nullable|in:Ringan,Sedang,Berat,Luar Biasa',
            'catatan_verifikasi' => 'nullable|string',
        ]);

        /** @var User $user */
        $user = auth()->user();

        return DB::transaction(function () use ($request, $pelanggaran, $user) {

            $aturan = null;
            $namaPelanggaran = $pelanggaran->jenis_pelanggaran;
            $poin = 0;
            $kategori = null;

            if (
                $request->aturan_pelanggaran_id &&
                $request->aturan_pelanggaran_id !== 'custom'
            ) {
                $aturan = AturanPelanggaran::where('id', $request->aturan_pelanggaran_id)
                    ->where('aktif', true)
                    ->firstOrFail();

                $namaPelanggaran = $aturan->nama;
                $poin = (int) $aturan->poin;
                $kategori = $aturan->kategori;
            } elseif ($request->aturan_pelanggaran_id === 'custom') {
                if (! $request->jenis_pelanggaran_custom) {
                    return back()->withErrors([
                        'jenis_pelanggaran_custom' => 'Nama pelanggaran wajib diisi.',
                    ]);
                }
                if ($request->poin_custom === null) {
                    return back()->withErrors([
                        'poin_custom' => 'Poin wajib diisi.',
                    ]);
                }
                $namaPelanggaran = $request->jenis_pelanggaran_custom;
                $poin = (int) $request->poin_custom;
                $kategori = $request->kategori;
            }

            /*
            | Hitung saldo poin siswa.
            |
            | Laporan bisa dibuat tanpa siswa (laporan umum kelas),
            | jadi saldo poin hanya dihitung jika siswanya ada.
            */

            if ($pelanggaran->siswa_id) {

                $pelanggaranTerakhir = Pelanggaran::where('siswa_id', $pelanggaran->siswa_id)
                    ->where('status', 'diverifikasi')
                    ->latest('id')
                    ->first();

                $poinSebelum = $pelanggaranTerakhir
                    ? (int) $pelanggaranTerakhir->poin_sesudah
                    : 100;

                $poinSesudah = max(0, $poinSebelum - $poin);

            } else {

                $poinSebelum = null;
                $poinSesudah = null;
            }

            $pelanggaran->update([
                'aturan_pelanggaran_id' => $aturan?->id,
                'jenis_pelanggaran' => $namaPelanggaran,
                'kategori' => $kategori,
                'poin' => $poin,
                'poin_sebelum' => $poinSebelum,
                'poin_sesudah' => $poinSesudah,
                'sanksi_tahap' => $aturan ? 1 : null,
                'status' => 'diverifikasi',
                'diverifikasi_oleh' => $user->id,
                'catatan_verifikasi' => $request->catatan_verifikasi,
            ]);

            return redirect()
                ->route('pelanggaran.index')
                ->with(
                    'success',
                    $poinSesudah === null
                        ? 'Laporan umum diverifikasi. Saldo poin tidak diperbarui karena laporan tidak ditempelkan ke siswa tertentu.'
                        : "Laporan diverifikasi. Saldo poin siswa sekarang {$poinSesudah}."
                );
        });
    }

    /**
     * Tolak laporan dari Walas.
     */
    public function reject(Request $request, string $id)
    {
        $pelanggaran = Pelanggaran::where('status', 'pending')
            ->findOrFail($id);

        $request->validate([
            'catatan_verifikasi' => 'required|string|max:500',
        ]);

        /** @var User $user */
        $user = auth()->user();

        $pelanggaran->update([
            'status' => 'ditolak',
            'diverifikasi_oleh' => $user->id,
            'catatan_verifikasi' => $request->catatan_verifikasi,
        ]);

        return redirect()
            ->route('pelanggaran.index')
            ->with('success', 'Laporan telah ditolak.');
    }
}
