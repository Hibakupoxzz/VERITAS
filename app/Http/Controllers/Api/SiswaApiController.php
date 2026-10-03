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
     *
     * Query params (opsional):
     *   ?search=andi  -> cari pada nisn / nama / kelas
     *   ?kelas=XII-A  -> filter kelas
     *   ?per_page=10  -> jumlah data per halaman
     *   ?page=2      -> halaman
     *   ?all=1       -> ambil semua data (tanpa pagination)
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:100',
            'kelas' => 'nullable|string|max:100',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'all' => 'nullable|boolean',
        ]);

        $siswas = Siswa::query()
            ->when($validated['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%")
                        ->orWhere('kelas', 'like', "%{$search}%");
                });
            })
            ->when($validated['kelas'] ?? null, function ($query, $kelas) {
                $query->where('kelas', $kelas);
            })
            ->orderBy('nama')
            ->get();

        // Kembalikan semua data sekaligus bila ?all=1
        if (! empty($validated['all'])) {
            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengambil data siswa.',
                'total' => $siswas->count(),
                'data' => $siswas,
            ]);
        }

        $perPage = (int) ($validated['per_page'] ?? 10);
        $page = (int) ($validated['page'] ?? 1);

        $paginator = $siswas->forPage($page, $perPage)->values();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data siswa.',
            'data' => $paginator,
            'meta' => [
                'total' => $siswas->count(),
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => max(1, (int) ceil($siswas->count() / $perPage)),
            ],
        ]);
    }

    /**
     * GET /api/siswa/{id}
     * Detail siswa beserta riwayat pelanggaran & prestasi.
     */
    public function show(string $id)
    {
        $siswa = Siswa::with([
            'pelanggarans' => fn ($q) => $q->latest('tanggal')->limit(10),
            'prestasis' => fn ($q) => $q->latest('tanggal')->limit(10),
        ])->find($id);

        if (! $siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan.',
            ], 404);
        }

        $totalPoin = $siswa->pelanggarans()->sum('poin');

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil detail siswa.',
            'data' => array_merge($siswa->toArray(), [
                'total_poin_pelanggaran' => $totalPoin,
            ]),
        ]);
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
