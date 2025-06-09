<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiDompet extends Model
{
    protected $table = 'transaksi_dompet';

    protected $fillable = [
        'kode_transaksi',
        'dompet_id',
        'dompet_tujuan_id',
        'jenis_transaksi',
        'kategori',
        'jumlah',
        'saldo_sebelum',
        'saldo_sesudah',
        'keterangan',
        'referensi_eksternal',
        'transaksi_kas_id',
        'created_by',
        'status',
        'tanggal_transaksi'
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'saldo_sebelum' => 'decimal:2',
        'saldo_sesudah' => 'decimal:2',
        'tanggal_transaksi' => 'datetime'
    ];

    // Generate kode transaksi
    public static function generateKodeTransaksi($jenis)
    {
        $prefix = match($jenis) {
            'top_up' => 'TU',
            'pembelian' => 'BL',
            'transfer_masuk' => 'TM',
            'transfer_keluar' => 'TK',
            'penarikan' => 'WD',
            default => 'TR'
        };
        
        $date = date('Ymd');
        $count = self::whereDate('created_at', today())->count() + 1;
        $number = str_pad($count, 4, '0', STR_PAD_LEFT);
        
        return $prefix . '-' . $date . '-' . $number;
    }

    // Relasi ke dompet
    public function dompet()
    {
        return $this->belongsTo(Dompet::class);
    }

    // Relasi ke dompet tujuan (untuk transfer)
    public function dompetTujuan()
    {
        return $this->belongsTo(Dompet::class, 'dompet_tujuan_id');
    }

    // Relasi ke transaksi kas
    public function transaksiKas()
    {
        return $this->belongsTo(TransaksiKas::class);
    }

    // Relasi ke user pembuat
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scope untuk filter berdasarkan jenis transaksi
    public function scopeJenisTransaksi($query, $jenis)
    {
        return $query->where('jenis_transaksi', $jenis);
    }

    // Scope untuk filter berdasarkan status
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk filter berdasarkan dompet
    public function scopeDompet($query, $dompetId)
    {
        return $query->where('dompet_id', $dompetId);
    }

    // Accessor untuk format jumlah
    public function getJumlahFormattedAttribute()
    {
        return 'Rp ' . number_format($this->jumlah, 0, ',', '.');
    }

    // Accessor untuk icon berdasarkan jenis transaksi
    public function getIconAttribute()
    {
        return match($this->jenis_transaksi) {
            'top_up' => 'add_circle',
            'pembelian' => 'shopping_cart',
            'transfer_masuk' => 'call_received',
            'transfer_keluar' => 'call_made',
            'penarikan' => 'money_off',
            default => 'account_balance_wallet'
        };
    }

    // Accessor untuk warna berdasarkan jenis transaksi
    public function getColorAttribute()
    {
        return match($this->jenis_transaksi) {
            'top_up', 'transfer_masuk' => 'green',
            'pembelian', 'transfer_keluar', 'penarikan' => 'red',
            default => 'blue'
        };
    }
}
