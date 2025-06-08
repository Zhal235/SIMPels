<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Asrama;

class AsramaSeederWithoutTruncate extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {        // Clear existing data using DELETE instead of TRUNCATE
        DB::table('asrama_anggota')->delete();
        DB::table('asrama')->delete();        $daftar = [
            [
                'kode' => 'ASR-PA-001',
                'nama' => 'Asrama Putra Blok A',
                'wali_asrama' => 'Ustadz Ahmad Fauzi'
            ],
            [
                'kode' => 'ASR-PA-002', 
                'nama' => 'Asrama Putra Blok B',
                'wali_asrama' => 'Ustadz Muhammad Hasan'
            ],
            [
                'kode' => 'ASR-PI-001',
                'nama' => 'Asrama Putri Blok A',
                'wali_asrama' => 'Ustadzah Siti Aisyah'
            ],
            [
                'kode' => 'ASR-PI-002',
                'nama' => 'Asrama Putri Blok B', 
                'wali_asrama' => 'Ustadzah Fatimah Zahra'
            ]
        ];

        foreach ($daftar as $row) {
            Asrama::create($row);
        }
    }
}
