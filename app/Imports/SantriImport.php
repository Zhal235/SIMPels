<?php

namespace App\Imports;

use App\Models\Santri;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date; // <-- Penting untuk konversi tanggal

class SantriImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Convert tanggal lahir & tanggal kelulusan
        $tanggal_lahir     = $this->convertExcelDate($row['tanggal_lahir'] ?? null);
        $tanggal_kelulusan = $this->convertExcelDate($row['tanggal_kelulusan'] ?? null);

        return new Santri([
            'nis'               => $row['nis'] ?? null,
            'nisn'              => $row['nisn'] ?? null,
            'nik_santri'        => $row['nik_santri'] ?? null,
            'nama_santri'       => $row['nama_santri'] ?? null,
            'tempat_lahir'      => $row['tempat_lahir'] ?? null,
            'tanggal_lahir'     => $tanggal_lahir,
            'jenis_kelamin'     => $row['jenis_kelamin'] ?? null,
            'kelas'             => $row['kelas'] ?? null,
            'asal_sekolah'      => $row['asal_sekolah'] ?? null,
            'hobi'              => $row['hobi'] ?? null,
            'cita_cita'         => $row['cita_cita'] ?? null,
            'jumlah_saudara'    => $row['jumlah_saudara'] ?? null,
            'alamat' => !empty($row['alamat']) ? $row['alamat'] : '-',
            'provinsi'          => $row['provinsi'] ?? null,
            'kabupaten'         => $row['kabupaten'] ?? null,
            'kecamatan'         => $row['kecamatan'] ?? null,
            'desa'              => $row['desa'] ?? null,
            'kode_pos'          => $row['kode_pos'] ?? null,
            'no_kk'             => $row['no_kk'] ?? null,
            'nama_ayah'         => $row['nama_ayah'] ?? null,
            'nik_ayah'          => $row['nik_ayah'] ?? null,
            'pendidikan_ayah'   => $row['pendidikan_ayah'] ?? null,
            'pekerjaan_ayah'    => $row['pekerjaan_ayah'] ?? null,
            'hp_ayah'           => $row['hp_ayah'] ?? null,
            'nama_ibu'          => $row['nama_ibu'] ?? null,
            'nik_ibu'           => $row['nik_ibu'] ?? null,
            'pendidikan_ibu'    => $row['pendidikan_ibu'] ?? null,
            'pekerjaan_ibu'     => $row['pekerjaan_ibu'] ?? null,
            'hp_ibu'            => $row['hp_ibu'] ?? null,
            'no_bpjs'           => $row['no_bpjs'] ?? null,
            'no_pkh'            => $row['no_pkh'] ?? null,
            'no_kip'            => $row['no_kip'] ?? null,
            'npsn_sekolah'      => $row['npsn_sekolah'] ?? null,
            'no_blanko_skhu'    => $row['no_blanko_skhu'] ?? null,
            'no_seri_ijazah'    => $row['no_seri_ijazah'] ?? null,
            'total_nilai_un'    => $row['total_nilai_un'] ?? null,
            'tanggal_kelulusan' => $tanggal_kelulusan,
        ]);
    }

    /** Helper untuk konversi Excel serial number/date string */
    private function convertExcelDate($value)
    {
        // Kalau kosong
        if (empty($value)) return null;

        // Kalau string dan sudah format tanggal
        if (is_string($value) && strtotime($value)) {
            return date('Y-m-d', strtotime($value));
        }
        // Kalau numeric (Excel serial)
        if (is_numeric($value)) {
            try {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }
        return null;
    }
}
