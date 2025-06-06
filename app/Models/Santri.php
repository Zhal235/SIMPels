<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pekerjaan; // ← WAJIB agar relasi tidak error
use App\Models\Kelas;
use App\Models\KelasAnggota;

class Santri extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis', 'nisn', 'nik_santri', 'nama_santri', 'tempat_lahir', 'tanggal_lahir',
        'jenis_kelamin', 'kelas', 'asal_sekolah', 'hobi', 'cita_cita',
        'jumlah_saudara', 'alamat', 'provinsi', 'kabupaten', 'kecamatan',
        'desa', 'kode_pos', 'no_kk', 'nama_ayah', 'nik_ayah', 'pendidikan_ayah',
        'pekerjaan_ayah', 'hp_ayah', 'nama_ibu', 'nik_ibu', 'pendidikan_ibu',
        'pekerjaan_ibu', 'hp_ibu', 'no_bpjs', 'no_pkh', 'no_kip',
        'npsn_sekolah', 'no_blanko_skhu', 'no_seri_ijazah', 'status',
        'total_nilai_un', 'tanggal_kelulusan', 'foto', 'asrama_id', 'kelas_id',// ← WAJIB
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
/**
 * Relasi: santri bisa punya satu UID RFID
 */
public function rfidTag()
{
    return $this->hasOne(RfidTag::class);
}

 public function kelasRelasi()
    {
        return $this->belongsToMany(
            \App\Models\Kelas::class,
            'kelas_anggota',  // nama tabel pivot yang benar
            'santri_id',      // FK pada pivot ke santri
            'kelas_id'        // FK pada pivot ke kelas
        );
    }

    /**
     * Relasi dengan TagihanSantri
     */
    public function tagihanSantris()
    {
        return $this->hasMany(TagihanSantri::class);
    }

}
