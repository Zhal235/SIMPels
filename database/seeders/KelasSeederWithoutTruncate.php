<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Kelas;

class KelasSeederWithoutTruncate extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data using DELETE instead of TRUNCATE
        DB::table('kelas')->delete();

        $daftar = [
            ['kode'=>'7A',     'nama'=>'7A',      'tingkat'=>'7'],
            ['kode'=>'7B',     'nama'=>'7B',      'tingkat'=>'7'],
            ['kode'=>'8A',     'nama'=>'8A',      'tingkat'=>'8'],
            ['kode'=>'8B',     'nama'=>'8B',      'tingkat'=>'8'],
            ['kode'=>'9A',     'nama'=>'9A',      'tingkat'=>'9'],
            ['kode'=>'9B',     'nama'=>'9B',      'tingkat'=>'9'],
            ['kode'=>'10 TKJ', 'nama'=>'10 TKJ',  'tingkat'=>'10'],
            ['kode'=>'11 TKJ', 'nama'=>'11 TKJ',  'tingkat'=>'11'],
            ['kode'=>'12 TKJ', 'nama'=>'12 TKJ',  'tingkat'=>'12'],
        ];

        foreach ($daftar as $row) {
            Kelas::create($row);
        }
    }
}
