<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BukuKas;
use App\Models\JenisBukuKas;
use Carbon\Carbon;

class BukuKasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan jenis kas sudah ada
        $jenisKasSPP = JenisBukuKas::where('nama', 'SPP')->first();
        $jenisKasUangGedung = JenisBukuKas::where('nama', 'Uang Gedung')->first();
        $jenisKasSeragam = JenisBukuKas::where('nama', 'Seragam')->first();
        $jenisKasOperasional = JenisBukuKas::where('nama', 'Operasional')->first();
        $jenisKasPembangunan = JenisBukuKas::where('nama', 'Pembangunan')->first();
        $jenisKasInsidental = JenisBukuKas::where('nama', 'Insidental')->first();
        $jenisKasCadangan = JenisBukuKas::where('nama', 'Cadangan Darurat')->first();

        $bukuKas = [
            [
                'nama_kas' => 'Kas SPP Santri',
                'kode_kas' => 'SPP-001',
                'deskripsi' => 'Kas khusus untuk penerimaan pembayaran SPP bulanan santri',
                'jenis_kas_id' => $jenisKasSPP?->id,
                'saldo_awal' => 50000000.00,
                'saldo_saat_ini' => 45000000.00,
                'is_active' => true
            ],
            [
                'nama_kas' => 'Kas Uang Gedung',
                'kode_kas' => 'UG-001',
                'deskripsi' => 'Kas untuk penerimaan uang gedung santri baru',
                'jenis_kas_id' => $jenisKasUangGedung?->id,
                'saldo_awal' => 25000000.00,
                'saldo_saat_ini' => 20000000.00,
                'is_active' => true
            ],
            [
                'nama_kas' => 'Kas Seragam Santri',
                'kode_kas' => 'SRG-001',
                'deskripsi' => 'Kas untuk penerimaan pembayaran seragam santri',
                'jenis_kas_id' => $jenisKasSeragam?->id,
                'saldo_awal' => 10000000.00,
                'saldo_saat_ini' => 8500000.00,
                'is_active' => true
            ],
            [
                'nama_kas' => 'Kas Operasional Harian',
                'kode_kas' => 'OPS-001',
                'deskripsi' => 'Kas untuk keperluan operasional sehari-hari pesantren',
                'jenis_kas_id' => $jenisKasOperasional?->id,
                'saldo_awal' => 15000000.00,
                'saldo_saat_ini' => 12000000.00,
                'is_active' => true
            ],
            [
                'nama_kas' => 'Kas Pembangunan Gedung',
                'kode_kas' => 'PBG-001',
                'deskripsi' => 'Kas khusus untuk dana pembangunan dan renovasi gedung pesantren',
                'jenis_kas_id' => $jenisKasPembangunan?->id,
                'saldo_awal' => 100000000.00,
                'saldo_saat_ini' => 85000000.00,
                'is_active' => true
            ],
            [
                'nama_kas' => 'Kas Kegiatan Insidental',
                'kode_kas' => 'INS-001',
                'deskripsi' => 'Kas untuk kegiatan insidental dan tidak terduga',
                'jenis_kas_id' => $jenisKasInsidental?->id,
                'saldo_awal' => 5000000.00,
                'saldo_saat_ini' => 3500000.00,
                'is_active' => true
            ],
            [
                'nama_kas' => 'Kas Cadangan Darurat',
                'kode_kas' => 'CAD-001',
                'deskripsi' => 'Kas cadangan untuk keperluan darurat',
                'jenis_kas_id' => $jenisKasCadangan?->id,
                'saldo_awal' => 10000000.00,
                'saldo_saat_ini' => 10000000.00,
                'is_active' => true
            ]
        ];

        // Gunakan updateOrCreate untuk mencegah duplikasi
        foreach ($bukuKas as $kas) {
            if ($kas['jenis_kas_id']) { // Pastikan jenis kas ada
                BukuKas::updateOrCreate(
                    ['nama_kas' => $kas['nama_kas']], // Gunakan nama_kas sebagai unique identifier
                    $kas
                );
            }
        }
    }
}
