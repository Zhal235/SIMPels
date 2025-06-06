<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
{
    $this->call([
        AdminUserSeeder::class,
        PekerjaanSeeder::class,
        TahunAjaranSeeder::class,
        SantriDummySeeder::class,
        KategoriKeuanganSeeder::class,
        AsramaSeeder::class,
        KelasSeeder::class,
        KelasAnggotaSeeder::class,
        JenisBukuKasSeeder::class,
        JenisTagihanSeeder::class,
        BukuKasSeeder::class,
        TransaksiKasSeeder::class,
    ]);
    }
}
