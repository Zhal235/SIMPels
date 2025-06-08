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
            AsramaSeederWithoutTruncate::class,
            KelasSeederWithoutTruncate::class,
            KategoriTagihanSeederWithoutTruncate::class,
            JenisTagihanSeederWithoutTruncate::class,
            TarifPerKelasSeederWithoutTruncate::class,
            SantriSeeder::class,
            // Skip TagihanSantriSeeder and PembayaranSeeder for now due to FK constraints
        ]);
    }
}
