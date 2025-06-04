<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\KelasAnggota;
use Illuminate\Support\Facades\DB;

class KelasAnggotaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        KelasAnggota::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

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
        $kelasAnggotaData = [];

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
                $kelasAnggotaData[] = [
                    'santri_id' => $santris[$santriIndex]->id,
                    'kelas_id' => $kelasItem->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $santriIndex++;
            }
        }

        // Insert data dalam batch untuk performa yang lebih baik
        if (!empty($kelasAnggotaData)) {
            KelasAnggota::insert($kelasAnggotaData);
            $this->command->info("Berhasil membuat " . count($kelasAnggotaData) . " anggota kelas.");
        }

        // Tampilkan ringkasan
        foreach ($kelas as $kelasItem) {
            $jumlahAnggota = KelasAnggota::where('kelas_id', $kelasItem->id)->count();
            $this->command->info("Kelas {$kelasItem->nama} ({$kelasItem->kode}): {$jumlahAnggota} anggota");
        }
    }
}