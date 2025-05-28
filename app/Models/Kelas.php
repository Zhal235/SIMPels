<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'tingkat',
        'wali_kelas',
    ];

    public function wali()
{
    return $this->belongsTo(\App\Models\User::class, 'wali_kelas');
}

public function santris()
{
    return $this->hasMany(\App\Models\Santri::class, 'kelas_id');
}
 // app/Models/Kelas.php
public function siswa()
{
    return $this->hasMany(\App\Models\Santri::class, 'kelas_id');
}
public function anggota()
{
    return $this->hasMany(KelasAnggota::class, 'kelas_id');
}

    // relasi dan method lain...
}
