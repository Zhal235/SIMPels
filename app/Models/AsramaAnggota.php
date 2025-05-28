<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsramaAnggota extends Model
{
    use HasFactory;

    protected $table = 'asrama_anggota';

    // Mass assignment
    protected $guarded = [];

    // Relasi ke santri
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    // Relasi ke asrama (jika model Asrama ada)
    public function asrama()
    {
        return $this->belongsTo(Asrama::class, 'asrama_id');
    }
}
