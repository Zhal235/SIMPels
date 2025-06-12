<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bidang extends Model
{
    protected $table = 'bidangs';
    
    protected $fillable = [
        'nama_bidang',
        'kode_bidang',
        'deskripsi',
        'naib_penanggung_jawab_id',
        'status',
        'urutan',
        'created_by'
    ];

    protected $casts = [
        'urutan' => 'integer'
    ];

    // Relasi ke jabatan dalam bidang ini
    public function jabatans(): HasMany
    {
        return $this->hasMany(Jabatan::class, 'bidang_id');
    }

    // Relasi ke pegawai dalam bidang ini (melalui jabatan)
    public function pegawais(): HasMany
    {
        return $this->hasManyThrough(Pegawai::class, Jabatan::class, 'bidang_id', 'jabatan_id');
    }

    // Relasi ke naib penanggung jawab
    public function naibPenanggungJawab(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'naib_penanggung_jawab_id');
    }

    // Relasi ke user yang membuat
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scope untuk bidang aktif
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // Scope untuk ordering berdasarkan urutan
    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan')->orderBy('nama_bidang');
    }

    // Accessor untuk jumlah jabatan
    public function getJumlahJabatanAttribute()
    {
        return $this->jabatans()->count();
    }

    // Accessor untuk jumlah pegawai
    public function getJumlahPegawaiAttribute()
    {
        return $this->pegawais()->count();
    }
}
