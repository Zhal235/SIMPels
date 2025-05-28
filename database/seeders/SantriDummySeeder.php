<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Santri;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SantriDummySeeder extends Seeder
{
    public function run()
    {
        // Hapus data lama
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Santri::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $alamatList = ['Jl. Melati', 'Jl. Mawar', 'Jl. Kenanga', 'Jl. Anggrek', 'Jl. Dahlia', 'Jl. Kamboja', 'Jl. Flamboyan'];
        $ayahNames = ['Budi', 'Joko', 'Samsul', 'Andi', 'Syahrul', 'Soleh', 'Imam', 'Rohmat', 'Tono', 'Fauzi', 'Wahyudi', 'Herman', 'Eko', 'Ahmad', 'Ramli'];
        $ibuNames = ['Siti', 'Aisyah', 'Nani', 'Fitri', 'Nurul', 'Lina', 'Yanti', 'Dewi', 'Farida', 'Astuti', 'Rahma', 'Laila', 'Fatimah', 'Rini', 'Aulia'];

        $tempatLahirList = ['Cianjur', 'Bandung', 'Jakarta', 'Tasikmalaya', 'Bogor', 'Garut', 'Depok', 'Bekasi', 'Sukabumi', 'Purwakarta'];

        $laki = ['Ahmad', 'Muhammad', 'Rizky', 'Fahmi', 'Bagas', 'Aldi', 'Irfan', 'Rafi', 'Bima', 'Agus', 'Putra', 'Adit', 'Reza', 'Fajar', 'Farhan', 'Dimas', 'Rendy', 'Rizal', 'Hafiz', 'Naufal'];
        $perempuan = ['Aulia', 'Putri', 'Zahra', 'Laila', 'Nabila', 'Aisyah', 'Salsabila', 'Nisa', 'Safira', 'Indah', 'Intan', 'Rahma', 'Dewi', 'Alya', 'Maya', 'Yuliana', 'Rani', 'Lina', 'Farah', 'Siti'];

        $nis = 1001;

        for ($i = 1; $i <= 200; $i++) {
            $jenis_kelamin = $i % 2 == 0 ? 'L' : 'P'; // Acak L/P
            $nama = $jenis_kelamin == 'L'
                ? $laki[array_rand($laki)] . ' ' . Str::random(5)
                : $perempuan[array_rand($perempuan)] . ' ' . Str::random(5);

            // Tanggal lahir random antara 2006-2012
            $tanggal_lahir = Carbon::create(rand(2006, 2012), rand(1, 12), rand(1, 28))->format('Y-m-d');

            Santri::create([
                'nis'           => $nis++,
                'nama_siswa'    => $nama,
                'jenis_kelamin' => $jenis_kelamin,
                'tempat_lahir'  => $tempatLahirList[array_rand($tempatLahirList)],
                'tanggal_lahir' => $tanggal_lahir,
                'alamat'        => $alamatList[array_rand($alamatList)],
                'nama_ayah'     => $ayahNames[array_rand($ayahNames)],
                'nama_ibu'      => $ibuNames[array_rand($ibuNames)]
            ]);
        }
    }
}
