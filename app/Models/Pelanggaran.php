<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggaran extends Model
{
    protected $fillable = [
        'siswa_id',
        'pelapor_id',
        'aturan_pelanggaran_id',
        'tahun_pelajaran_id',
        'tanggal',
        'jenis_pelanggaran',
        'kategori',
        'status',
        'diverifikasi_oleh',
        'catatan_verifikasi',
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

    /*
    |--------------------------------------------------------------------------
    | Relasi
    |--------------------------------------------------------------------------
    */

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function aturanPelanggaran()
    {
        return $this->belongsTo(
            AturanPelanggaran::class,
            'aturan_pelanggaran_id'
        );
    }

    /**
     * Guru yang melaporkan (Walas).
     */
    public function pelapor()
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }

    /**
     * Guru BK/PDS yang memverifikasi.
     */
    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'diverifikasi');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'ditolak');
    }
}
