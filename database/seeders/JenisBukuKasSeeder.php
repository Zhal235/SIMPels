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
        // Daftar jenis kas yang disesuaikan dengan jenis tagihan yang ada
        $jenisKasList = [
            [
                'nama' => 'SPP',
                'kode' => 'SPP',
                'deskripsi' => 'Kas untuk penerimaan SPP bulanan santri',
                'is_active' => true
            ],
            [
                'nama' => 'Uang Gedung',
                'kode' => 'UG',
                'deskripsi' => 'Kas untuk penerimaan uang gedung santri baru',
                'is_active' => true
            ],
            [
                'nama' => 'Seragam',
                'kode' => 'SRG',
                'deskripsi' => 'Kas untuk penerimaan biaya seragam santri',
                'is_active' => true
            ],
            [
                'nama' => 'Operasional',
                'kode' => 'OPS',
                'deskripsi' => 'Kas untuk kegiatan operasional sehari-hari',
                'is_active' => true
            ],
            [
                'nama' => 'Pembangunan',
                'kode' => 'PBG',
                'deskripsi' => 'Kas untuk kegiatan pembangunan dan pengembangan infrastruktur',
                'is_active' => true
            ],
            [
                'nama' => 'Insidental',
                'kode' => 'INS',
                'deskripsi' => 'Kas untuk kegiatan insidental/tidak rutin',
                'is_active' => true
            ],
            [
                'nama' => 'Cadangan Darurat',
                'kode' => 'CAD',
                'deskripsi' => 'Kas cadangan untuk keperluan darurat',
                'is_active' => true
            ]
        ];
        
        // Tambahkan semua jenis kas ke database
        foreach ($jenisKasList as $jenisKas) {
            // Gunakan updateOrCreate untuk mencegah duplikasi
            JenisBukuKas::updateOrCreate(
                ['nama' => $jenisKas['nama']],
                $jenisKas
            );
        }
    }
}
