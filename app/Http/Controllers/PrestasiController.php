<?php

namespace App\Http\Controllers;

use App\Models\Prestasi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PrestasiController extends Controller
{
    /**
     * Daftar semua prestasi.
     */
    public function index()
    {
        $query = Prestasi::with('siswa')
            ->latest('tanggal')
            ->latest('id');

        // Jika walas, hanya tampilkan prestasi siswa di kelasnya
        if (auth()->user()->isWalas() && auth()->user()->kelas) {
            $query->whereHas('siswa', function ($q) {
                $q->where('kelas', auth()->user()->kelas);
            });
        }

        $prestasis = $query->get();

        return view('prestasi.index', compact('prestasis'));
    }


    /**
     * Form tambah prestasi.
     */
    public function create()
    {
        $siswas = Siswa::orderBy('nama')->get();

        return view('prestasi.create', compact('siswas'));
    }


    /**
     * Simpan prestasi.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => [
                'required',
                'exists:siswas,id',
            ],

            'tanggal' => [
                'required',
                'date',
            ],

            'jenis_prestasi' => [
                'required',
                'string',
                'max:255',
            ],

            'tingkat' => [
                'nullable',
                'string',
                'max:100',
            ],

            'poin' => [
                'required',
                'integer',
                'min:1',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],

            'bukti' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);


        DB::transaction(function () use ($request, $validated) {

            /*
             * Lock data siswa agar perhitungan
             * poin aman ketika ada transaksi bersamaan.
             */
            $siswa = Siswa::where('id', $validated['siswa_id'])
                ->lockForUpdate()
                ->firstOrFail();


            /*
             * Hitung total pelanggaran siswa.
             */
            $totalPelanggaran = $siswa
                ->pelanggarans()
                ->sum('poin');


            /*
             * Hitung total prestasi siswa.
             */
            $totalPrestasi = $siswa
                ->prestasis()
                ->sum('poin');


            /*
             * Rumus saldo:
             *
             * Poin awal
             * - total pelanggaran
             * + total prestasi
             */
            $poinSebelum =
                100
                - $totalPelanggaran
                + $totalPrestasi;


            /*
             * Poin setelah mendapatkan prestasi.
             */
            $poinSesudah =
                $poinSebelum
                + $validated['poin'];


            /*
             * Upload bukti prestasi.
             */
            $bukti = null;

            if ($request->hasFile('bukti')) {
                $bukti = $request
                    ->file('bukti')
                    ->store('prestasi', 'public');
            }


            /*
             * Simpan prestasi.
             */
            Prestasi::create([
                'siswa_id' => $validated['siswa_id'],
                'tanggal' => $validated['tanggal'],
                'jenis_prestasi' => $validated['jenis_prestasi'],
                'tingkat' => $validated['tingkat'] ?? null,
                'poin' => $validated['poin'],

                'poin_sebelum' => $poinSebelum,
                'poin_sesudah' => $poinSesudah,

                'keterangan' => $validated['keterangan'] ?? null,
                'bukti' => $bukti,
            ]);
        });


        return redirect()
            ->route('prestasi.index')
            ->with(
                'success',
                'Prestasi berhasil ditambahkan dan poin siswa bertambah.'
            );
    }


    /**
     * Detail prestasi.
     */
    public function show(Prestasi $prestasi)
    {
        $prestasi->load('siswa');

        return view(
            'prestasi.show',
            compact('prestasi')
        );
    }


    /**
     * Form edit prestasi.
     */
    public function edit(Prestasi $prestasi)
    {
        $siswas = Siswa::orderBy('nama')->get();

        return view(
            'prestasi.edit',
            compact(
                'prestasi',
                'siswas'
            )
        );
    }


    /**
     * Update prestasi.
     *
     * Poin tidak diubah ketika edit.
     * Hal ini dilakukan agar riwayat poin
     * tetap konsisten.
     */
    public function update(
        Request $request,
        Prestasi $prestasi
    ) {
        $validated = $request->validate([
            'siswa_id' => [
                'required',
                'exists:siswas,id',
            ],

            'tanggal' => [
                'required',
                'date',
            ],

            'jenis_prestasi' => [
                'required',
                'string',
                'max:255',
            ],

            'tingkat' => [
                'nullable',
                'string',
                'max:100',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],
        ]);


        /*
         * Jika siswa diubah, kita tetap memperbarui
         * data prestasi tanpa mengubah nilai poin.
         */
        $prestasi->update([
            'siswa_id' => $validated['siswa_id'],
            'tanggal' => $validated['tanggal'],
            'jenis_prestasi' => $validated['jenis_prestasi'],
            'tingkat' => $validated['tingkat'] ?? null,
            'keterangan' => $validated['keterangan'] ?? null,
        ]);


        return redirect()
            ->route('prestasi.index')
            ->with(
                'success',
                'Data prestasi berhasil diperbarui.'
            );
    }


    /**
     * Hapus prestasi.
     */
    public function destroy(Prestasi $prestasi)
    {
        /*
         * Hapus file bukti jika ada.
         */
        if ($prestasi->bukti) {
            Storage::disk('public')
                ->delete($prestasi->bukti);
        }


        /*
         * Hapus data prestasi.
         */
        $prestasi->delete();


        return redirect()
            ->route('prestasi.index')
            ->with(
                'success',
                'Prestasi berhasil dihapus.'
            );
    }


    /**
     * Leaderboard.
     */
    public function leaderboard(Request $request)
    {
        $search = $request->query('search');
        $kelasFilter = $request->query('kelas');

        // Helper filter query untuk Siswa
        $applyFilter = function ($query) use ($search, $kelasFilter) {
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('nisn', 'like', "%{$search}%")
                      ->orWhere('kelas', 'like', "%{$search}%");
                });
            }

            if (!empty($kelasFilter)) {
                $k = trim($kelasFilter);
                $kUpper = strtoupper($k);

                if (in_array($kUpper, ['X', 'XI', 'XII', '10', '11', '12'])) {
                    if ($kUpper === 'XII' || $kUpper === '12') {
                        $query->where(function ($q) {
                            $q->where('kelas', 'like', 'XII %')
                              ->orWhere('kelas', 'like', 'XII-%')
                              ->orWhere('kelas', '=', 'XII')
                              ->orWhere('kelas', 'like', '12 %')
                              ->orWhere('kelas', 'like', '12-%')
                              ->orWhere('kelas', '=', '12');
                        });
                    } elseif ($kUpper === 'XI' || $kUpper === '11') {
                        $query->where(function ($q) {
                            $q->where(function ($sub) {
                                $sub->where('kelas', 'like', 'XI %')
                                    ->orWhere('kelas', 'like', 'XI-%')
                                    ->orWhere('kelas', '=', 'XI')
                                    ->orWhere('kelas', 'like', '11 %')
                                    ->orWhere('kelas', 'like', '11-%')
                                    ->orWhere('kelas', '=', '11');
                            })
                            ->where('kelas', 'not like', 'XII %')
                            ->where('kelas', 'not like', 'XII-%');
                        });
                    } elseif ($kUpper === 'X' || $kUpper === '10') {
                        $query->where(function ($q) {
                            $q->where(function ($sub) {
                                $sub->where('kelas', 'like', 'X %')
                                    ->orWhere('kelas', 'like', 'X-%')
                                    ->orWhere('kelas', '=', 'X')
                                    ->orWhere('kelas', 'like', '10 %')
                                    ->orWhere('kelas', 'like', '10-%')
                                    ->orWhere('kelas', '=', '10');
                            })
                            ->where('kelas', 'not like', 'XI %')
                            ->where('kelas', 'not like', 'XI-%')
                            ->where('kelas', 'not like', 'XII %')
                            ->where('kelas', 'not like', 'XII-%');
                        });
                    }
                } else {
                    $query->where('kelas', $k);
                }
            }
        };

        // ==========================================
        // TOP PRESTASI
        // ==========================================

        $topPrestasiQuery = Siswa::query()
            ->withCount('prestasis')
            ->withSum('prestasis', 'poin')
            ->having('prestasis_count', '>', 0);

        $applyFilter($topPrestasiQuery);

        $topPrestasi = $topPrestasiQuery
            ->orderByDesc('prestasis_count')
            ->orderByDesc('prestasis_sum_poin')
            ->limit(50)
            ->get();


        // ==========================================
        // TOP PELANGGARAN
        // ==========================================

        $topPelanggaranQuery = Siswa::query()
            ->withCount('pelanggarans')
            ->withSum('pelanggarans', 'poin')
            ->having('pelanggarans_count', '>', 0);

        $applyFilter($topPelanggaranQuery);

        $topPelanggaran = $topPelanggaranQuery
            ->orderByDesc('pelanggarans_count')
            ->orderByDesc('pelanggarans_sum_poin')
            ->limit(50)
            ->get();


        // ==========================================
        // SALDO POIN SISWA
        // ==========================================

        $saldoSiswaQuery = Siswa::query()
            ->withSum('pelanggarans', 'poin')
            ->withSum('prestasis', 'poin');

        $applyFilter($saldoSiswaQuery);

        $saldoSiswa = $saldoSiswaQuery
            ->get()
            ->map(function ($siswa) {
                $totalPelanggaran = (int) ($siswa->pelanggarans_sum_poin ?? 0);
                $totalPrestasi = (int) ($siswa->prestasis_sum_poin ?? 0);

                /*
                 * Rumus saldo poin:
                 * 100 - pelanggaran + prestasi
                 */
                $siswa->saldo_poin = 100 - $totalPelanggaran + $totalPrestasi;

                return $siswa;
            })
            ->sortByDesc('saldo_poin')
            ->values();


        // ==========================================
        // DAFTAR KELAS
        // ==========================================

        $kelasList = Siswa::whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');


        // ==========================================
        // TAMPILKAN VIEW
        // ==========================================

        return view(
            'prestasi.leaderboard',
            compact(
                'topPrestasi',
                'topPelanggaran',
                'saldoSiswa',
                'kelasList'
            )
        );
    }
}
