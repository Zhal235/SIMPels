<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisBayar extends Model
{
    use HasFactory;
    protected $table = 'jenis_bayar';
    protected $fillable = [
        'nama_jenis_bayar',
        'klasifikasi',
        'jenis_kas_id',
        // 'nominal', // Sesuaikan jika nominal masih diperlukan
    ];

    public function jenisKas()
    {
        return $this->belongsTo(JenisKas::class, 'jenis_kas_id');
    }
}