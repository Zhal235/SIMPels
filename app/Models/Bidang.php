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

    // Relasi ke penanggung jawab bidang (naib/wakil pimpinan)
    public function naibPenanggungJawab(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'naib_penanggung_jawab_id');
    }

    // Relasi ke pembuat/admin yang menambahkan bidang
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scope untuk bidang aktif saja
    public function scopeAktif($query)
    {
        return $query->where('status', 'active');
    }

    // Scope untuk pengurutan berdasarkan field urutan
    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan');
    }
}
