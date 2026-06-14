<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggaran extends Model
{
    protected $fillable = [
    'siswa_id',
    'tanggal',
    'jenis_pelanggaran',
    'poin',
    'keterangan',
    'foto_bukti'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
