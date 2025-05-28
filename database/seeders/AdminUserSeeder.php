<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'], // Ganti email sesuai kebutuhan
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'), // Ganti password sesuai kebutuhan
                'email_verified_at' => now(),         // Jika ada kolom ini
            ]
        );
    }
}
