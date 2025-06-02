<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KategoriKeuangan;

class KategoriKeuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'nama_kategori' => 'SPP',
                'deskripsi' => 'Sumbangan Pembinaan Pendidikan bulanan'
            ],
            [
                'nama_kategori' => 'Makan',
                'deskripsi' => 'Biaya makan harian santri'
            ],
            [
                'nama_kategori' => 'Asrama',
                'deskripsi' => 'Biaya penginapan dan fasilitas asrama'
            ],
            [
                'nama_kategori' => 'Laundry',
                'deskripsi' => 'Biaya pencucian pakaian santri'
            ],
            [
                'nama_kategori' => 'Kegiatan',
                'deskripsi' => 'Biaya kegiatan ekstrakurikuler dan pengembangan'
            ],
            [
                'nama_kategori' => 'Kesehatan',
                'deskripsi' => 'Biaya layanan kesehatan dan obat-obatan'
            ]
        ];

        foreach ($categories as $category) {
            KategoriKeuangan::create($category);
        }
    }
}
