<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeringananTagihan extends Model
{
    use HasFactory;

    protected $table = 'keringanan_tagihans';

    protected $fillable = [
        'santri_id',
        'jenis_tagihan_id',
        'tahun_ajaran_id',
        'jenis_keringanan',
        'nilai_potongan',
        'keterangan',
        'status',
        'santri_tertanggung_id',
        'tanggal_mulai',
        'tanggal_selesai'
    ];

    protected $casts = [
        'nilai_potongan' => 'decimal:2',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    /**
     * Relasi dengan santri penerima keringanan
     */
    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    /**
     * Relasi dengan jenis tagihan
     */
    public function jenisTagihan()
    {
        return $this->belongsTo(JenisTagihan::class);
    }

    /**
     * Relasi dengan tahun ajaran
     */
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    /**
     * Relasi dengan santri tertanggung (untuk kasus 2 santri bayar 1)
     */
    public function santriTertanggung()
    {
        return $this->belongsTo(Santri::class, 'santri_tertanggung_id');
    }

    /**
     * Scope untuk filter data aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Cek apakah keringanan masih berlaku berdasarkan tanggal
     */
    public function isValidByDate()
    {
        $today = now()->startOfDay();
        
        if ($this->tanggal_mulai && $today->lt($this->tanggal_mulai)) {
            return false;
        }
        
        if ($this->tanggal_selesai && $today->gt($this->tanggal_selesai)) {
            return false;
        }
        
        return true;
    }

    /**
     * Hitung nilai keringanan untuk tagihan tertentu
     */
    public function hitungNilaiKeringanan($nominalTagihan)
    {
        switch ($this->jenis_keringanan) {
            case 'potongan_persen':
                // Nilai potongan sebagai persentase (misalnya 50 untuk 50%)
                return $nominalTagihan * ($this->nilai_potongan / 100);
                
            case 'potongan_nominal':
                // Nilai potongan sebagai nominal tetap
                return min($this->nilai_potongan, $nominalTagihan); // Tidak boleh lebih dari nominal tagihan
                
            case 'pembebasan':
                // Pembebasan biaya (gratis)
                return $nominalTagihan;
                
            case 'bayar_satu_gratis_satu':
                // Kasus 2 santri bayar 1
                return $nominalTagihan;
                
            default:
                return 0;
        }
    }
}
