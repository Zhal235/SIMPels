<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Asrama;
use App\Models\AsramaAnggota;
use App\Models\Santri;
use Carbon\Carbon;

class AsramaAnggotaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\nMendistribusikan santri ke asrama berdasarkan jenis kelamin...\n";

        // Hapus data anggota asrama yang sudah ada (jika ada)
        AsramaAnggota::truncate();

        // Ambil semua santri yang aktif
        $santris = Santri::where('status', 'aktif')->get();
        $totalSantri = $santris->count();

        if ($totalSantri == 0) {
            echo "Tidak ada santri aktif untuk didistribusikan ke asrama.\n";
            return;
        }

        // Pisahkan santri berdasarkan jenis kelamin
        $santriPutra = $santris->where('jenis_kelamin', 'L');
        $santriPutri = $santris->where('jenis_kelamin', 'P');

        // Ambil asrama yang sudah ada
        $asramaPutra = Asrama::where('kode', 'like', '%-PA-%')->get(); // Asrama Putra
        $asramaPutri = Asrama::where('kode', 'like', '%-PI-%')->get(); // Asrama Putri

        if ($asramaPutra->isEmpty()) {
            echo "Tidak ada asrama putra yang tersedia.\n";
        }

        if ($asramaPutri->isEmpty()) {
            echo "Tidak ada asrama putri yang tersedia.\n";
        }

        $tanggalMasuk = Carbon::now()->subMonths(6); // 6 bulan yang lalu

        // Distribusi santri putra
        if ($santriPutra->count() > 0 && $asramaPutra->count() > 0) {
            $santriPerAsramaPutra = ceil($santriPutra->count() / $asramaPutra->count());
            
            foreach ($asramaPutra as $index => $asrama) {
                $santrisUntukAsrama = $santriPutra->slice($index * $santriPerAsramaPutra, $santriPerAsramaPutra);
                
                foreach ($santrisUntukAsrama as $santri) {
                    AsramaAnggota::create([
                        'santri_id' => $santri->id,
                        'asrama_id' => $asrama->id,
                        'tanggal_masuk' => $tanggalMasuk->copy()->addDays(rand(0, 30)),
                        'tanggal_keluar' => null
                    ]);
                }
                
                echo "Asrama '{$asrama->nama}': {$santrisUntukAsrama->count()} santri putra\n";
            }
        }

        // Distribusi santri putri  
        if ($santriPutri->count() > 0 && $asramaPutri->count() > 0) {
            $santriPerAsramaPutri = ceil($santriPutri->count() / $asramaPutri->count());
            
            foreach ($asramaPutri as $index => $asrama) {
                $santrisUntukAsrama = $santriPutri->slice($index * $santriPerAsramaPutri, $santriPerAsramaPutri);
                
                foreach ($santrisUntukAsrama as $santri) {
                    AsramaAnggota::create([
                        'santri_id' => $santri->id,
                        'asrama_id' => $asrama->id,
                        'tanggal_masuk' => $tanggalMasuk->copy()->addDays(rand(0, 30)),
                        'tanggal_keluar' => null
                    ]);
                }
                
                echo "Asrama '{$asrama->nama}': {$santrisUntukAsrama->count()} santri putri\n";
            }
        }

        $totalAnggota = AsramaAnggota::count();
        echo "\nBerhasil membuat {$totalAnggota} anggota asrama dari {$totalSantri} santri.\n";
        echo "Santri Putra: {$santriPutra->count()}\n";
        echo "Santri Putri: {$santriPutri->count()}\n";

        // Tampilkan ringkasan per asrama
        echo "\nRingkasan Anggota Asrama:\n";
        foreach (Asrama::all() as $asrama) {
            $jumlahAnggota = AsramaAnggota::where('asrama_id', $asrama->id)
                                        ->whereNull('tanggal_keluar')
                                        ->count();
            echo "Asrama '{$asrama->nama}' ('{$asrama->kode}'): {$jumlahAnggota} anggota\n";
        }
    }
}
