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
            ['nama' => 'VII A', 'keterangan' => 'Kelas 7A'],
            ['nama' => 'VII B', 'keterangan' => 'Kelas 7B'],
            ['nama' => 'VIII A', 'keterangan' => 'Kelas 8A'],
            ['nama' => 'IX A', 'keterangan' => 'Kelas 9A'],
        ];

        foreach ($kelas as $kls) {
            Kelas::create($kls);
        }
    }
}
