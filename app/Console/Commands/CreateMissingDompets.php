<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Santri;
use App\Models\Dompet;
use Illuminate\Support\Facades\DB;

class CreateMissingDompets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dompet:create-missing {--force : Force create without confirmation}';

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
        $this->info('Checking for santri without dompet...');

        // Get santri yang belum punya dompet
        $santriWithoutDompet = Santri::whereNotIn('id', function($query) {
            $query->select('pemilik_id')
                  ->from('dompet')
                  ->where('jenis_pemilik', 'santri');
        })->get();

        if ($santriWithoutDompet->count() === 0) {
            $this->info('All santri already have dompet. Nothing to do.');
            return 0;
        }

        $this->info("Found {$santriWithoutDompet->count()} santri without dompet:");
        
        // Show list of santri
        foreach ($santriWithoutDompet as $santri) {
            $this->line("- {$santri->nama_santri} (ID: {$santri->id})");
        }

        // Ask for confirmation unless forced
        if (!$this->option('force')) {
            if (!$this->confirm('Do you want to create dompet for these santri?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Creating dompet for santri...');
        $created = 0;

        DB::beginTransaction();
        try {
            foreach ($santriWithoutDompet as $santri) {
                $this->createDompetForSantri($santri);
                $created++;
                $this->line("✓ Created dompet for {$santri->nama_santri}");
            }

            DB::commit();
            $this->info("Successfully created {$created} dompet!");
            
        } catch (\Exception $e) {
            DB::rollback();
            $this->error("Error creating dompet: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Create dompet for santri
     */
    private function createDompetForSantri(Santri $santri): void
    {
        // Generate nomor dompet dengan format DS[TAHUN][ID_SANTRI]
        $tahunSekarang = date('Y');
        $nomorDompet = 'DS' . $tahunSekarang . str_pad($santri->id, 4, '0', STR_PAD_LEFT);

        // Pastikan nomor dompet unik
        $counter = 1;
        $originalNomor = $nomorDompet;
        while (Dompet::where('nomor_dompet', $nomorDompet)->exists()) {
            $nomorDompet = $originalNomor . '-' . $counter;
            $counter++;
        }

        // Buat dompet baru
        Dompet::create([
            'nomor_dompet' => $nomorDompet,
            'jenis_pemilik' => 'santri',
            'pemilik_id' => $santri->id,
            'saldo' => 0,
            'limit_transaksi' => 500000, // Default limit 500rb
            'is_active' => true,
        ]);
    }
}
