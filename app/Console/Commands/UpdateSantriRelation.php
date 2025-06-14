<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateSantriRelation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'santri:update-relation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update santri relation with user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating santri relation...');
        
        // Find santri "Ahmad Santri"
        $santri = Santri::where('nama_santri', 'Ahmad Santri')->first();
        
        if (!$santri) {
            $this->error('Santri "Ahmad Santri" not found!');
            return;
        }
        
        // Find user with email wali@test.com
        $user = User::where('email', 'wali@test.com')->first();
        
        if (!$user) {
            $this->error('User "wali@test.com" not found!');
            return;
        }
        
        $this->info("Found santri: {$santri->nama_santri} (ID: {$santri->id})");
        $this->info("Found user: {$user->email} (ID: {$user->id})");
        
        // Update santri relation
        $santri->update([
            'user_id' => $user->id,
            'wali_santri_id' => $user->id
        ]);
        
        $this->info('✅ Santri relation updated successfully!');
        
        // Verify the update
        $santri->refresh();
        $this->info("Verification:");
        $this->info("- Santri: {$santri->nama_santri}");
        $this->info("- User ID: {$santri->user_id}");
        $this->info("- Wali Santri ID: {$santri->wali_santri_id}");
    }
}
