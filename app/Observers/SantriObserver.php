<?php

namespace App\Observers;

use App\Models\Santri;
use App\Models\Dompet;

class SantriObserver
{
    /**
     * Handle the Santri "created" event.
     */
    public function created(Santri $santri): void
    {
        // Buat dompet otomatis untuk santri baru
        $this->createDompetForSantri($santri);
    }    /**
     * Create dompet for santri
     */
    private function createDompetForSantri(Santri $santri): void
    {
        // Cek apakah santri sudah memiliki dompet
        if ($santri->dompet()->exists()) {
            return;
        }

        // Generate nomor dompet dengan format DS[TAHUN][ID_SANTRI]
        $tahunSekarang = date('Y');
        $nomorDompet = 'DS' . $tahunSekarang . str_pad($santri->id, 4, '0', STR_PAD_LEFT);

        // Buat dompet baru
        Dompet::create([
            'nomor_dompet' => $nomorDompet,
            'jenis_pemilik' => 'santri',
            'pemilik_id' => $santri->id,
            'saldo' => 0,
            'limit_transaksi' => null,
            'is_active' => true,
        ]);
    }
}
