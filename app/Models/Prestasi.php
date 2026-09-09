<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestasi extends Model
{
    protected $fillable = [
        'siswa_id',
        'tanggal',
        'jenis_prestasi',
        'tingkat',
        'poin',
        'poin_sebelum',
        'poin_sesudah',
        'keterangan',
        'bukti',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'poin' => 'integer',
        'poin_sebelum' => 'integer',
        'poin_sesudah' => 'integer',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
