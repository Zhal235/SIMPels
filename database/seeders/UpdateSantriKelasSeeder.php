<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;
use App\Models\Santri;
use Illuminate\Support\Facades\DB;

class UpdateSantriKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset kelas_id untuk semua santri
        Santri::query()->update(['kelas_id' => null]);

        // Ambil semua kelas yang ada
        $kelas = Kelas::all();
        
        // Ambil semua santri aktif
        $santris = Santri::where('status', 'aktif')->get();
        
        if ($kelas->isEmpty() || $santris->isEmpty()) {
            $this->command->info('Tidak ada kelas atau santri yang tersedia untuk dibuat anggota kelas.');
            return;
        }

        // Hitung jumlah santri per kelas (distribusi merata)
        $totalSantri = $santris->count();
        $totalKelas = $kelas->count();
        $santriPerKelas = intval($totalSantri / $totalKelas);
        $sisaSantri = $totalSantri % $totalKelas;

        $this->command->info("Total Santri: {$totalSantri}");
        $this->command->info("Total Kelas: {$totalKelas}");
        $this->command->info("Santri per kelas: {$santriPerKelas}");
        $this->command->info("Sisa santri: {$sisaSantri}");

        // Shuffle santri untuk distribusi acak
        $santris = $santris->shuffle();
        
        $santriIndex = 0;

        foreach ($kelas as $index => $kelasItem) {
            // Tentukan jumlah santri untuk kelas ini
            $jumlahSantriKelas = $santriPerKelas;
            
            // Tambahkan 1 santri extra untuk kelas pertama jika ada sisa
            if ($index < $sisaSantri) {
                $jumlahSantriKelas++;
            }

            $this->command->info("Mengisi kelas {$kelasItem->nama} ({$kelasItem->kode}) dengan {$jumlahSantriKelas} santri");

            // Assign santri ke kelas ini
            for ($i = 0; $i < $jumlahSantriKelas && $santriIndex < $totalSantri; $i++) {
                $santris[$santriIndex]->update(['kelas_id' => $kelasItem->id]);
                $santriIndex++;
            }
        }

        // Tampilkan ringkasan
        $this->command->info("\nRingkasan Anggota Kelas:");
        foreach ($kelas as $kelasItem) {
            $jumlahAnggota = Santri::where('kelas_id', $kelasItem->id)->where('status', 'aktif')->count();
            $this->command->info("Kelas {$kelasItem->nama} ({$kelasItem->kode}): {$jumlahAnggota} anggota");
        }

        $totalSantriDiKelas = Santri::whereNotNull('kelas_id')->where('status', 'aktif')->count();
        $this->command->info("Total santri yang berhasil ditempatkan di kelas: {$totalSantriDiKelas}");
    }
}
