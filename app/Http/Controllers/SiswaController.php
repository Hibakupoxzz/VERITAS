<?php

namespace App\Http\Controllers;

use App\Exports\SiswaTemplateExport;
use App\Imports\SiswaImport;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $siswas = Siswa::when($search, function ($query) use ($search) {
            $query->where('nama', 'like', "%{$search}%")
                ->orWhere('nisn', 'like', "%{$search}%")
                ->orWhere('kelas', 'like', "%{$search}%");
        })->latest()->get();

        return view('siswa.index', compact('siswas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('siswa.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string|max:20|unique:siswas,nisn',
            'nama' => 'required|string|max:255',
            'kelas' => 'required|string|max:100',
        ]);

        Siswa::create([
            'nisn' => $request->nisn,
            'nama' => $request->nama,
            'kelas' => $request->kelas,
        ]);

        return redirect()
            ->route('siswa.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $siswa = Siswa::findOrFail($id);

        return view('siswa.show', compact('siswa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $siswa = Siswa::findOrFail($id);

        return view('siswa.edit', compact('siswa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $siswa = Siswa::findOrFail($id);

        $request->validate([
            'nisn' => 'required|string|max:20|unique:siswas,nisn,'.$id,
            'nama' => 'required|string|max:255',
            'kelas' => 'required|string|max:100',
        ]);

        $siswa->update([
            'nisn' => $request->nisn,
            'nama' => $request->nama,
            'kelas' => $request->kelas,
        ]);

        return redirect()
            ->route('siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $siswa = Siswa::findOrFail($id);

        $siswa->delete();

        return redirect()
            ->route('siswa.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }

    /**
     * Search siswa (AJAX)
     */
    public function search(Request $request)
    {
        $keyword = $request->q;

        return Siswa::where('nama', 'like', "%{$keyword}%")
            ->orWhere('nisn', 'like', "%{$keyword}%")
            ->limit(10)
            ->get();
    }

    /**
     * Download template Excel untuk import data siswa.
     */
    public function downloadTemplate()
    {
        return Excel::download(
            new SiswaTemplateExport,
            'template-import-siswa.xlsx'
        );
    }

    /**
     * Import data siswa dari file Excel / CSV.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file.required' => 'Silakan pilih file Excel terlebih dahulu.',
            'file.mimes' => 'Format file harus berupa .xlsx, .xls, atau .csv.',
            'file.max' => 'Ukuran file maksimal adalah 5MB.',
        ]);

        try {
            $import = new SiswaImport;
            Excel::import($import, $request->file('file'));

            $created = $import->getCreatedCount();
            $updated = $import->getUpdatedCount();
            $total = $import->getTotalSuccess();
            $failures = $import->getFailures();

            $message = "Berhasil memproses {$total} data siswa ({$created} data baru ditambahkan, {$updated} data diperbarui).";

            if (count($failures) > 0) {
                return redirect()
                    ->route('siswa.index')
                    ->with('success', $message)
                    ->with('import_errors', $failures);
            }

            return redirect()
                ->route('siswa.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()
                ->route('siswa.index')
                ->with('error', 'Gagal memproses file Excel: '.$e->getMessage());
        }
    }
}
