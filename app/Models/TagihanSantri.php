<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanSantri extends Model
{
    use HasFactory;

    protected $table = 'tagihan_santris';

    protected $fillable = [
        'santri_id',
        'jenis_tagihan_id',
        'tahun_ajaran_id',
        'bulan',
        'nominal_tagihan',
        'nominal_dibayar',
        'status',
        'keterangan'
    ];

    protected $casts = [
        'nominal_tagihan' => 'decimal:2',
        'nominal_dibayar' => 'decimal:2',
    ];

    /**
     * Relasi dengan Santri
     */
    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    /**
     * Relasi dengan JenisTagihan
     */
    public function jenisTagihan()
    {
        return $this->belongsTo(JenisTagihan::class);
    }

    /**
     * Relasi dengan TahunAjaran
     */
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    /**
     * Relasi dengan Transaksi (Pembayaran)
     */
    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'tagihan_santri_id');
    }

    /**
     * Scope untuk filter berdasarkan tahun ajaran aktif
     */
    public function scopeActiveYear($query)
    {
        $activeTahunAjaran = TahunAjaran::where('is_active', true)->first();
        if ($activeTahunAjaran) {
            return $query->where('tahun_ajaran_id', $activeTahunAjaran->id);
        }
        return $query;
    }

    /**
     * Scope untuk filter berdasarkan status aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Get sisa tagihan
     */
    public function getSisaTagihanAttribute()
    {
        return $this->nominal_tagihan - $this->nominal_dibayar;
    }

    /**
     * Get status pembayaran
     */
    public function getStatusPembayaranAttribute()
    {
        if ($this->nominal_dibayar == 0) {
            return 'belum_bayar';
        } elseif ($this->nominal_dibayar >= $this->nominal_tagihan) {
            return 'lunas';
        } else {
            return 'sebagian';
        }
    }

    /**
     * Get persentase pembayaran
     */
    public function getPersentasePembayaranAttribute()
    {
        if ($this->nominal_tagihan == 0) {
            return 0;
        }
        return round(($this->nominal_dibayar / $this->nominal_tagihan) * 100, 2);
    }

    /**
     * Get bulan tahun dalam format yang mudah dibaca
     */
    public function getBulanTahunAttribute()
    {
        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        $parts = explode('-', $this->bulan);
        if (count($parts) === 2) {
            $tahun = $parts[0];
            $bulan = $parts[1];
            return ($months[$bulan] ?? $bulan) . ' ' . $tahun;
        }
        
        return $this->bulan;
    }

    /**
     * Update nominal dibayar berdasarkan transaksi
     */
    public function updateNominalDibayar()
    {
        $totalDibayar = $this->transaksis()->sum('nominal');
        $this->update(['nominal_dibayar' => $totalDibayar]);
        return $this;
    }

    /**
     * Update pembayaran (hanya nominal_dibayar) berdasarkan transaksi
     */
    public function updatePembayaran()
    {
        $totalDibayar = $this->transaksis()->sum('nominal');
        
        // Update hanya nominal_dibayar
        // Status pembayaran akan dihitung otomatis melalui accessor getStatusPembayaranAttribute()
        $this->update([
            'nominal_dibayar' => $totalDibayar
        ]);
        
        return $this;
    }

    /**
     * Scope untuk filter berdasarkan bulan
     */
    public function scopeBulan($query, $bulan)
    {
        return $query->where('bulan', $bulan);
    }

    /**
     * Scope untuk filter berdasarkan santri
     */
    public function scopeBySantri($query, $santriId)
    {
        return $query->where('santri_id', $santriId);
    }
}
