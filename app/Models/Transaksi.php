<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'keuangan_transaksis';

    protected $fillable = [
        'santri_id',
        'tagihan_santri_id',
        'jenis_tagihan_id',
        'tipe_pembayaran',
        'nominal',
        'tanggal',
        'keterangan',
        'tahun_ajaran_id'
    ];

    protected $dates = [
        'tanggal'
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function tagihanSantri()
    {
        return $this->belongsTo(TagihanSantri::class, 'tagihan_santri_id');
    }

    // Relasi langsung ke JenisTagihan
    public function jenisTagihan()
    {
        return $this->belongsTo(JenisTagihan::class, 'jenis_tagihan_id');
    }

    // Akses JenisTagihan melalui TagihanSantri (backup method)
    public function jenisTagihanViaTagihan()
    {
        return $this->hasOneThrough(
            JenisTagihan::class,
            TagihanSantri::class,
            'id', // foreign key on TagihanSantri
            'id', // foreign key on JenisTagihan
            'tagihan_santri_id', // local key on Transaksi
            'jenis_tagihan_id' // local key on TagihanSantri
        );
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function scopeActiveYear($query)
    {
        $activeTahunAjaran = TahunAjaran::where('is_active', true)->first();
        if ($activeTahunAjaran) {
            return $query->where('tahun_ajaran_id', $activeTahunAjaran->id);
        }
        return $query->whereNull('id'); // Return empty result if no active year
    }
}
