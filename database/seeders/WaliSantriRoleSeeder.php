<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class WaliSantriRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create wali_santri role if it doesn't exist
        $role = Role::firstOrCreate(['name' => 'wali_santri']);

        // Define permissions for wali_santri role
        $permissions = [
            'view_santri',
            'view_tagihan',
            'view_transaksi',
            'view_keringanan',
            'view_asrama',
            'view_akademik',
            'manage_perizinan',
        ];

        // Create permissions if they don't exist
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Assign permissions to role
        $role->syncPermissions($permissions);

        $this->command->info('Wali Santri role and permissions created successfully');
    }
}
