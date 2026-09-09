<?php

namespace App\Http\Controllers;

use App\Models\Pelanggaran;
use App\Models\Siswa;
use App\Models\AturanPelanggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PelanggaranHarianExport;
use App\Exports\PelanggaranMingguanExport;

class PelanggaranController extends Controller
{
    /**
     * Menampilkan daftar pelanggaran
     */
    public function index()
    {
        $pelanggarans = Pelanggaran::with([
            'siswa',
            'aturanPelanggaran'
        ])
            ->latest()
            ->get();

        return view('pelanggaran.index', compact('pelanggarans'));
    }

    /**
     * Menampilkan form tambah pelanggaran
     */
    public function create()
    {
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

        return DB::transaction(function () use ($request) {

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

                if (!$request->jenis_pelanggaran_custom) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'jenis_pelanggaran_custom' =>
                                'Nama pelanggaran lainnya wajib diisi.'
                        ]);
                }

                if ($request->poin_custom === null) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'poin_custom' =>
                                'Poin pelanggaran lainnya wajib diisi.'
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

            $pelanggaranTerakhir = Pelanggaran::where(
                'siswa_id',
                $request->siswa_id
            )
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
            'aturanPelanggaran'
        ])->findOrFail($id);

        return view('pelanggaran.show', compact('pelanggaran'));
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
            }
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
}
