<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Imports\SiswaImport;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class SiswaApiController extends Controller
{
    /**
     * GET /api/siswa
     */
    public function index()
    {
        return response()->json(
            Siswa::orderBy('nama')->get()
        );
    }

    /**
     * POST /api/siswa
     * Tambah 1 siswa
     */
    public function store(Request $request)
    {
        return response()->json([
            'controller' => 'SiswaApiController',
            'data' => $request->all(),
        ]);

        // $request->validate([
        //     'nisn' => 'required|unique:siswas,nisn',
        //     'nama' => 'required',
        //     'kelas' => 'required'
        // ]);

        // $siswa = Siswa::create([
        //     'nisn' => $request->nisn,
        //     'nama' => $request->nama,
        //     'kelas' => $request->kelas,
        // ]);

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Siswa berhasil ditambahkan',
        //     'data' => $siswa
        // ], 201);

    }

    /**
     * POST /api/siswa/import
     * Import banyak siswa sekaligus (Mendukung File Excel/CSV atau Payload JSON)
     */
    public function bulkStore(Request $request)
    {
        // 1. Jika request mengirimkan file Excel / CSV
        if ($request->hasFile('file')) {
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            ], [
                'file.required' => 'File Excel wajib diunggah.',
                'file.mimes' => 'Format file harus berupa .xlsx, .xls, atau .csv.',
                'file.max' => 'Ukuran file maksimal adalah 5MB.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi file gagal',
                    'errors' => $validator->errors(),
                ], 422);
            }

            try {
                $import = new SiswaImport;
                Excel::import($import, $request->file('file'));

                return response()->json([
                    'success' => true,
                    'message' => 'Data siswa berhasil diimport dari file Excel',
                    'data' => [
                        'total' => $import->getTotalSuccess(),
                        'created' => $import->getCreatedCount(),
                        'updated' => $import->getUpdatedCount(),
                        'failures' => $import->getFailures(),
                    ],
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memproses file Excel: '.$e->getMessage(),
                ], 500);
            }
        }

        // 2. Jika request mengirimkan array JSON
        $items = $request->json()->all() ?: $request->all();

        if (empty($items) || ! is_array($items)) {
            return response()->json([
                'success' => false,
                'message' => 'Data payload kosong atau format tidak sesuai. Kirim file Excel atau array JSON data siswa.',
            ], 422);
        }

        $created = 0;
        $updated = 0;
        $errors = [];

        foreach ($items as $idx => $item) {
            $rowNum = $idx + 1;
            if (! is_array($item)) {
                $errors[] = "Item {$rowNum}: Format data harus berupa objek JSON.";

                continue;
            }

            if (empty($item['nisn']) || empty($item['nama']) || empty($item['kelas'])) {
                $errors[] = "Item {$rowNum}: Kolom nisn, nama, dan kelas wajib diisi.";

                continue;
            }

            $nisnClean = preg_replace('/[^0-9]/', '', (string) $item['nisn']);
            $namaClean = trim(preg_replace('/\s+/', ' ', (string) $item['nama']));
            $kelasClean = strtoupper(trim(preg_replace('/\s+/', ' ', (string) $item['kelas'])));

            $existing = Siswa::where('nisn', $nisnClean)->first();
            if ($existing) {
                $existing->update([
                    'nama' => $namaClean,
                    'kelas' => $kelasClean,
                ]);
                $updated++;
            } else {
                Siswa::create([
                    'nisn' => $nisnClean,
                    'nama' => $namaClean,
                    'kelas' => $kelasClean,
                ]);
                $created++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Data siswa berhasil diproses ({$created} baru, {$updated} diperbarui).",
            'data' => [
                'total' => $created + $updated,
                'created' => $created,
                'updated' => $updated,
                'errors' => $errors,
            ],
        ]);
    }
}
