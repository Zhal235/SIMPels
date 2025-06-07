<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResetAndSeedDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with fresh data.
     * This seeder disables foreign key checks, truncates tables, and then seeds in proper order.
     */
    public function run(): void
    {
        // Disable foreign key checks to allow truncation
        DB::statement('SET FOREIGN_KEY_CHECKS=0');        // Truncate all relevant tables in reverse dependency order
        $this->command->info('Truncating all tables...');
        DB::table('pembayaran_santris')->truncate();
        DB::table('tagihan_santris')->truncate();
        DB::table('jenis_tagihan_kelas')->truncate();
        DB::table('santris')->truncate();
        DB::table('jenis_tagihans')->truncate();
        DB::table('keuangan_kategoris')->truncate();
        DB::table('kelas')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $this->command->info('All tables truncated. Starting seed process...');

        // Run seeders in dependency order
        $this->call([
            TahunAjaranSeeder::class,
            KelasSeeder::class,
            KategoriTagihanSeeder::class,
            JenisTagihanSeeder::class,
            TarifPerKelasSeeder::class,
            SantriSeeder::class,
            TagihanSantriSeeder::class,
            PembayaranSeeder::class,
        ]);
        
        $this->command->info('Database seeding completed successfully!');
    }
}
