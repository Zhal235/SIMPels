<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TahunAjaran;
use App\Services\TagihanService;
use Carbon\Carbon;

class CopyRoutineTagihanToNewYear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tagihan:copy-routine {--target-year-id=} {--source-year-id=} {--confirm}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy routine tagihan from previous academic year to new academic year for continuing santri';

    protected $tagihanService;

    public function __construct(TagihanService $tagihanService)
    {
        parent::__construct();
        $this->tagihanService = $tagihanService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetYearId = $this->option('target-year-id');
        $sourceYearId = $this->option('source-year-id');
        $confirm = $this->option('confirm');

        // Get target academic year (default to active year)
        if ($targetYearId) {
            $targetYear = TahunAjaran::find($targetYearId);
            if (!$targetYear) {
                $this->error('Target academic year not found.');
                return 1;
            }
        } else {
            $targetYear = TahunAjaran::where('is_active', true)->first();
            if (!$targetYear) {
                $this->error('No active academic year found. Please specify --target-year-id.');
                return 1;
            }
        }

        // Get source academic year (default to previous year)
        if ($sourceYearId) {
            $sourceYear = TahunAjaran::find($sourceYearId);
            if (!$sourceYear) {
                $this->error('Source academic year not found.');
                return 1;
            }
        } else {
            $sourceYear = TahunAjaran::where('tahun_mulai', '<', $targetYear->tahun_mulai)
                ->orderBy('tahun_mulai', 'desc')
                ->first();
                
            if (!$sourceYear) {
                $this->error('No previous academic year found to copy from.');
                return 1;
            }
        }

        $this->info("Copying routine tagihan:");
        $this->info("  From: {$sourceYear->nama} (ID: {$sourceYear->id})");
        $this->info("  To:   {$targetYear->nama} (ID: {$targetYear->id})");
        $this->newLine();

        // Show confirmation unless --confirm flag is used
        if (!$confirm) {
            if (!$this->confirm('Do you want to continue with copying routine tagihan?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Starting to copy routine tagihan...');
        $this->newLine();

        // Use the TagihanService to copy routine tagihan
        $result = $this->tagihanService->copyRoutineTagihanToNewYear($targetYear, $sourceYear);

        if ($result['success']) {
            $this->info('✅ ' . $result['message']);
            $this->newLine();
            
            $this->table(['Metric', 'Count'], [
                ['Tagihan Copied', $result['copied_count']],
                ['Tagihan Skipped', $result['skipped_count'] ?? 0],
                ['Source Year', $result['source_year']],
                ['Target Year', $result['target_year']],
            ]);
            
            $this->info('Routine tagihan copy completed successfully!');
        } else {
            $this->error('❌ ' . $result['message']);
            return 1;
        }

        return 0;
    }
}
