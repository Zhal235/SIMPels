<?php

namespace App\Exports;

use App\Models\Santri;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SantriExport implements FromCollection, WithHeadings
{
    protected $kelasId;

    public function __construct($kelasId = null)
    {
        $this->kelasId = $kelasId;
    }

    public function collection()
    {
        $query = Santri::select([
            'nis','nisn','nik_santri','nama_santri','tempat_lahir','tanggal_lahir',
            'jenis_kelamin','kelas','asal_sekolah','hobi','cita_cita','jumlah_saudara',
            'alamat','provinsi','kabupaten','kecamatan','desa','kode_pos','no_kk',
            'nama_ayah','nik_ayah','pendidikan_ayah','pekerjaan_ayah','hp_ayah',
            'nama_ibu','nik_ibu','pendidikan_ibu','pekerjaan_ibu','hp_ibu',
            'no_bpjs','no_pkh','no_kip','npsn_sekolah','no_blanko_skhu',
            'no_seri_ijazah','total_nilai_un','tanggal_kelulusan'
        ]);

        if ($this->kelasId) {
            $query->where('kelas_id', $this->kelasId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'nis','nisn','nik_santri','nama_santri','tempat_lahir','tanggal_lahir',
            'jenis_kelamin','kelas','asal_sekolah','hobi','cita_cita','jumlah_saudara',
            'alamat','provinsi','kabupaten','kecamatan','desa','kode_pos','no_kk',
            'nama_ayah','nik_ayah','pendidikan_ayah','pekerjaan_ayah','hp_ayah',
            'nama_ibu','nik_ibu','pendidikan_ibu','pekerjaan_ibu','hp_ibu',
            'no_bpjs','no_pkh','no_kip','npsn_sekolah','no_blanko_skhu',
            'no_seri_ijazah','total_nilai_un','tanggal_kelulusan'
        ];
    }
}
