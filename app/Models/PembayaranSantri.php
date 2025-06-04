<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranSantri extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_santris';

    protected $fillable = [
        'santri_id',
        'jenis_pembayaran_id',
        'tahun_ajaran_id',
        'nominal_tagihan',
        'nominal_dibayar',
        'bulan_pembayaran',
        'status',
        'keterangan'
    ];

    protected $casts = [
        'nominal_tagihan' => 'decimal:2',
        'nominal_dibayar' => 'decimal:2',
        'bulan_pembayaran' => 'array'
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
     * Relasi dengan Transaksi
     */
    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'pembayaran_santri_id');
    }

    /**
     * Scope untuk filter berdasarkan tahun ajaran aktif
     */
    public function scopeActiveYear($query)
    {
        $activeTahunAjaran = TahunAjaran::getActive();
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
     * Check if payment is for specific month
     */
    public function isForMonth($month)
    {
        if (!$this->bulan_pembayaran) {
            return false;
        }
        return in_array($month, $this->bulan_pembayaran);
    }

    /**
     * Get month names in Indonesian
     */
    public function getBulanNamesAttribute()
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $selectedMonths = $this->bulan_pembayaran ?? [];
        return collect($selectedMonths)->map(function($month) use ($months) {
            return $months[$month] ?? $month;
        })->toArray();
    }
}