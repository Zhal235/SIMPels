<?php

namespace App\Observers;

use App\Models\Pegawai;
use App\Models\Dompet;

class PegawaiObserver
{
    /**
     * Handle the Pegawai "created" event.
     */
    public function created(Pegawai $pegawai): void
    {
        // Buat dompet otomatis untuk pegawai baru
        $this->createDompetForPegawai($pegawai);
    }

    /**
     * Create dompet for pegawai
     */
    private function createDompetForPegawai(Pegawai $pegawai): void
    {
        // Cek apakah pegawai sudah memiliki dompet
        if ($pegawai->dompet()->exists()) {
            return;
        }

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
        
        \Log::info("Auto-created dompet for pegawai: {$pegawai->nama_pegawai} (ID: {$pegawai->id}) with number: {$nomorDompet}");
    }
}
