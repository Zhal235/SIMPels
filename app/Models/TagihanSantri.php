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
        'nominal_keringanan', // Tambahkan kolom ini
        'status',
        'keterangan',
        'tanggal_jatuh_tempo'
    ];

    protected $casts = [
        'nominal_tagihan' => 'decimal:2',
        'nominal_dibayar' => 'decimal:2',
        'nominal_keringanan' => 'decimal:2', // Tambahkan casting
        'tanggal_jatuh_tempo' => 'date'
    ];

    protected $appends = [
        'sisa_tagihan', 
        'status_pembayaran', 
        'persentase_pembayaran',
        'nominal_harus_dibayar', // Nominal setelah dikurangi keringanan
        'is_jatuh_tempo'
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
        return $this->hasMany(Transaksi::class);
    }
    
    /**
     * Relasi dengan TransaksiKas
     */
    public function transaksiKas()
    {
        return $this->hasMany(\App\Models\TransaksiKas::class);
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
        return $query->whereHas('tahunAjaran', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope untuk filter berdasarkan tahun ajaran tertentu
     */
    public function scopeByTahunAjaran($query, $tahunAjaranId)
    {
        return $query->where('tahun_ajaran_id', $tahunAjaranId);
    }

    /**
     * Scope untuk filter tagihan yang sudah jatuh tempo
     */
    public function scopeJatuhTempo($query)
    {
        return $query->where('tanggal_jatuh_tempo', '<', now())
                    ->whereNotNull('tanggal_jatuh_tempo');
    }

    /**
     * Scope untuk filter tagihan yang belum lunas
     */
    public function scopeBelumLunas($query)
    {
        return $query->whereRaw('(nominal_tagihan - nominal_dibayar - COALESCE(nominal_keringanan, 0)) > 0');
    }

    /**
     * Get sisa tagihan
     */
    public function getSisaTagihanAttribute()
    {
        return $this->nominal_tagihan - $this->nominal_dibayar - $this->nominal_keringanan;
    }

    /**
     * Get status pembayaran
     */
    public function getStatusPembayaranAttribute()
    {
        // Hitung total yang sudah terpenuhi (dibayar + keringanan)
        $totalTerpenuhi = $this->nominal_dibayar + $this->nominal_keringanan;
        
        // Jika pembebasan penuh (gratis)
        if ($this->nominal_keringanan >= $this->nominal_tagihan) {
            return 'lunas';
        }
        
        // Jika belum ada pembayaran sama sekali
        if ($this->nominal_dibayar == 0) {
            return 'belum_bayar';
        } 
        // Jika sudah lunas (dibayar + keringanan >= nominal tagihan)
        elseif ($totalTerpenuhi >= $this->nominal_tagihan) {
            return 'lunas';
        } 
        // Jika sudah bayar sebagian
        else {
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
        
        // Hitung persentase berdasarkan pembayaran + keringanan
        $totalTerpenuhi = $this->nominal_dibayar + $this->nominal_keringanan;
        return round(($totalTerpenuhi / $this->nominal_tagihan) * 100, 2);
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
     * Get nominal yang harus dibayar (setelah keringanan)
     */
    public function getNominalHarusDibayarAttribute()
    {
        return $this->nominal_tagihan - $this->nominal_keringanan;
    }

    /**
     * Cek apakah tagihan sudah jatuh tempo
     * dengan memperhatikan apakah tahun ajaran sudah dimulai
     */
    public function getIsJatuhTempoAttribute()
    {
        // Jika tanggal jatuh tempo tidak ada, tidak dianggap jatuh tempo
        if (!$this->tanggal_jatuh_tempo) {
            return false;
        }
        
        // Cek tahun ajaran apakah sudah dimulai
        $tahunAjaran = $this->tahunAjaran;
        if ($tahunAjaran) {
            // Format tahun ajaran: 2025-07-01
            $tanggalMulaiTahunAjaran = \Carbon\Carbon::createFromDate(
                $tahunAjaran->tahun_mulai, 
                7, // Juli
                1  // Tanggal 1
            );
            
            // Jika sekarang belum mencapai tanggal mulai tahun ajaran
            // maka tagihan belum dianggap jatuh tempo
            if (now()->lessThan($tanggalMulaiTahunAjaran)) {
                return false;
            }
        }
        
        // Jika tanggal sekarang > tanggal jatuh tempo, maka jatuh tempo
        return now()->greaterThan($this->tanggal_jatuh_tempo);
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
     * Update nominal keringanan
     * 
     * @param float $nominalKeringanan
     * @return $this
     */
    public function updateNominalKeringanan($nominalKeringanan)
    {
        // Pastikan nilai keringanan tidak melebihi nominal tagihan
        $nominalKeringanan = min($nominalKeringanan, $this->nominal_tagihan);
        
        // Update nilai keringanan
        $this->update([
            'nominal_keringanan' => $nominalKeringanan
        ]);
        
        return $this;
    }

    /**
     * Check apakah tagihan memiliki keringanan
     * 
     * @return bool
     */
    public function hasKeringanan()
    {
        return $this->nominal_keringanan > 0;
    }
    
    /**
     * Relasi dengan keringanan tagihan
     */
    public function keringanan()
    {
        return $this->hasMany(\App\Models\KeringananTagihan::class, 'santri_id', 'santri_id')
            ->where('tahun_ajaran_id', $this->tahun_ajaran_id)
            ->where('status', 'aktif')
            ->where(function($query) {
                $query->whereNull('jenis_tagihan_id')
                    ->orWhere('jenis_tagihan_id', $this->jenis_tagihan_id);
            });
    }
    
    /**
     * Mendapatkan keringanan aktif untuk tagihan ini
     * 
     * @return \App\Models\KeringananTagihan|null
     */
    public function getKeringananAktif()
    {
        $keringanan = $this->keringanan()->first();
        
        if ($keringanan && !$keringanan->isValidByDate()) {
            return null;
        }
        
        return $keringanan;
    }

    /**
     * Aplikasikan keringanan berdasarkan model KeringananTagihan
     */
    public function aplikasikanKeringanan(KeringananTagihan $keringanan)
    {
        // Pastikan keringanan masih berlaku berdasarkan tanggal
        if (!$keringanan->isValidByDate()) {
            return $this;
        }
        
        // Hitung nilai keringanan berdasarkan jenis keringanan
        $nominalKeringanan = $keringanan->hitungNilaiKeringanan($this->nominal_tagihan);
        
        // Update nominal keringanan
        $this->updateNominalKeringanan($nominalKeringanan);
        
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
