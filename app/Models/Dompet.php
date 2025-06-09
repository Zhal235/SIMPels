<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dompet extends Model
{
    protected $table = 'dompet';

    protected $fillable = [
        'jenis_pemilik',
        'pemilik_id',
        'nomor_dompet',
        'saldo',
        'limit_transaksi',
        'is_active'
    ];

    protected $casts = [
        'saldo' => 'decimal:2',
        'limit_transaksi' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // Generate nomor dompet otomatis
    public static function generateNomorDompet($jenis, $pemilikId)
    {
        $prefix = $jenis === 'santri' ? 'DS' : 'DA'; // Dompet Santri / Dompet Asatidz
        $year = date('Y');
        $number = str_pad($pemilikId, 4, '0', STR_PAD_LEFT);
        
        return $prefix . $year . $number;
    }

    // Relasi ke santri (polymorphic)
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'pemilik_id');
    }

    // Relasi ke user/asatidz (polymorphic)
    public function asatidz()
    {
        return $this->belongsTo(User::class, 'pemilik_id');
    }

    // Relasi ke transaksi dompet
    public function transaksiDompet()
    {
        return $this->hasMany(TransaksiDompet::class);
    }

    // Method untuk update saldo
    public function updateSaldo($jumlah, $jenis)
    {
        $saldoLama = $this->saldo;
        
        if ($jenis === 'tambah') {
            $this->saldo += $jumlah;
        } elseif ($jenis === 'kurang') {
            if ($this->saldo < $jumlah) {
                throw new \Exception('Saldo tidak mencukupi');
            }
            $this->saldo -= $jumlah;
        }
        
        $this->save();
        
        return [
            'saldo_sebelum' => $saldoLama,
            'saldo_sesudah' => $this->saldo
        ];
    }

    // Scope untuk filter berdasarkan jenis pemilik
    public function scopeJenisPemilik($query, $jenis)
    {
        return $query->where('jenis_pemilik', $jenis);
    }

    // Scope untuk dompet aktif
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    // Accessor untuk nama pemilik
    public function getNamaPemilikAttribute()
    {
        if ($this->jenis_pemilik === 'santri') {
            return $this->santri?->nama_santri ?? 'Santri tidak ditemukan';
        } else {
            return $this->asatidz?->name ?? 'Asatidz tidak ditemukan';
        }
    }
}
