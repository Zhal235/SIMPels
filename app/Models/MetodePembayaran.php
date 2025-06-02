<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodePembayaran extends Model
{
    protected $table = 'keuangan_metodes';

    protected $fillable = [
        'nama_metode',
        'deskripsi',
    ];

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'keuangan_metode_id');
    }
    //
}
