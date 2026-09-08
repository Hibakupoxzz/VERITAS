<?php

namespace App\Http\Controllers;

use App\Models\Pelanggaran;
use App\Models\Siswa;
use App\Models\AturanPelanggaran;
use Illuminate\Http\Request;
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

        $aturanPelanggarans = AturanPelanggaran::where('aktif', true)
            ->orderByRaw("
                CASE kategori
                    WHEN 'Ringan' THEN 1
                    WHEN 'Sedang' THEN 2
                    WHEN 'Berat' THEN 3
                    WHEN 'Luar Biasa' THEN 4
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
            'aturan_pelanggaran_id' => 'required|exists:aturan_pelanggarans,id',
            'keterangan' => 'nullable|string',
            'foto_bukti' => 'nullable|image|max:5120',
        ]);

        $aturan = AturanPelanggaran::where('id', $request->aturan_pelanggaran_id)
            ->where('aktif', true)
            ->firstOrFail();

        $foto = null;

        if ($request->hasFile('foto_bukti')) {
            $foto = $request->file('foto_bukti')
                ->store('bukti', 'public');
        }

        Pelanggaran::create([
            'siswa_id' => $request->siswa_id,
            'tanggal' => $request->tanggal,

            // Diambil dari master aturan
            'aturan_pelanggaran_id' => $aturan->id,
            'jenis_pelanggaran' => $aturan->nama,
            'poin' => $aturan->poin,

            'keterangan' => $request->keterangan,
            'foto_bukti' => $foto,
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

    public function exportHarian()
    {
        return Excel::download(
            new PelanggaranHarianExport,
            'pelanggaran-harian.xlsx'
        );
    }

    public function exportMingguan()
    {
        return Excel::download(
            new PelanggaranMingguanExport,
            'pelanggaran-mingguan.xlsx'
        );
    }
}
