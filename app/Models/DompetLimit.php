<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DompetLimit extends Model
{
    use HasFactory;

    protected $table = 'dompet_limit';

    protected $fillable = [
        'dompet_id',
        'limit_harian',
        'limit_transaksi', 
        'limit_mingguan',
        'limit_bulanan',
        'is_active',
        'catatan',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'limit_harian' => 'decimal:2',
        'limit_transaksi' => 'decimal:2',
        'limit_mingguan' => 'decimal:2', 
        'limit_bulanan' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /**
     * Relasi ke model Dompet
     */
    public function dompet()
    {
        return $this->belongsTo(Dompet::class);
    }

    /**
     * Format limit harian
     */
    public function getFormattedLimitHarianAttribute()
    {
        return number_format($this->limit_harian, 0, ',', '.');
    }

    /**
     * Format limit transaksi
     */
    public function getFormattedLimitTransaksiAttribute()
    {
        return number_format($this->limit_transaksi, 0, ',', '.');
    }

    /**
     * Format limit mingguan
     */
    public function getFormattedLimitMingguanAttribute()
    {
        return $this->limit_mingguan ? number_format($this->limit_mingguan, 0, ',', '.') : '-';
    }

    /**
     * Format limit bulanan
     */
    public function getFormattedLimitBulananAttribute()
    {
        return $this->limit_bulanan ? number_format($this->limit_bulanan, 0, ',', '.') : '-';
    }

    /**
     * Scope untuk limit aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
