<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriKeuangan extends Model
{
    protected $table = 'keuangan_kategoris';

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'keuangan_kategori_id');
    }
    //
}
