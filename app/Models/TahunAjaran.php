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
     * Get tahun ajaran aktif with cache clearing capability
     */
    public static function getActive($forceRefresh = false)
    {
        if ($forceRefresh) {
            // Clear any potential model cache if using caching
            \Cache::forget('active_tahun_ajaran');
        }
        
        $active = self::where('is_active', true)->first();
        
        // Fallback: if no active academic year, get the most recent one
        if (!$active) {
            $active = self::orderBy('tahun_mulai', 'desc')->first();
            if ($active) {
                \Log::warning('No active academic year found, using most recent: ' . $active->nama_tahun_ajaran);
            }
        }
        
        return $active;
    }
    
    /**
     * Clear any cached academic year data
     */
    public static function clearCache()
    {
        \Cache::forget('active_tahun_ajaran');
        // Clear other potential caches if using any caching mechanism
        return true;
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
    
    /**
     * Accessor untuk nama (alias ke nama_tahun_ajaran)
     */
    public function getNamaAttribute()
    {
        return $this->nama_tahun_ajaran ?? $this->tahun_mulai . '/' . $this->tahun_selesai;
    }
}