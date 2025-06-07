<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Santri;
use App\Models\Kelas;
use Faker\Factory as Faker;

class SantriSeeder extends Seeder
{    public function run(): void
    {
        // Clear existing data without truncate
        // Use delete instead of truncate to respect foreign key constraints
        DB::table('santris')->delete();

        $faker = Faker::create('id_ID');
        $kelasList = Kelas::all();

        // Ensure we have at least 100 santri (about 12-15 per kelas)
        for ($i = 0; $i < 100; $i++) {
            $kelas = $kelasList->random();
            
            // determine gender based on kelas
            if (str_contains($kelas->kode, 'TKJ')) {
                $gender = $faker->randomElement(['putra','putri']);
            } else {
                $gender = str_ends_with($kelas->kode, 'A') ? 'putra' : 'putri';
            }
              // Create default values for required fields
            Santri::create([
                'nama_santri'   => $faker->name($gender=='putra'?'male':'female'),
                'jenis_kelamin' => $gender == 'putra' ? 'L' : 'P',
                'kelas_id'      => $kelas->id,
                'tanggal_lahir' => $faker->dateTimeBetween('-18 years', '-12 years')->format('Y-m-d'),
                'tempat_lahir'  => $faker->city,
                'alamat'        => $faker->address,
                'status'        => 'aktif',
                'nis'           => 'S' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'nama_ayah'     => $faker->name('male'),
                'nama_ibu'      => $faker->name('female'),
                'hp_ayah'       => $faker->phoneNumber,
                'hp_ibu'        => $faker->phoneNumber,
            ]);
        }
    }
}
