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

        // Jika walas, hanya tampilkan siswa di kelasnya
        if (auth()->user()->isWalas() && auth()->user()->kelas) {
            $query->where('kelas', auth()->user()->kelas);
        }

        $siswas = $query->get();

        $laporans = Pelanggaran::with(['siswa', 'verifikator'])
            ->where('pelapor_id', auth()->id())
            ->latest()
            ->get();

        $stats = [
            'total' => $laporans->count(),
            'pending' => $laporans->where('status', 'pending')->count(),
            'verified' => $laporans->where('status', 'diverifikasi')->count(),
            'rejected' => $laporans->where('status', 'ditolak')->count(),
        ];

        return view('lapor.index', compact('siswas', 'laporans', 'stats'));
    }

    /**
     * Halaman Riwayat Laporan
     */
    public function riwayat()
    {
        $laporans = Pelanggaran::with(['siswa', 'verifikator'])
            ->where('pelapor_id', auth()->id())
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

        Pelanggaran::create([
            'siswa_id' => $request->siswa_id,
            'pelapor_id' => auth()->id(),
            'tanggal' => $request->tanggal,
            'jenis_pelanggaran' => $request->jenis_pelanggaran,
            'keterangan' => $request->keterangan,
            'foto_bukti' => $foto,
            'status' => 'pending',
            'poin' => 0,
        ]);

        return redirect()
            ->route('lapor.index')
            ->with('success', 'Laporan pelanggaran berhasil dikirim. Menunggu verifikasi BK / PDS.');
    }
}
