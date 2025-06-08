<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BukuKas;
use Illuminate\Support\Facades\DB;

class BukuKasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('buku_kas')->delete();

        
        $bukuKasData = [
            [
                'nama_kas' => 'BMP',
                'kode_kas' => 'BMP001',
                'deskripsi' => 'Buku Kas Bantuan Modal Pendidikan',
                'jenis_kas_id' => null, // Will be set if JenisBukuKas exists
                'saldo_awal' => 0,
                'saldo_saat_ini' => 0,
                'is_active' => true
            ],
            [
                'nama_kas' => 'SiMPelS',
                'kode_kas' => 'SMP001',
                'deskripsi' => 'Buku Kas Sistem Manajemen Pelayanan Santri',
                'jenis_kas_id' => null,
                'saldo_awal' => 0,
                'saldo_saat_ini' => 0,
                'is_active' => true
            ],
            [
                'nama_kas' => 'Kas Santri',
                'kode_kas' => 'KS001',
                'deskripsi' => 'Buku Kas Khusus untuk Keperluan Santri',
                'jenis_kas_id' => null,
                'saldo_awal' => 0,
                'saldo_saat_ini' => 0,
                'is_active' => true
            ],
            [
                'nama_kas' => 'Panitia Ujian',
                'kode_kas' => 'PU001',
                'deskripsi' => 'Buku Kas untuk Keperluan Pelaksanaan Ujian',
                'jenis_kas_id' => null,
                'saldo_awal' => 0,
                'saldo_saat_ini' => 0,
                'is_active' => true
            ],
            [
                'nama_kas' => 'Panitia PSB',
                'kode_kas' => 'PSB001',
                'deskripsi' => 'Buku Kas untuk Penerimaan Santri Baru',
                'jenis_kas_id' => null,
                'saldo_awal' => 0,
                'saldo_saat_ini' => 0,
                'is_active' => true
            ],
            [
                'nama_kas' => 'Panitia PHBI',
                'kode_kas' => 'PHBI001',
                'deskripsi' => 'Buku Kas untuk Peringatan Hari Besar Islam',
                'jenis_kas_id' => null,
                'saldo_awal' => 0,
                'saldo_saat_ini' => 0,
                'is_active' => true
            ]
        ];

        foreach ($bukuKasData as $data) {
            BukuKas::create($data);
        }

        echo "Created " . count($bukuKasData) . " buku kas records.\n";
    }
}
