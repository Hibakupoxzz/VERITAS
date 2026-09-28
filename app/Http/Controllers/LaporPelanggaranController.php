<?php

namespace App\Http\Controllers;

use App\Models\Pelanggaran;
use App\Models\Siswa;
use Illuminate\Http\Request;

class LaporPelanggaranController extends Controller
{
    /**
     * Halaman lapor pelanggaran & riwayat (Guru Walas).
     */
    public function index()
    {
        $query = Siswa::orderBy('nama');

        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Jika walas, hanya tampilkan siswa di kelasnya
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
        $selectedSiswaId = request('siswa_id');

        $laporans = Pelanggaran::with(['siswa', 'verifikator'])
            ->where('pelapor_id', $user->id)
            ->latest()
            ->get();

        $stats = [
            'total' => $laporans->count(),
            'pending' => $laporans->where('status', 'pending')->count(),
            'verified' => $laporans->where('status', 'diverifikasi')->count(),
            'rejected' => $laporans->where('status', 'ditolak')->count(),
        ];

        return view('lapor.index', compact('siswas', 'laporans', 'stats', 'kelasList', 'selectedSiswaId'));
    }

    /**
     * Halaman Riwayat Laporan
     */
    public function riwayat()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $laporans = Pelanggaran::with(['siswa', 'verifikator'])
            ->where('pelapor_id', $user->id)
            ->latest()
            ->get();

        $stats = [
            'total' => $laporans->count(),
            'pending' => $laporans->where('status', 'pending')->count(),
            'verified' => $laporans->where('status', 'diverifikasi')->count(),
            'rejected' => $laporans->where('status', 'ditolak')->count(),
        ];

        return view('lapor.riwayat', compact('laporans', 'stats'));
    }

    /**
     * Simpan laporan pelanggaran (status: pending).
     */
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'tanggal' => 'required|date',
            'jenis_pelanggaran' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'foto_bukti' => 'nullable|image|max:5120',
        ]);

        $foto = null;

        if ($request->hasFile('foto_bukti')) {
            $foto = $request->file('foto_bukti')
                ->store('bukti', 'public');
        }

        /** @var \App\Models\User $user */
        $user = auth()->user();

        Pelanggaran::create([
            'siswa_id' => $request->siswa_id,
            'pelapor_id' => $user->id,
            'tanggal' => $request->tanggal,
            'jenis_pelanggaran' => $request->jenis_pelanggaran,
            'keterangan' => $request->keterangan,
            'foto_bukti' => $foto,
            'status' => 'pending',
            'poin' => 0,
        ]);

        return redirect()
            ->route('lapor.index')
            ->with('success', 'Laporan pelanggaran berhasil dikirim. Menunggu verifikasi PDS.');
    }
}
