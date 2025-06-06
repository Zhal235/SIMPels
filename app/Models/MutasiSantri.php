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
        'tanggal_mutasi',
        'keterangan'
    ];
    
    // Cast tanggal mutasi sebagai date
    protected $dates = [
        'tanggal_mutasi',
        'created_at',
        'updated_at'
    ];



    // Relasi ke data santri
public function santri()
{
    return $this->belongsTo(\App\Models\Santri::class, 'santri_id');
}

}
