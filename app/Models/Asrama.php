<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asrama extends Model
{
    use HasFactory;

    protected $table = 'asrama'; // sesuaikan dengan nama tabel di database

    protected $fillable = ['kode', 'nama', 'wali_asrama'];

    // Relasi utama ke santri (anggota asrama)
    public function santris()
    {
        return $this->hasMany(\App\Models\Santri::class, 'asrama_id');
    }

    // Alias anggota() agar controller bisa pakai nama anggota()
    public function anggota()
    {
        return $this->santris();
    }
}
