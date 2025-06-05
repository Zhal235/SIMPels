<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisTagihanKelas extends Model
{
    use HasFactory;

    protected $table = 'jenis_tagihan_kelas';

    protected $fillable = [
        'jenis_tagihan_id',
        'kelas_id',
        'nominal'
    ];

    protected $casts = [
        'nominal' => 'decimal:2'
    ];

    /**
     * Get the jenis tagihan that owns the class-specific amount
     */
    public function jenisTagihan()
    {
        return $this->belongsTo(JenisTagihan::class);
    }

    /**
     * Get the class that owns this amount
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}
