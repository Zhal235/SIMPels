<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPembayaran extends Model
{
    use HasFactory;

    protected $table = 'keuangan_jenis_pembayarans';

    protected $fillable = [
        'nama',
        'kategori_pembayaran',
        'nominal_tagihan',
    ];
}
