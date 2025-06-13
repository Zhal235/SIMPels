<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'santri_id', 'jenis_tagihan', 'periode', 'jumlah', 
        'sudah_bayar', 'status', 'jatuh_tempo', 'keterangan'
    ];
    
    protected $dates = [
        'jatuh_tempo'
    ];
    
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }
    
    public function pembayaran()
    {
        return $this->hasMany(PembayaranTagihan::class, 'tagihan_id');
    }
    
    public function getSisaTagihanAttribute()
    {
        return $this->jumlah - $this->sudah_bayar;
    }
    
    public function getSantriNamaAttribute()
    {
        return $this->santri->nama_santri ?? null;
    }
}
