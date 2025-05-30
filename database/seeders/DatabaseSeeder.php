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
        // Seeder lainnya...
    ]);



        // Seeder lainnya
        $this->call([
            PekerjaanSeeder::class,
            SantriDummySeeder::class,
            AsramaSeeder::class,
            KelasSeeder::class,
        ]);
    }
}
