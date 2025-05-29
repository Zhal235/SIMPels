<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kelas = [
            ['kode' => 'VIIA', 'nama' => 'VII A', 'tingkat' => 'VII' /*, 'keterangan' => 'Kelas 7A' */],
            ['kode' => 'VIIB', 'nama' => 'VII B', 'tingkat' => 'VII' /*, 'keterangan' => 'Kelas 7B' */],
            ['kode' => 'VIIIA', 'nama' => 'VIII A', 'tingkat' => 'VIII' /*, 'keterangan' => 'Kelas 8A' */],
            ['kode' => 'IXA', 'nama' => 'IX A', 'tingkat' => 'IX' /*, 'keterangan' => 'Kelas 9A' */],
        ];

        foreach ($kelas as $kls) {
            Kelas::create($kls);
        }
    }
}
