<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\KeuanganManagementTrait;

class BukuKas extends Model
{
    use HasFactory, KeuanganManagementTrait;

    protected $table = 'buku_kas';

    protected $fillable = [
        'nama_kas',
        'kode_kas',
        'deskripsi',
        'jenis_kas_id',
        'saldo_awal',
        'saldo_saat_ini',
        'is_active'
    ];

    protected $casts = [
        'saldo_awal' => 'decimal:2',
        'saldo_saat_ini' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /**
     * Relasi dengan JenisTagihan
     */
    public function jenisTagihan()
    {
        return $this->hasMany(JenisTagihan::class, 'buku_kas_id');
    }

    /**
     * Relasi dengan TransaksiKas
     */
    public function transaksiKas()
    {
        return $this->hasMany(TransaksiKas::class, 'buku_kas_id');
    }

    /**
     * Relasi dengan JenisBukuKas
     */
    public function jenisBukuKas()
    {
        return $this->belongsTo(JenisBukuKas::class, 'jenis_kas_id');
    }

    /**
     * Accessor untuk mendapatkan nama jenis kas
     */
    public function getJenisKasAttribute()
    {
        return $this->jenisBukuKas ? $this->jenisBukuKas->nama : null;
    }
    
    /**
     * Accessor untuk mendapatkan kode jenis kas
     */
    public function getKodeJenisKasAttribute()
    {
        return $this->jenisBukuKas ? $this->jenisBukuKas->kode : null;
    }

    /**
     * Get formatted saldo
     */
    public function getFormattedSaldoAttribute()
    {
        return 'Rp ' . number_format($this->saldo_saat_ini, 0, ',', '.');
    }

    /**
     * Update saldo kas
     */
    public function updateSaldo($jumlah, $tipe = 'masuk')
    {
        if ($tipe === 'masuk') {
            $this->saldo_saat_ini += $jumlah;
        } elseif ($tipe === 'keluar') {
            $this->saldo_saat_ini -= $jumlah;
        }
        
        $this->save();
    }

    /**
     * Get searchable fields for BukuKas
     */
    protected function getSearchableFields(): array
    {
        return ['nama_kas', 'kode_kas', 'deskripsi'];
    }
}
