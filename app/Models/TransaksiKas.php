<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiKas extends Model
{
    use HasFactory;
    
    protected $table = 'transaksi_kas';
    
    protected $fillable = [
        'buku_kas_id',
        'buku_kas_tujuan_id',
        'jenis_transaksi',
        'kategori',
        'kode_transaksi',
        'jumlah',
        'keterangan',
        'metode_pembayaran',
        'nama_pemohon',
        'no_referensi',
        'tanggal_transaksi',
        'bukti_transaksi',
        'created_by',
        'approved_by',
        'status',
        'tagihan_santri_id'
    ];
    
    protected $casts = [
        'tanggal_transaksi' => 'date',
        'jumlah' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Relasi ke Buku Kas sumber
     */
    public function bukuKas(): BelongsTo
    {
        return $this->belongsTo(BukuKas::class, 'buku_kas_id');
    }
    
    /**
     * Relasi ke Buku Kas tujuan (untuk transfer)
     */
    public function bukuKasTujuan(): BelongsTo
    {
        return $this->belongsTo(BukuKas::class, 'buku_kas_tujuan_id');
    }
    
    /**
     * Relasi ke User yang membuat transaksi
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Relasi ke User yang menyetujui transaksi
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
    /**
     * Relasi ke Tagihan Santri (untuk transaksi pembayaran tagihan)
     */
    public function tagihanSantri(): BelongsTo
    {
        return $this->belongsTo(TagihanSantri::class, 'tagihan_santri_id');
    }
    
    /**
     * Menentukan jenis transaksi dengan warna
     */
    public function getJenisTransaksiColorAttribute(): string
    {
        return match($this->jenis_transaksi) {
            'pemasukan' => 'green',
            'pengeluaran' => 'red',
            'transfer' => 'blue',
            default => 'gray'
        };
    }
    
    /**
     * Format jumlah transaksi dengan tanda + atau - berdasarkan jenis transaksi
     */
    public function getFormattedJumlahAttribute(): string
    {
        $prefix = $this->jenis_transaksi === 'pengeluaran' ? '-' : '+';
        
        if ($this->jenis_transaksi === 'transfer') {
            $prefix = '';
        }
        
        return $prefix . number_format($this->jumlah, 0, ',', '.');
    }
    
    /**
     * Generate unique kode transaksi
     */
    public static function generateKodeTransaksi(string $jenisTransaksi): string
    {
        $prefix = match($jenisTransaksi) {
            'pemasukan' => 'IN',
            'pengeluaran' => 'OUT',
            'transfer' => 'TRF',
            default => 'TRX'
        };
        
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', now()->toDateString())->count() + 1;
        
        return "{$prefix}-{$date}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
