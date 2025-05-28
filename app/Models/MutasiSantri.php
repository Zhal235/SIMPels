<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutasiSantri extends Model
{
    use HasFactory;

    // Table name (optional, kalau table kamu bukan mutasi_santris)
    protected $table = 'mutasi_santris';

    protected $fillable = [
    'santri_id',
    'nama',
    'alasan',
    'tujuan_mutasi',
    'tanggal_mutasi',   // jangan lupa ini
];



    // Relasi ke data santri
public function santri()
{
    return $this->belongsTo(\App\Models\Santri::class, 'santri_id');
}

}
