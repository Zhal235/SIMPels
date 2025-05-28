<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pekerjaan; // ← WAJIB agar relasi tidak error

class Santri extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis', 'nisn', 'nik_siswa', 'nama_siswa', 'tempat_lahir', 'tanggal_lahir',
        'jenis_kelamin', 'kelas', 'asal_sekolah', 'hobi', 'cita_cita',
        'jumlah_saudara', 'alamat', 'provinsi', 'kabupaten', 'kecamatan',
        'desa', 'kode_pos', 'no_kk', 'nama_ayah', 'nik_ayah', 'pendidikan_ayah',
        'pekerjaan_ayah', 'hp_ayah', 'nama_ibu', 'nik_ibu', 'pendidikan_ibu',
        'pekerjaan_ibu', 'hp_ibu', 'no_bpjs', 'no_pkh', 'no_kip',
        'npsn_sekolah', 'no_blanko_skhu', 'no_seri_ijazah',
        'total_nilai_un', 'tanggal_kelulusan', 'foto',// ← WAJIB
    ];

    public function pekerjaanAyah()
    {
        return $this->belongsTo(Pekerjaan::class, 'pekerjaan_ayah');
    }

    public function pekerjaanIbu()
    {
        return $this->belongsTo(Pekerjaan::class, 'pekerjaan_ibu');
    }
    public function asrama()
    {
    return $this->belongsTo(\App\Models\Asrama::class, 'asrama_id');
    }
    public function kelas_anggota()
    {
    return $this->hasMany(\App\Models\KelasAnggota::class, 'santri_id');
    }
    public function asrama_anggota()
{
    return $this->hasMany(AsramaAnggota::class, 'santri_id');
}
public function asrama_anggota_terakhir()
{
    return $this->hasOne(AsramaAnggota::class, 'santri_id')->latestOfMany();
}
// app/Models/Santri.php

public function mutasi()
{
    return $this->hasOne(MutasiSantri::class, 'santri_id');
}

public function scopeBelumMutasi($query)
{
    return $query->whereDoesntHave('mutasi');
}




}
