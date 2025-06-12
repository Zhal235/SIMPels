<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jabatan extends Model
{
    protected $table = 'jabatans';
    
    protected $fillable = [
        'nama_jabatan',
        'kode_jabatan',
        'deskripsi',
        'gaji_pokok',
        'tunjangan',
        'status',
        'level_jabatan',
        'kategori_jabatan',
        'bidang_id',
        'parent_jabatan_id',
        'is_struktural',
        'created_by'
    ];

    protected $casts = [
        'gaji_pokok' => 'decimal:2',
        'tunjangan' => 'decimal:2',
        'level_jabatan' => 'integer',
        'is_struktural' => 'boolean'
    ];

    // Relasi ke pegawai
    public function pegawais(): HasMany
    {
        return $this->hasMany(Pegawai::class, 'jabatan_id');
    }

    // Relasi ke user yang membuat
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke bidang
    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Bidang::class, 'bidang_id');
    }

    // Relasi hierarki jabatan (parent)
    public function parentJabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'parent_jabatan_id');
    }

    // Relasi hierarki jabatan (children)
    public function childJabatans(): HasMany
    {
        return $this->hasMany(Jabatan::class, 'parent_jabatan_id');
    }

    // Scope untuk jabatan aktif
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // Scope untuk ordering berdasarkan level
    public function scopeOrderByLevel($query)
    {
        return $query->orderBy('level_jabatan')->orderBy('nama_jabatan');
    }

    // Scope untuk kategori jabatan
    public function scopeKategori($query, $kategori)
    {
        return $query->where('kategori_jabatan', $kategori);
    }

    // Scope untuk jabatan struktural
    public function scopeStruktural($query)
    {
        return $query->where('is_struktural', true);
    }

    // Accessor untuk total gaji
    public function getTotalGajiAttribute()
    {
        return $this->gaji_pokok + $this->tunjangan;
    }

    // Accessor untuk format rupiah gaji pokok
    public function getGajiPokokFormattedAttribute()
    {
        return 'Rp ' . number_format($this->gaji_pokok, 0, ',', '.');
    }

    // Accessor untuk format rupiah tunjangan
    public function getTunjanganFormattedAttribute()
    {
        return 'Rp ' . number_format($this->tunjangan, 0, ',', '.');
    }

    // Accessor untuk format rupiah total gaji
    public function getTotalGajiFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total_gaji, 0, ',', '.');
    }

    // Accessor untuk nama kategori yang lebih readable
    public function getKategoriNamaAttribute()
    {
        $kategoriMap = [
            'pengasuh' => 'Dewan Pengasuh',
            'pengurus' => 'Pengurus Yayasan',
            'pimpinan' => 'Pimpinan Pesantren',
            'naib' => 'Wakil Pimpinan',
            'kepala' => 'Kepala Unit/Bagian',
            'staff' => 'Staff/Pelaksana'
        ];
        
        return $kategoriMap[$this->kategori_jabatan] ?? ucfirst($this->kategori_jabatan);
    }
}
