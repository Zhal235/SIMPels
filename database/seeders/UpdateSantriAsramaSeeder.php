<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asrama;
use App\Models\Santri;
use Illuminate\Support\Facades\DB;

class UpdateSantriAsramaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset asrama_id untuk semua santri
        Santri::query()->update(['asrama_id' => null]);

        // Ambil semua asrama yang ada
        $asramaPutra = Asrama::where('kode', 'like', '%-PA-%')->get(); // Asrama Putra
        $asramaPutri = Asrama::where('kode', 'like', '%-PI-%')->get(); // Asrama Putri
        
        // Ambil semua santri aktif berdasarkan jenis kelamin
        $santriPutra = Santri::where('status', 'aktif')->where('jenis_kelamin', 'L')->get();
        $santriPutri = Santri::where('status', 'aktif')->where('jenis_kelamin', 'P')->get();
        
        if ($asramaPutra->isEmpty() && $asramaPutri->isEmpty()) {
            $this->command->info('Tidak ada asrama yang tersedia.');
            return;
        }

        $this->command->info("Total Santri Putra: {$santriPutra->count()}");
        $this->command->info("Total Santri Putri: {$santriPutri->count()}");
        $this->command->info("Total Asrama Putra: {$asramaPutra->count()}");
        $this->command->info("Total Asrama Putri: {$asramaPutri->count()}");

        // Distribusi santri putra ke asrama putra
        if ($santriPutra->count() > 0 && $asramaPutra->count() > 0) {
            $totalSantriPutra = $santriPutra->count();
            $totalAsramaPutra = $asramaPutra->count();
            $santriPerAsrama = intval($totalSantriPutra / $totalAsramaPutra);
            $sisaSantri = $totalSantriPutra % $totalAsramaPutra;

            $this->command->info("Santri putra per asrama: {$santriPerAsrama}");
            $this->command->info("Sisa santri putra: {$sisaSantri}");

            // Shuffle santri untuk distribusi acak
            $santriPutra = $santriPutra->shuffle();
            
            $santriIndex = 0;

            foreach ($asramaPutra as $index => $asrama) {
                // Tentukan jumlah santri untuk asrama ini
                $jumlahSantriAsrama = $santriPerAsrama;
                
                // Tambahkan 1 santri extra untuk asrama pertama jika ada sisa
                if ($index < $sisaSantri) {
                    $jumlahSantriAsrama++;
                }

                $this->command->info("Mengisi asrama {$asrama->nama} ({$asrama->kode}) dengan {$jumlahSantriAsrama} santri putra");

                // Assign santri ke asrama ini
                for ($i = 0; $i < $jumlahSantriAsrama && $santriIndex < $totalSantriPutra; $i++) {
                    $santriPutra[$santriIndex]->update(['asrama_id' => $asrama->id]);
                    $santriIndex++;
                }
            }
        }

        // Distribusi santri putri ke asrama putri
        if ($santriPutri->count() > 0 && $asramaPutri->count() > 0) {
            $totalSantriPutri = $santriPutri->count();
            $totalAsramaPutri = $asramaPutri->count();
            $santriPerAsrama = intval($totalSantriPutri / $totalAsramaPutri);
            $sisaSantri = $totalSantriPutri % $totalAsramaPutri;

            $this->command->info("Santri putri per asrama: {$santriPerAsrama}");
            $this->command->info("Sisa santri putri: {$sisaSantri}");

            // Shuffle santri untuk distribusi acak
            $santriPutri = $santriPutri->shuffle();
            
            $santriIndex = 0;

            foreach ($asramaPutri as $index => $asrama) {
                // Tentukan jumlah santri untuk asrama ini
                $jumlahSantriAsrama = $santriPerAsrama;
                
                // Tambahkan 1 santri extra untuk asrama pertama jika ada sisa
                if ($index < $sisaSantri) {
                    $jumlahSantriAsrama++;
                }

                $this->command->info("Mengisi asrama {$asrama->nama} ({$asrama->kode}) dengan {$jumlahSantriAsrama} santri putri");

                // Assign santri ke asrama ini
                for ($i = 0; $i < $jumlahSantriAsrama && $santriIndex < $totalSantriPutri; $i++) {
                    $santriPutri[$santriIndex]->update(['asrama_id' => $asrama->id]);
                    $santriIndex++;
                }
            }
        }

        // Tampilkan ringkasan
        $this->command->info("\nRingkasan Anggota Asrama:");
        foreach (Asrama::all() as $asrama) {
            $jumlahAnggota = Santri::where('asrama_id', $asrama->id)->where('status', 'aktif')->count();
            $this->command->info("Asrama {$asrama->nama} ({$asrama->kode}): {$jumlahAnggota} anggota");
        }

        $totalSantriDiAsrama = Santri::whereNotNull('asrama_id')->where('status', 'aktif')->count();
        $this->command->info("Total santri yang berhasil ditempatkan di asrama: {$totalSantriDiAsrama}");
    }
}
