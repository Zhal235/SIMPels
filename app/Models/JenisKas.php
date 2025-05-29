<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisKas extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_kas',
        'nama_jenis_kas',
        'keterangan',
    ];
}