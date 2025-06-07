<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CheckDatabaseTableSeeder extends Seeder
{
    /**
     * Check tagihan related tables in the database
     */
    public function run(): void
    {
        $tables = DB::select("SHOW TABLES LIKE '%tagihan%'");
        echo "Tagihan related tables in the database:\n";
        foreach ($tables as $table) {
            $tableName = array_values(get_object_vars($table))[0];
            echo "- {$tableName}\n";
        }
        
        echo "\nPembayaran related tables in the database:\n";
        $tables = DB::select("SHOW TABLES LIKE '%pembayaran%'");
        foreach ($tables as $table) {
            $tableName = array_values(get_object_vars($table))[0];
            echo "- {$tableName}\n";
        }
    }
}
