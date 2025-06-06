<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\KeuanganManagementTrait;

class JenisBukuKas extends Model
{
    use HasFactory, KeuanganManagementTrait;

    protected $table = 'jenis_buku_kas';

    protected $fillable = [
        'nama',
        'kode',
        'deskripsi',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Relasi dengan BukuKas
     */
    public function bukuKas()
    {
        return $this->hasMany(BukuKas::class, 'jenis_kas_id');
    }

    /**
     * Get searchable fields for JenisBukuKas
     */
    protected function getSearchableFields(): array
    {
        return ['nama', 'kode', 'deskripsi'];
    }
}
