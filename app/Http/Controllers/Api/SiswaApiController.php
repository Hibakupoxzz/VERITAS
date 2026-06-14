<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Http\Request;

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
            'data' => $request->all()
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
     * Import banyak siswa sekaligus
     */
    public function bulkStore(Request $request)
    {
        foreach ($request->all() as $item) {

            Siswa::updateOrCreate(
                [
                    'nisn' => $item['nisn']
                ],
                [
                    'nama' => $item['nama'],
                    'kelas' => $item['kelas']
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Data siswa berhasil diimport'
        ]);
    }
}
