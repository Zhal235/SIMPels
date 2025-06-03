<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajaran';

    protected $fillable = [
        'nama_tahun_ajaran',
        'tahun_mulai',
        'tahun_selesai',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active',
        'keterangan'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean'
    ];

    /**
     * Scope untuk mendapatkan tahun ajaran aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get tahun ajaran aktif
     */
    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }

    /**
     * Set tahun ajaran sebagai aktif dan nonaktifkan yang lain
     */
    public function setAsActive()
    {
        // Nonaktifkan semua tahun ajaran
        self::query()->update(['is_active' => false]);
        
        // Aktifkan tahun ajaran ini
        $this->update(['is_active' => true]);
    }

    /**
     * Format nama tahun ajaran
     */
    public function getFormattedNameAttribute()
    {
        return $this->nama_tahun_ajaran . ' (' . $this->tahun_mulai . '/' . $this->tahun_selesai . ')';
    }
}