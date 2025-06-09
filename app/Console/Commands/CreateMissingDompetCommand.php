<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Santri;
use App\Models\Dompet;

class CreateMissingDompetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dompet:create-missing {--force : Force create dompet even if already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create dompet for santri who don\'t have one yet';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to create missing dompet for santri...');

        // Get all santri
        $allSantri = Santri::all();
        $this->info("Found {$allSantri->count()} santri in total.");

        $created = 0;
        $skipped = 0;

        foreach ($allSantri as $santri) {
            // Check if santri already has dompet
            $existingDompet = $santri->dompet()->exists();

            if ($existingDompet && !$this->option('force')) {
                $skipped++;
                continue;
            }

            if ($existingDompet && $this->option('force')) {
                // Delete existing dompet if force option is used
                $santri->dompet()->delete();
                $this->warn("Deleted existing dompet for santri: {$santri->nama_santri}");
            }

            // Generate nomor dompet
            $tahunSekarang = date('Y');
            $nomorDompet = 'DS' . $tahunSekarang . str_pad($santri->id, 4, '0', STR_PAD_LEFT);

            // Create dompet
            Dompet::create([
                'nomor_dompet' => $nomorDompet,
                'jenis_pemilik' => 'santri',
                'pemilik_id' => $santri->id,
                'saldo' => 0,
                'limit_transaksi' => null,
                'is_active' => true,
            ]);

            $created++;
            $this->info("Created dompet for santri: {$santri->nama_santri} ({$nomorDompet})");
        }

        $this->info("\n=== Summary ===");
        $this->info("Total santri: {$allSantri->count()}");
        $this->info("Dompet created: {$created}");
        $this->info("Skipped (already has dompet): {$skipped}");
        $this->info('Done!');

        return Command::SUCCESS;
    }
}
