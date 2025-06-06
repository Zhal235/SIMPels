<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat roles dasar
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'guru']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'staff']);
        
        // Menambahkan user admin default jika belum ada
        $admin = \App\Models\User::firstOrCreate([
            'email' => 'admin@simpels.com'
        ], [
            'name' => 'Administrator',
            'password' => bcrypt('password')
        ]);
        
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }
    }
}
