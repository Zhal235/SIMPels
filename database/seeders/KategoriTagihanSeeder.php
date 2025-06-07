<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\KategoriKeuangan;

class KategoriTagihanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('keuangan_kategoris')->truncate();

        $list = [
            ['nama_kategori'=>'BMP',               'deskripsi'=>'Biaya Makan & Pemondokan'],
            ['nama_kategori'=>'Biaya SIMPels',      'deskripsi'=>'Biaya administrasi SIMPels'],
            ['nama_kategori'=>'Biaya Kas Santri',   'deskripsi'=>'Iuran kas harian santri'],
            ['nama_kategori'=>'Ujian Semester 1',   'deskripsi'=>'Biaya Ujian Semester 1'],
            ['nama_kategori'=>'PHBI',               'deskripsi'=>'Perayaan Hari Besar Islam'],
            ['nama_kategori'=>'Administrasi Pendaftaran','deskripsi'=>'Biaya pendaftaran santri baru'],
            ['nama_kategori'=>'Mantassa & Khutbatul Arsy','deskripsi'=>'Biaya syiar & dakwah'],
            ['nama_kategori'=>'Pembukaan Akun SIMPels',   'deskripsi'=>'Pembuatan akun SIMPels'],
            ['nama_kategori'=>'IBSP',               'deskripsi'=>'Iuran Bantuan Sosial Pondok'],
        ];

        foreach ($list as $cat) {
            KategoriKeuangan::create($cat);
        }
    }
}
