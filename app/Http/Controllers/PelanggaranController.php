<?php

namespace App\Http\Controllers;

use App\Models\Pelanggaran;
use App\Models\Siswa;
use Illuminate\Http\Request;

class PelanggaranController extends Controller
{
    /**
     * Menampilkan daftar pelanggaran
     */
    public function index()
    {
        $pelanggarans = Pelanggaran::with('siswa')
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

        return view('pelanggaran.create', compact('siswas'));
    }

    /**
     * Menyimpan data pelanggaran
     */
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required',
            'tanggal' => 'required',
            'jenis_pelanggaran' => 'required',
            'keterangan' => 'required',
            'foto_bukti' => 'nullable|image'
        ]);

        $foto = null;

        if ($request->hasFile('foto_bukti')) {
            $foto = $request->file('foto_bukti')
                ->store('bukti', 'public');
        }

    Pelanggaran::create([
        'siswa_id' => $request->siswa_id,
        'tanggal' => $request->tanggal,
        'jenis_pelanggaran' => $request->jenis_pelanggaran,
        'poin' => $request->poin,
        'keterangan' => $request->keterangan,
        'foto_bukti' => $foto
    ]);

        return redirect()
            ->route('pelanggaran.index')
            ->with('success', 'Data pelanggaran berhasil ditambahkan');
    }

    /**
     * Detail pelanggaran
     */
    public function show(string $id)
    {
        $pelanggaran = Pelanggaran::with('siswa')
            ->findOrFail($id);

        return view('pelanggaran.show', compact('pelanggaran'));
    }

    /**
     * Form edit
     */
    public function edit(string $id)
    {
        $pelanggaran = Pelanggaran::findOrFail($id);
        $siswas = Siswa::orderBy('nama')->get();

        return view(
            'pelanggaran.edit',
            compact('pelanggaran', 'siswas')
        );
    }

    /**
     * Update data
     */
    public function update(Request $request, string $id)
    {
        $pelanggaran = Pelanggaran::findOrFail($id);

        $request->validate([
            'siswa_id' => 'required',
            'tanggal' => 'required',
            'jenis_pelanggaran' => 'required',
            'poin' => 'required|numeric'
        ]);

        if ($request->hasFile('foto_bukti')) {
            $foto = $request->file('foto_bukti')
                ->store('bukti', 'public');

            $pelanggaran->foto_bukti = $foto;
        }

        $pelanggaran->update([
            'siswa_id' => $request->siswa_id,
            'tanggal' => $request->tanggal,
            'jenis_pelanggaran' => $request->jenis_pelanggaran,
            'poin' => $request->poin,
            'catatan' => $request->catatan,
            'foto_bukti' => $pelanggaran->foto_bukti
        ]);

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
        $rekap = Siswa::withSum('pelanggarans', 'poin')
            ->orderByDesc('pelanggarans_sum_poin')
            ->get();

        return view('pelanggaran.rekap', compact('rekap'));
    }
}
