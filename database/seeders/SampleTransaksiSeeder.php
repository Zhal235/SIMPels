<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TagihanSantri;
use App\Models\Transaksi;
use App\Models\TahunAjaran;

class SampleTransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeTahunAjaran = TahunAjaran::where('is_active', 1)->first();
        
        if (!$activeTahunAjaran) {
            echo "Tidak ada tahun ajaran aktif\n";
            return;
        }

        // Ambil beberapa tagihan untuk dibuat pembayaran parsial
        $tagihanSantris = TagihanSantri::take(5)->get();

        $count = 0;
        
        foreach ($tagihanSantris as $tagihan) {
            // Buat pembayaran parsial (50% dari nominal tagihan)
            $nominalPembayaran = $tagihan->nominal_tagihan * 0.5;
              $transaksi = Transaksi::create([
                'santri_id' => $tagihan->santri_id,
                'tagihan_santri_id' => $tagihan->id,
                'tipe_pembayaran' => 'cicilan',
                'nominal' => $nominalPembayaran,
                'tanggal' => now(),
                'keterangan' => 'Pembayaran parsial 50%',
                'tahun_ajaran_id' => $activeTahunAjaran->id
            ]);
            
            // Update nominal_dibayar di TagihanSantri
            $tagihan->update([
                'nominal_dibayar' => $nominalPembayaran
            ]);
            
            $count++;
            echo "Pembayaran parsial untuk tagihan ID {$tagihan->id}: Rp " . number_format($nominalPembayaran) . "\n";
        }

        echo "SELESAI: Created {$count} pembayaran parsial\n";
    }
}
