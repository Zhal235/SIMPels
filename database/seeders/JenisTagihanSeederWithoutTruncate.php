<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\JenisTagihan;
use App\Models\Kelas;
use App\Models\TahunAjaran;

class JenisTagihanSeederWithoutTruncate extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear without truncate
        DB::table('jenis_tagihan_kelas')->delete();
        DB::table('jenis_tagihans')->delete();

        $tahun = TahunAjaran::where('is_active', true)->first();

        // months: July→June
        $bulanan = ['07','08','09','10','11','12','01','02','03','04','05','06'];

        $types = [
            ['nama'=>'BMP',                    'is_bulanan'=>true,  'kategori_tagihan'=>'Rutin',      'deskripsi'=>'Biaya Makan & Pemondokan'],
            ['nama'=>'Biaya SIMPels',           'is_bulanan'=>true,  'kategori_tagihan'=>'Rutin',      'deskripsi'=>'Iuran SIMPels bulanan'],
            ['nama'=>'Biaya Kas Santri',        'is_bulanan'=>true,  'kategori_tagihan'=>'Rutin',      'deskripsi'=>'Iuran kas harian santri'],
            ['nama'=>'Ujian Semester 1',        'is_bulanan'=>false, 'kategori_tagihan'=>'Insidental','deskripsi'=>'Biaya Ujian Semester 1'],
            ['nama'=>'PHBI',                    'is_bulanan'=>false, 'kategori_tagihan'=>'Insidental','deskripsi'=>'Perayaan Hari Besar Islam'],
            ['nama'=>'Administrasi Pendaftaran','is_bulanan'=>false, 'kategori_tagihan'=>'Insidental','deskripsi'=>'Biaya pendaftaran santri baru'],
            ['nama'=>'Mantassa & Khutbatul Arsy','is_bulanan'=>false,'kategori_tagihan'=>'Insidental','deskripsi'=>'Biaya syiar & dakwah'],
            ['nama'=>'Pembukaan Akun SIMPels',   'is_bulanan'=>false,'kategori_tagihan'=>'Insidental','deskripsi'=>'Pembuatan akun SIMPels'],
            ['nama'=>'IBSP',                    'is_bulanan'=>true,  'kategori_tagihan'=>'Rutin',      'deskripsi'=>'Iuran Bantuan Sosial Pondok'],
        ];

        foreach ($types as $t) {
            $payload = [
                'nama'            => $t['nama'],
                'nominal'         => 0,
                'is_bulanan'      => $t['is_bulanan'],
                'bulan_pembayaran'=> $t['is_bulanan'] ? $bulanan : null,
                'deskripsi'       => $t['deskripsi'],
                'tahun_ajaran_id' => $tahun?->id,
                'kategori_tagihan'=> $t['kategori_tagihan'],
                'is_nominal_per_kelas' => true,
            ];
            JenisTagihan::create($payload);
        }
    }
}
