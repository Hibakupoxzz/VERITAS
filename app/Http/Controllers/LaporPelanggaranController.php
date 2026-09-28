<?php

namespace App\Http\Controllers;

use App\Models\AturanPelanggaran;
use App\Models\Pelanggaran;
use App\Models\Siswa;
use Illuminate\Http\Request;

class LaporPelanggaranController extends Controller
{
    /**
     * Halaman utama Lapor Pelanggaran
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Daftar siswa
        |--------------------------------------------------------------------------
        | Jika user adalah Wali Kelas dan memiliki kelas,
        | hanya siswa dari kelas tersebut yang ditampilkan.
        */

        $query = Siswa::orderBy('nama');

        if ($user && $user->isWalas() && $user->kelas) {
            $query->where('kelas', $user->kelas);
        }

        $siswas = $query->get();

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
        | Riwayat laporan milik Wali Kelas
        |--------------------------------------------------------------------------
        */

        $laporans = Pelanggaran::with([
                'siswa',
                'verifikator'
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
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $laporans = Pelanggaran::with([
                'siswa',
                'verifikator'
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

            'pelanggaran_mode' => [
                'required',
                'in:aturan,manual',
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
        | Tentukan jenis pelanggaran
        |--------------------------------------------------------------------------
        */

        $jenisPelanggaran = null;
        $kategori = null;
        $poin = 0;


        /*
        |--------------------------------------------------------------------------
        | MODE ATURAN
        |--------------------------------------------------------------------------
        */

        if ($request->pelanggaran_mode === 'aturan') {

            $request->validate([
                'aturan_pelanggaran_id' => [
                    'required',
                    'exists:aturan_pelanggarans,id',
                ],
            ]);

            /*
            | Pastikan aturan yang dipilih masih aktif
            */

            $aturan = AturanPelanggaran::where('aktif', true)
                ->findOrFail($request->aturan_pelanggaran_id);

            $jenisPelanggaran = $aturan->nama;
            $kategori = $aturan->kategori;
            $poin = $aturan->poin;
        }


        /*
        |--------------------------------------------------------------------------
        | MODE MANUAL
        |--------------------------------------------------------------------------
        */

        if ($request->pelanggaran_mode === 'manual') {

            $request->validate([
                'jenis_pelanggaran_manual' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'kategori_manual' => [
                    'required',
                    'in:Ringan,Sedang,Berat,Luar Biasa',
                ],

                'poin_manual' => [
                    'required',
                    'integer',
                    'min:0',
                ],
            ]);

            $jenisPelanggaran =
                $request->jenis_pelanggaran_manual;

            $kategori =
                $request->kategori_manual;

            $poin =
                $request->poin_manual;
        }


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

        /** @var \App\Models\User $user */
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
