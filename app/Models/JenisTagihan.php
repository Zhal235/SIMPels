<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisTagihan extends Model
{
    use HasFactory;

    protected $table = 'jenis_tagihans';

    protected $fillable = [
        'nama',
        'nominal',
        'is_nominal_per_kelas',
        'is_bulanan',
        'bulan_pembayaran',
        'deskripsi',
        'tahun_ajaran_id',
        'kategori_tagihan',
        'buku_kas_id',
        'tanggal_jatuh_tempo',
        'bulan_jatuh_tempo'
    ];

    protected $casts = [
        'bulan_pembayaran' => 'array',
        'is_bulanan' => 'boolean',
        'nominal_tagihan' => 'decimal:2'
    ];

    /**
     * Relasi dengan TahunAjaran
     */
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    /**
     * Relasi dengan BukuKas
     */
    public function bukuKas()
    {
        return $this->belongsTo(BukuKas::class);
    }

    /**
     * Scope untuk filter berdasarkan tahun ajaran aktif
     * Termasuk jenis tagihan rutin yang tidak terikat tahun ajaran
     */
    public function scopeActiveYear($query)
    {
        $activeTahunAjaran = TahunAjaran::getActive();
        if ($activeTahunAjaran) {
            return $query->where(function($q) use ($activeTahunAjaran) {
                $q->where('tahun_ajaran_id', $activeTahunAjaran->id)
                  ->orWhere(function($subQ) {
                      $subQ->where('kategori_tagihan', 'Rutin')
                           ->whereNull('tahun_ajaran_id');
                  });
            });
        }
        return $query->where('kategori_tagihan', 'Rutin')->whereNull('tahun_ajaran_id');
    }

    /**
     * Scope untuk jenis tagihan rutin (tidak terikat tahun ajaran)
     */
    public function scopeRutin($query)
    {
        return $query->where('kategori_tagihan', 'Rutin')->whereNull('tahun_ajaran_id');
    }

    /**
     * Scope untuk jenis tagihan insidental (terikat tahun ajaran)
     */
    public function scopeInsidental($query)
    {
        return $query->where('kategori_tagihan', 'Insidental');
    }

    /**
     * Get list of months for tagihan
     */
    public function getBulanPembayaranListAttribute()
    {
        if ($this->kategori_tagihan === 'Rutin' && $this->is_bulanan) {
            // Rutin bulanan = semua bulan
            return ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
        }
        
        return $this->bulan_pembayaran ?? [];
    }

    /**
     * Get month names in Indonesian
     */
    public function getBulanNamesAttribute()
    {
        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        $selectedMonths = $this->bulan_pembayaran_list;
        return collect($selectedMonths)->map(function($month) use ($months) {
            return $months[$month] ?? $month;
        })->toArray();
    }

    /**
     * Get the class-specific amounts for this jenis tagihan
     */
    public function kelasNominal()
    {
        return $this->hasMany(JenisTagihanKelas::class);
    }

    /**
     * Get nominal for a specific class
     */
    public function getNominalForKelas($kelasId)
    {
        if (!$this->is_nominal_per_kelas) {
            return $this->nominal;
        }

        $kelasNominal = $this->kelasNominal()
            ->where('kelas_id', $kelasId)
            ->first();

        return $kelasNominal ? $kelasNominal->nominal : $this->nominal;
    }

    /**
     * Set nominal for a specific class
     */
    public function setNominalForKelas($kelasId, $nominal)
    {
        return $this->kelasNominal()->updateOrCreate(
            ['kelas_id' => $kelasId],
            ['nominal' => $nominal]
        );
    }

    /**
     * Determine if this tagihan has any class-specific amounts
     */
    public function hasClassSpecificAmounts()
    {
        return $this->is_nominal_per_kelas && $this->kelasNominal()->count() > 0;
    }
}
