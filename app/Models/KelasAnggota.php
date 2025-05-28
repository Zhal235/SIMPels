<?php

// app/Models/KelasAnggota.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelasAnggota extends Model
{
    use HasFactory;

    protected $table = 'kelas_anggota';

    protected $fillable = [
        'santri_id',
        'kelas_id',
    ];

    // Relasi ke Santri
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    // Relasi ke Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}
