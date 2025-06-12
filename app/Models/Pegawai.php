<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pegawai extends Model
{
    use HasFactory;

    protected $fillable = [
        'nip',
        'nama_pegawai',
        'nik',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'no_hp',
        'email',
        'agama',
        'status_pernikahan',
        'pendidikan_terakhir',
        'jurusan',
        'institusi',
        'tahun_lulus',
        'jabatan',
        'jabatan_id',
        'divisi',
        'tanggal_masuk',
        'tanggal_keluar',
        'status_pegawai',
        'jenis_pegawai',
        'gaji_pokok',
        'keterangan',
        'foto'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
        'gaji_pokok' => 'decimal:2'
    ];

    // Accessor untuk nama lengkap
    public function getNamaLengkapAttribute()
    {
        return $this->nama_pegawai;
    }

    // Accessor untuk umur
    public function getUmurAttribute()
    {
        return $this->tanggal_lahir ? $this->tanggal_lahir->age : null;
    }

    // Accessor untuk jabatan utama
    public function getJabatanUtamaAttribute()
    {
        return $this->jabatanUtama()->first() ?: $this->jabatan;
    }

    // Accessor untuk nama jabatan utama
    public function getNamaJabatanUtamaAttribute()
    {
        $jabatanUtama = $this->jabatan_utama;
        return $jabatanUtama ? $jabatanUtama->nama_jabatan : ($this->jabatan ? $this->jabatan->nama_jabatan : $this->jabatan);
    }

    // Accessor untuk bidang dari jabatan utama
    public function getBidangUtamaAttribute()
    {
        $jabatanUtama = $this->jabatan_utama;
        if ($jabatanUtama && $jabatanUtama->bidang) {
            return $jabatanUtama->bidang->nama_bidang;
        }
        return $this->jabatan && $this->jabatan->bidang ? $this->jabatan->bidang->nama_bidang : $this->divisi;
    }

    // Method untuk set jabatan utama
    public function setJabatanUtama($jabatanId, $tanggalMulai = null)
    {
        // Reset semua jabatan utama
        $this->pegawaiJabatans()->update(['is_jabatan_utama' => false]);
        
        // Set jabatan utama baru
        return $this->pegawaiJabatans()
                    ->where('jabatan_id', $jabatanId)
                    ->where('status', 'aktif')
                    ->update([
                        'is_jabatan_utama' => true,
                        'tanggal_mulai' => $tanggalMulai ?: now()
                    ]);
    }

    // Scope untuk pegawai aktif
    public function scopeAktif($query)
    {
        return $query->where('status_pegawai', 'Aktif');
    }

    // Scope untuk pegawai berdasarkan jabatan
    public function scopeByJabatan($query, $jabatan)
    {
        return $query->where('jabatan', $jabatan);
    }

    // Scope untuk pegawai berdasarkan divisi
    public function scopeByDivisi($query, $divisi)
    {
        return $query->where('divisi', $divisi);
    }

    // Relasi ke jabatan (single - untuk backward compatibility)
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    // Relasi ke banyak jabatan melalui pivot
    public function jabatans()
    {
        return $this->belongsToMany(Jabatan::class, 'pegawai_jabatan')
                    ->withPivot(['is_jabatan_utama', 'tanggal_mulai', 'tanggal_selesai', 'status', 'keterangan'])
                    ->withTimestamps();
    }

    // Relasi ke jabatan aktif saja
    public function jabatansAktif()
    {
        return $this->belongsToMany(Jabatan::class, 'pegawai_jabatan')
                    ->withPivot(['is_jabatan_utama', 'tanggal_mulai', 'tanggal_selesai', 'status', 'keterangan'])
                    ->withTimestamps()
                    ->wherePivot('status', 'aktif')
                    ->where(function($query) {
                        $query->whereNull('pegawai_jabatan.tanggal_selesai')
                              ->orWhere('pegawai_jabatan.tanggal_selesai', '>', now()->format('Y-m-d'));
                    });
    }

    // Relasi ke jabatan utama
    public function jabatanUtama()
    {
        return $this->belongsToMany(Jabatan::class, 'pegawai_jabatan')
                    ->withPivot(['is_jabatan_utama', 'tanggal_mulai', 'tanggal_selesai', 'status', 'keterangan'])
                    ->withTimestamps()
                    ->wherePivot('is_jabatan_utama', true)
                    ->wherePivot('status', 'aktif');
    }

    // Relasi ke PegawaiJabatan untuk manajemen detail
    public function pegawaiJabatans()
    {
        return $this->hasMany(PegawaiJabatan::class);
    }

    // Relasi ke dompet (asatidz)
    public function dompet()
    {
        return $this->hasOne(Dompet::class, 'pemilik_id')->where('jenis_pemilik', 'asatidz');
    }
}
