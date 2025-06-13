<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranTagihan extends Model
{
    use HasFactory;
    
    protected $table = 'pembayaran_tagihan';
    
    protected $fillable = [
        'tagihan_id', 'jumlah', 'tanggal', 'metode', 'keterangan'
    ];
    
    protected $dates = [
        'tanggal'
    ];
    
    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'tagihan_id');
    }
}
