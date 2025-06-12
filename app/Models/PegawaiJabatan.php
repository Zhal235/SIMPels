<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PegawaiJabatan extends Model
{
    protected $table = 'pegawai_jabatan';
    
    protected $fillable = [
        'pegawai_id',
        'jabatan_id', 
        'is_jabatan_utama',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'keterangan'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_jabatan_utama' => 'boolean'
    ];

    // Relasi ke pegawai
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    // Relasi ke jabatan
    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }

    // Scope untuk jabatan aktif
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif')
                    ->where(function($q) {
                        $q->whereNull('tanggal_selesai')
                          ->orWhere('tanggal_selesai', '>', now());
                    });
    }

    // Scope untuk jabatan utama
    public function scopeJabatanUtama($query)
    {
        return $query->where('is_jabatan_utama', true);
    }
}
