<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pegawai;
use App\Models\Dompet;
use Illuminate\Support\Facades\DB;

class CreateMissingDompetPegawai extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dompet:create-missing-pegawai {--force : Force create without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create dompet for pegawai who don\'t have one yet';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for pegawai without dompet...');

        // Get pegawai yang belum punya dompet dan status aktif
        $pegawaiWithoutDompet = Pegawai::whereNotIn('id', function($query) {
            $query->select('pemilik_id')
                  ->from('dompet')
                  ->where('jenis_pemilik', 'asatidz');
        })->where('status_pegawai', 'Aktif')->get();

        if ($pegawaiWithoutDompet->count() === 0) {
            $this->info('All active pegawai already have dompet. Nothing to do.');
            return 0;
        }

        $this->info("Found {$pegawaiWithoutDompet->count()} pegawai without dompet:");
        
        // Show list of pegawai
        foreach ($pegawaiWithoutDompet as $pegawai) {
            $this->line("- {$pegawai->nama_pegawai} (ID: {$pegawai->id})");
        }

        // Ask for confirmation unless forced
        if (!$this->option('force')) {
            if (!$this->confirm('Do you want to create dompet for these pegawai?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Creating dompet for pegawai...');
        $created = 0;

        DB::beginTransaction();
        try {
            foreach ($pegawaiWithoutDompet as $pegawai) {
                $this->createDompetForPegawai($pegawai);
                $created++;
                $this->line("✓ Created dompet for {$pegawai->nama_pegawai}");
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
     * Create dompet for pegawai
     */
    private function createDompetForPegawai(Pegawai $pegawai): void
    {
        // Generate nomor dompet dengan format DA[TAHUN][ID_PEGAWAI]
        $tahunSekarang = date('Y');
        $nomorDompet = 'DA' . $tahunSekarang . str_pad($pegawai->id, 4, '0', STR_PAD_LEFT);

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
            'jenis_pemilik' => 'asatidz',
            'pemilik_id' => $pegawai->id,
            'saldo' => 0,
            'limit_transaksi' => null, // Asatidz tidak ada limit
            'is_active' => true,
        ]);
    }
}
