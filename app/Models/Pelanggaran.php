<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggaran extends Model
{
    protected $fillable = [
        'siswa_id',
        'aturan_pelanggaran_id',
        'tahun_pelajaran_id',
        'tanggal',
        'jenis_pelanggaran',
        'kategori',
        'poin',
        'poin_sebelum',
        'poin_sesudah',
        'sanksi_tahap',
        'status_pembinaan',
        'dikembalikan_ke_orangtua',
        'keterangan',
        'restitusi',
        'klarifikasi',
        'foto_bukti',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'poin' => 'integer',
        'poin_sebelum' => 'integer',
        'poin_sesudah' => 'integer',
        'dikembalikan_ke_orangtua' => 'boolean',
        'restitusi' => 'boolean',
        'klarifikasi' => 'boolean',
    ];

    /**
     * Relasi ke siswa
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    /**
     * Relasi ke aturan/master pelanggaran
     */
    public function aturanPelanggaran()
    {
        return $this->belongsTo(
            AturanPelanggaran::class,
            'aturan_pelanggaran_id'
        );
    }
}
