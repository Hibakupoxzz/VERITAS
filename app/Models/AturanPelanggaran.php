<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AturanPelanggaran extends Model
{
    protected $fillable = [
        'kode',
        'kategori',
        'subkategori',
        'nama',
        'poin',
        'tahap_maksimal',
        'sanksi_i',
        'sanksi_ii',
        'sanksi_iii',
        'sanksi_iv',
        'langsung_kembali',
        'aktif'
    ];

    protected $casts = [
        'langsung_kembali' => 'boolean',
        'aktif' => 'boolean',
    ];

    public function pelanggarans()
    {
        return $this->hasMany(Pelanggaran::class);
    }
}
