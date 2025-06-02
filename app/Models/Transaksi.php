<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'keuangan_transaksis';

    protected $fillable = [
        'santri_id',
        'keuangan_kategori_id',
        'keuangan_metode_id',
        'tipe_pembayaran',
        'nominal',
        'tanggal',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function kategoriKeuangan()
    {
        return $this->belongsTo(KategoriKeuangan::class, 'keuangan_kategori_id');
    }

    public function metodePembayaran()
    {
        return $this->belongsTo(MetodePembayaran::class, 'keuangan_metode_id');
    }
    //
}
