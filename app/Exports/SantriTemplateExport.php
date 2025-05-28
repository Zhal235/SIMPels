<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SantriTemplateExport implements FromCollection, WithHeadings
{
    public function collection(): Collection
    {
        return collect(); // kosong
    }

    public function headings(): array
    {
        return [
            'nis','nisn','nik_siswa','nama_siswa','tempat_lahir','tanggal_lahir',
            'jenis_kelamin','kelas','asal_sekolah','hobi','cita_cita','jumlah_saudara',
            'alamat','provinsi','kabupaten','kecamatan','desa','kode_pos','no_kk',
            'nama_ayah','nik_ayah','pendidikan_ayah','pekerjaan_ayah','hp_ayah',
            'nama_ibu','nik_ibu','pendidikan_ibu','pekerjaan_ibu','hp_ibu',
            'no_bpjs','no_pkh','no_kip','npsn_sekolah','no_blanko_skhu',
            'no_seri_ijazah','total_nilai_un','tanggal_kelulusan',
        ];
    }
}
