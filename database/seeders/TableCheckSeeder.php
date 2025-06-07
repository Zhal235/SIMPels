<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TableCheckSeeder extends Seeder
{
    /**
     * Check the tables in the database
     */
    public function run(): void
    {
        $tables = DB::select('SHOW TABLES');
        echo "Tables in the database:\n";
        foreach ($tables as $table) {
            $tableName = array_values(get_object_vars($table))[0];
            echo "- {$tableName}\n";
        }
    }
}
