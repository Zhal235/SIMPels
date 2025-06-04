<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TahunAjaran;
use Carbon\Carbon;

class TahunAjaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tahunAjarans = [
            [
                'nama_tahun_ajaran' => '2023/2024',
                'tahun_mulai' => 2023,
                'tahun_selesai' => 2024,
                'tanggal_mulai' => Carbon::create(2023, 7, 1),
                'tanggal_selesai' => Carbon::create(2024, 6, 30),
                'is_active' => false,
                'keterangan' => 'Tahun Ajaran 2023/2024'
            ],
            [
                'nama_tahun_ajaran' => '2024/2025',
                'tahun_mulai' => 2024,
                'tahun_selesai' => 2025,
                'tanggal_mulai' => Carbon::create(2024, 7, 1),
                'tanggal_selesai' => Carbon::create(2025, 6, 30),
                'is_active' => true,
                'keterangan' => 'Tahun Ajaran 2024/2025 (Aktif)'
            ],
            [
                'nama_tahun_ajaran' => '2025/2026',
                'tahun_mulai' => 2025,
                'tahun_selesai' => 2026,
                'tanggal_mulai' => Carbon::create(2025, 7, 1),
                'tanggal_selesai' => Carbon::create(2026, 6, 30),
                'is_active' => false,
                'keterangan' => 'Tahun Ajaran 2025/2026'
            ]
        ];

        foreach ($tahunAjarans as $tahunAjaran) {
            TahunAjaran::create($tahunAjaran);
        }
    }
}