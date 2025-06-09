<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisBukuKas;
use App\Models\BukuKas;

class DompetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tambah Jenis Buku Kas untuk Dompet
        $jenisDompet = [
            [
                'nama' => 'Dompet Digital',
                'kode' => 'DOMPET',
                'deskripsi' => 'Buku kas untuk mengelola dompet digital santri dan asatidz',
                'is_active' => true
            ]
        ];

        foreach ($jenisDompet as $jenis) {
            JenisBukuKas::firstOrCreate(
                ['nama' => $jenis['nama']],
                $jenis
            );
        }

        // 2. Tambah Buku Kas untuk Dompet
        $jenisDompetDigital = JenisBukuKas::where('nama', 'Dompet Digital')->first();

        $bukuKasDompet = [
            [
                'jenis_kas_id' => $jenisDompetDigital->id,
                'nama_kas' => 'Dompet Santri',
                'kode_kas' => 'DS',
                'deskripsi' => 'Buku kas untuk mengelola semua transaksi dompet santri',
                'saldo_awal' => 0,
                'saldo_saat_ini' => 0,
                'is_active' => true
            ],
            [
                'jenis_kas_id' => $jenisDompetDigital->id,
                'nama_kas' => 'Dompet Asatidz',
                'kode_kas' => 'DA',
                'deskripsi' => 'Buku kas untuk mengelola semua transaksi dompet asatidz',
                'saldo_awal' => 0,
                'saldo_saat_ini' => 0,
                'is_active' => true
            ]
        ];

        foreach ($bukuKasDompet as $buku) {
            BukuKas::firstOrCreate(
                ['nama_kas' => $buku['nama_kas']],
                $buku
            );
        }
    }
}
