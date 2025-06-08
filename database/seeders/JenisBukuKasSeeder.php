<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JenisBukuKas;

class JenisBukuKasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data - skip truncate to avoid foreign key issues
        // \DB::table('jenis_buku_kas')->truncate();
        
        // Daftar jenis kas yang disesuaikan dengan jenis tagihan baru
        $jenisKasList = [
            [
                'nama' => 'BMP',
                'kode' => 'BMP',
                'deskripsi' => 'Kas untuk penerimaan Biaya Makan & Pemondokan',
                'is_active' => true
            ],
            [
                'nama' => 'Biaya SIMPels',
                'kode' => 'SIM',
                'deskripsi' => 'Kas untuk penerimaan biaya administrasi SIMPels',
                'is_active' => true
            ],
            [
                'nama' => 'Biaya Kas Santri',
                'kode' => 'KAS',
                'deskripsi' => 'Kas untuk penerimaan iuran kas harian santri',
                'is_active' => true
            ],
            [
                'nama' => 'Ujian Semester',
                'kode' => 'UJI',
                'deskripsi' => 'Kas untuk penerimaan biaya ujian semester',
                'is_active' => true
            ],
            [
                'nama' => 'PHBI',
                'kode' => 'PHB',
                'deskripsi' => 'Kas untuk penerimaan biaya Perayaan Hari Besar Islam',
                'is_active' => true
            ],
            [
                'nama' => 'Administrasi Pendaftaran',
                'kode' => 'ADM',
                'deskripsi' => 'Kas untuk penerimaan biaya administrasi pendaftaran',
                'is_active' => true
            ],
            [
                'nama' => 'Mantassa & Khutbatul Arsy',
                'kode' => 'MKA',
                'deskripsi' => 'Kas untuk penerimaan biaya syiar & dakwah',
                'is_active' => true
            ],
            [
                'nama' => 'Pembukaan Akun SIMPels',
                'kode' => 'PAK',
                'deskripsi' => 'Kas untuk penerimaan biaya pembukaan akun SIMPels',
                'is_active' => true
            ],
            [
                'nama' => 'IBSP',
                'kode' => 'IBS',
                'deskripsi' => 'Kas untuk penerimaan Iuran Bantuan Sosial Pondok',
                'is_active' => true
            ],
            [
                'nama' => 'Operasional',
                'kode' => 'OPS',
                'deskripsi' => 'Kas untuk kegiatan operasional sehari-hari',
                'is_active' => true
            ],
            [
                'nama' => 'Cadangan Darurat',
                'kode' => 'CAD',
                'deskripsi' => 'Kas cadangan untuk keperluan darurat',
                'is_active' => true
            ]
        ];
        
        // Tambahkan semua jenis kas ke database tanpa duplikasi
        foreach ($jenisKasList as $jenisKas) {
            JenisBukuKas::updateOrCreate(
                [
                    'nama' => $jenisKas['nama'],
                    'kode' => $jenisKas['kode']
                ],
                $jenisKas
            );
        }
    }
}
