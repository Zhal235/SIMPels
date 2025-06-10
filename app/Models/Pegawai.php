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
}
