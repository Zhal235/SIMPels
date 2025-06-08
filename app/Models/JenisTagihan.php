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
        'bulan_jatuh_tempo',
        'target_type',
        'target_kelas',
        'target_santri',
        'tipe_pembayaran',
        'mode_santri',
        'kelas_ids',
        'santri_ids'
    ];

    protected $casts = [
        'bulan_pembayaran' => 'array',
        'target_kelas' => 'array',
        'target_santri' => 'array',
        'kelas_ids' => 'array',
        'santri_ids' => 'array',
        'is_bulanan' => 'boolean',
        'nominal_tagihan' => 'decimal:2'
    ];

    /**
     * Get bulan_pembayaran attribute with safe JSON decoding
     */
    public function getBulanPembayaranAttribute($value)
    {
        if (empty($value)) {
            return [];
        }
        
        // Handle double-encoded JSON
        $decoded = json_decode($value, true);
        
        // If first decode fails or returns string, try again
        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }
        
        // Return array or empty array
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Set bulan_pembayaran attribute with proper JSON encoding
     */
    public function setBulanPembayaranAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['bulan_pembayaran'] = json_encode($value);
        } else {
            $this->attributes['bulan_pembayaran'] = $value;
        }
    }

    /**
     * Get kelas_ids attribute with safe JSON decoding
     */
    public function getKelasIdsAttribute($value)
    {
        if (empty($value)) {
            return [];
        }
        
        // Handle double-encoded JSON
        $decoded = json_decode($value, true);
        
        // If first decode fails or returns string, try again
        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }
        
        // Convert to integers and return array
        return is_array($decoded) ? array_map('intval', $decoded) : [];
    }

    /**
     * Set kelas_ids attribute with proper JSON encoding
     */
    public function setKelasIdsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['kelas_ids'] = json_encode($value);
        } else {
            $this->attributes['kelas_ids'] = $value;
        }
    }

    /**
     * Get santri_ids attribute with safe JSON decoding
     */
    public function getSantriIdsAttribute($value)
    {
        if (empty($value)) {
            return [];
        }
        
        // Handle double-encoded JSON
        $decoded = json_decode($value, true);
        
        // If first decode fails or returns string, try again
        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }
        
        // Convert to integers and return array
        return is_array($decoded) ? array_map('intval', $decoded) : [];
    }

    /**
     * Set santri_ids attribute with proper JSON encoding
     */
    public function setSantriIdsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['santri_ids'] = json_encode($value);
        } else {
            $this->attributes['santri_ids'] = $value;
        }
    }

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

        // Get the actual bulan_pembayaran data
        $selectedMonths = $this->bulan_pembayaran ?? [];
        
        // If it's empty, return empty array
        if (empty($selectedMonths)) {
            return [];
        }
        
        // Map month codes to names
        return collect($selectedMonths)->map(function($month) use ($months) {
            return $months[$month] ?? $month;
        })->toArray();
    }

    /**
     * Get formatted month names as string for display
     */
    public function getBulanNamesStringAttribute()
    {
        $names = $this->bulan_names;
        if (empty($names)) {
            return '-';
        }
        
        // If more than 3 months, show first 2 and "+ X lainnya"
        if (count($names) > 3) {
            $firstTwo = array_slice($names, 0, 2);
            $remaining = count($names) - 2;
            return implode(', ', $firstTwo) . " + {$remaining} lainnya";
        }
        
        return implode(', ', $names);
    }



    /**
     * Relasi ke model Santri melalui tabel tagihan_santris
     */
    public function santris()
    {
        return $this->belongsToMany(Santri::class, 'tagihan_santris', 'jenis_tagihan_id', 'santri_id');
    }

    /**
     * Relasi ke model Kelas melalui tabel pivot jenis_tagihan_kelas
     */
    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'jenis_tagihan_kelas', 'jenis_tagihan_id', 'kelas_id');
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
