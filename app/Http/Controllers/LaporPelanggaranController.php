<?php

namespace App\Http\Controllers;

use App\Models\AturanPelanggaran;
use App\Models\Pelanggaran;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;

class LaporPelanggaranController extends Controller
{
    /**
     * Halaman utama Lapor Pelanggaran
     */
    public function index()
    {
        /** @var User $user */
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Daftar siswa
        |--------------------------------------------------------------------------
        | Jika user adalah Wali Kelas dan memiliki kelas,
        | hanya siswa dari kelas tersebut yang ditampilkan.
        | Guru BK / Guru Khusus / PDS / Admin melihat seluruh siswa.
        */

        $query = Siswa::orderBy('nama');

        if ($user && $user->isWalas() && $user->kelas) {
            $query->where('kelas', $user->kelas);

            $kelasList = collect([$user->kelas]);
        } else {
            $kelasList = Siswa::select('kelas')
                ->whereNotNull('kelas')
                ->distinct()
                ->orderBy('kelas')
                ->pluck('kelas');
        }

        $siswas = $query->get();

        /*
        |--------------------------------------------------------------------------
        | Siswa yang dipilih sebelumnya
        |--------------------------------------------------------------------------
        | Digunakan ketika siswa melapor dari halaman daftar siswa
        | (route: lapor.index?siswa_id=...)
        */

        $selectedSiswaId = request('siswa_id');

        /*
        |--------------------------------------------------------------------------
        | Daftar aturan pelanggaran
        |--------------------------------------------------------------------------
        | Hanya aturan yang aktif yang dapat dipilih.
        |
        | Urutan:
        | Ringan
        | Sedang
        | Berat
        | Luar Biasa
        */

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

        /*
        |--------------------------------------------------------------------------
        | Riwayat laporan milik pelapor
        |--------------------------------------------------------------------------
        */

        $laporans = Pelanggaran::with([
            'siswa',
            'verifikator',
        ])
            ->where('pelapor_id', $user->id)
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Statistik
        |--------------------------------------------------------------------------
        */

        $stats = [
            'total' => $laporans->count(),

            'pending' => $laporans
                ->where('status', 'pending')
                ->count(),

            'verified' => $laporans
                ->where('status', 'diverifikasi')
                ->count(),

            'rejected' => $laporans
                ->where('status', 'ditolak')
                ->count(),
        ];

        return view('lapor.index', compact(
            'siswas',
            'kelasList',
            'selectedSiswaId',
            'aturanPelanggarans',
            'laporans',
            'stats'
        ));
    }

    /**
     * Halaman riwayat laporan
     */
    public function riwayat()
    {
        /** @var User $user */
        $user = auth()->user();

        $laporans = Pelanggaran::with([
            'siswa',
            'verifikator',
        ])
            ->where('pelapor_id', $user->id)
            ->latest()
            ->get();

        $stats = [
            'total' => $laporans->count(),

            'pending' => $laporans
                ->where('status', 'pending')
                ->count(),

            'verified' => $laporans
                ->where('status', 'diverifikasi')
                ->count(),

            'rejected' => $laporans
                ->where('status', 'ditolak')
                ->count(),
        ];

        return view('lapor.riwayat', compact(
            'laporans',
            'stats'
        ));
    }

    /**
     * Menyimpan laporan pelanggaran
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validasi dasar
        |--------------------------------------------------------------------------
        */

        $request->validate([
            // SISWA SEKARANG OPSIONAL
            'siswa_id' => [
                'nullable',
                'exists:siswas,id',
            ],

            'tanggal' => [
                'required',
                'date',
            ],

            // Pelaporan WAJIB mengacu pada master aturan.
            'aturan_pelanggaran_id' => [
                'required',
                'integer',
                'exists:aturan_pelanggarans,id',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],

            'foto_bukti' => [
                'nullable',
                'image',
                'max:5120',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Pastikan aturan yang dipilih masih aktif
        |--------------------------------------------------------------------------
        | Dicek manual (bukan findOrFail) supaya pengguna
        | mendapat pesan validasi yang ramah, bukan error 404.
        | Poin & kategori TIDAK diambil dari input pengguna,
        | selalu mengikuti master aturan.
        */

        $aturan = AturanPelanggaran::where('aktif', true)
            ->find($request->aturan_pelanggaran_id);

        if (! $aturan) {
            return back()
                ->withInput()
                ->withErrors([
                    'aturan_pelanggaran_id' => 'Aturan pelanggaran yang dipilih tidak tersedia atau sudah tidak aktif.',
                ]);
        }

        $jenisPelanggaran = $aturan->nama;
        $kategori = $aturan->kategori;
        $poin = $aturan->poin;

        /*
        |--------------------------------------------------------------------------
        | Upload foto
        |--------------------------------------------------------------------------
        */

        $foto = null;

        if ($request->hasFile('foto_bukti')) {
            $foto = $request
                ->file('foto_bukti')
                ->store('bukti', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | User yang sedang login
        |--------------------------------------------------------------------------
        */

        /** @var User $user */
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Simpan laporan
        |--------------------------------------------------------------------------
        */

        Pelanggaran::create([
            // Bisa NULL karena siswa sekarang opsional
            'siswa_id' => $request->siswa_id ?: null,

            'pelapor_id' => $user->id,

            'aturan_pelanggaran_id' => $aturan->id,

            'tanggal' => $request->tanggal,

            'jenis_pelanggaran' => $jenisPelanggaran,

            'keterangan' => $request->keterangan,

            'foto_bukti' => $foto,

            'status' => 'pending',

            'poin' => $poin,

            'kategori' => $kategori,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('lapor.index')
            ->with(
                'success',
                'Laporan pelanggaran berhasil dikirim. Menunggu verifikasi BK / PDS.'
            );
    }
}
