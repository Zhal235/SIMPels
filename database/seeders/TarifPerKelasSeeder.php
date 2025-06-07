<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Kelas;
use App\Models\JenisTagihan;

class TarifPerKelasSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jenis_tagihan_kelas')->truncate();

        // mapping kelas.kode → tarif
        $tarif = [
            '7A'      => 550000,
            '7B'      => 550000,
            '8A'      => 530000,
            '8B'      => 530000,
            '9A'      => 530000,
            '9B'      => 530000,
            '10 TKJ'  => 550000,
            '11 TKJ'  => 530000,
            '12 TKJ'  => 530000,
        ];

        $kelasAll = Kelas::all();
        $jenisAll = JenisTagihan::where('is_nominal_per_kelas', true)->get();

        foreach ($jenisAll as $jenis) {
            foreach ($kelasAll as $kelas) {
                $nom = $tarif[$kelas->kode] ?? 0;
                DB::table('jenis_tagihan_kelas')->insert([
                    'jenis_tagihan_id' => $jenis->id,
                    'kelas_id'         => $kelas->id,
                    'nominal'          => $nom,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }
        }
    }
}
