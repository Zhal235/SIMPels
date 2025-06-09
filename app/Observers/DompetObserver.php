<?php

namespace App\Observers;

use App\Models\Dompet;
use App\Models\DompetLimit;

class DompetObserver
{
    /**
     * Handle the Dompet "created" event.
     */    public function created(Dompet $dompet): void
    {
        // Otomatis buat limit default untuk dompet baru
        // Default limit harian 50rb untuk santri, 100rb untuk asatidz
        $defaultLimit = $dompet->jenis_pemilik === 'santri' ? 50000 : 100000;
        
        DompetLimit::create([
            'dompet_id' => $dompet->id,
            'limit_harian' => $defaultLimit,
            'limit_transaksi' => $defaultLimit, // Sama dengan limit harian
            'limit_mingguan' => $defaultLimit * 7, // 7x limit harian
            'limit_bulanan' => $defaultLimit * 30, // 30x limit harian
            'is_active' => true,
        ]);
    }

    /**
     * Handle the Dompet "updated" event.
     */
    public function updated(Dompet $dompet): void
    {
        //
    }

    /**
     * Handle the Dompet "deleted" event.
     */
    public function deleted(Dompet $dompet): void
    {
        // Hapus limit ketika dompet dihapus
        $dompet->dompetLimit()?->delete();
    }

    /**
     * Handle the Dompet "restored" event.
     */
    public function restored(Dompet $dompet): void
    {
        //
    }

    /**
     * Handle the Dompet "force deleted" event.
     */
    public function forceDeleted(Dompet $dompet): void
    {
        // Hapus limit ketika dompet force deleted
        $dompet->dompetLimit()?->delete();
    }
}
