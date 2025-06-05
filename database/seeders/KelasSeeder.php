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
        // Clear existing data first
        \DB::table('kelas')->delete();
        
        $kelas = [
            ['kode' => "'VIIA'", 'nama' => "'VII A'", 'tingkat' => "'VII'"],
            ['kode' => "'VIIB'", 'nama' => "'VII B'", 'tingkat' => "'VII'"],
            ['kode' => "'VIIIA'", 'nama' => "'VIII A'", 'tingkat' => "'VIII'"],
            ['kode' => "'IXA'", 'nama' => "'IX A'", 'tingkat' => "'IX'"],
        ];

        foreach ($kelas as $kls) {
            Kelas::create($kls);
        }
    }
}
