<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan role 'admin' ada
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        
        // Buat permission untuk setiap menu
        $permissionNames = [
            'access admin area',
            'manage santris',
            'manage kelas',
            'manage asrama',
            'manage mutasi',
            'manage rfid',
            'manage pembayaran',
            'manage setting pembayaran',
            'manage bukti transfer',
            'manage limit tarik tabungan',
            'manage tabungan santri',
            'manage kas bank',
            'manage penggajian',
            'manage hutang',
            'manage kirim tagihan',
            'manage users',
            'manage roles'
        ];
        
        $permissions = [];
        foreach ($permissionNames as $permissionName) {
            $permissions[] = Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }
        
        // Assign all permissions to the admin role
        $role->syncPermissions($permissions);

        // Buat atau update user admin
        $user = User::updateOrCreate(
            ['email' => 'admin@example.com'], // Ganti email jika perlu
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'), // Ganti password jika perlu
                'email_verified_at' => now(),
            ]
        );

        // Tugaskan role admin ke user
        // Pastikan user belum memiliki role tersebut sebelum menugaskannya
        if (!$user->hasRole('admin')) {
            $user->assignRole($role);
        }
    }
}
