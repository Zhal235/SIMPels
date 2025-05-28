<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PekerjaanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pekerjaans')->insert([
            ['nama' => 'Petani'],
            ['nama' => 'Guru'],
            ['nama' => 'Wiraswasta'],
            ['nama' => 'PNS'],
            ['nama' => 'Pedagang'],
            ['nama' => 'Nelayan'],
            ['nama' => 'Karyawan Swasta'],
            ['nama' => 'TNI'],
            ['nama' => 'Polri'],
            ['nama' => 'Buruh'],
            ['nama' => 'Tidak Bekerja'],
        ]);
    }
}
