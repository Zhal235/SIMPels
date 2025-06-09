<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriKeuangan;

class KategoriPengeluaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'nama_kategori' => 'Operasional',
                'deskripsi' => 'Biaya operasional sehari-hari',
                'jenis_transaksi' => 'pengeluaran'
            ],
            [
                'nama_kategori' => 'Gaji',
                'deskripsi' => 'Pembayaran gaji karyawan',
                'jenis_transaksi' => 'pengeluaran'
            ],
            [
                'nama_kategori' => 'Pemeliharaan',
                'deskripsi' => 'Biaya pemeliharaan fasilitas',
                'jenis_transaksi' => 'pengeluaran'
            ],
            [
                'nama_kategori' => 'Pembangunan',
                'deskripsi' => 'Biaya pembangunan dan renovasi',
                'jenis_transaksi' => 'pengeluaran'
            ],
            [
                'nama_kategori' => 'ATK',
                'deskripsi' => 'Alat Tulis Kantor',
                'jenis_transaksi' => 'pengeluaran'
            ],
            [
                'nama_kategori' => 'Konsumsi',
                'deskripsi' => 'Biaya konsumsi dan catering',
                'jenis_transaksi' => 'pengeluaran'
            ],
            [
                'nama_kategori' => 'Lainnya',
                'deskripsi' => 'Pengeluaran lain-lain',
                'jenis_transaksi' => 'pengeluaran'
            ]
        ];

        foreach ($categories as $category) {
            KategoriKeuangan::create($category);
        }
    }
}
