<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivateExistingClassesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('kelas_anggota')->update(['is_active' => true]);
    }
}
