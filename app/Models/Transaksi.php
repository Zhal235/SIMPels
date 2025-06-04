<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'keuangan_transaksis';

    protected $fillable = [
        'santri_id',
        'jenis_pembayaran_id',
        'pembayaran_santri_id',
        'tipe_pembayaran',
        'nominal',
        'tanggal',
        'keterangan',
        'tahun_ajaran_id',
        'bulan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function jenisTagihan()
    {
        return $this->belongsTo(JenisTagihan::class, 'jenis_pembayaran_id');
    }

    /**
     * Relasi dengan PembayaranSantri
     */
    public function pembayaranSantri()
    {
        return $this->belongsTo(PembayaranSantri::class);
    }

    /**
     * Relasi dengan TahunAjaran
     */
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
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
    //
}
