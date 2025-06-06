<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Asrama;
use App\Models\AsramaAnggota;
use App\Models\Santri;
use Carbon\Carbon;

class AsramaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data asrama yang akan dibuat
        $asramaData = [
            [
                'kode' => 'ASR-PA-001',
                'nama' => 'Asrama Putra Blok A',
                'wali_asrama' => 'Ustadz Ahmad Fauzi',
                'jenis_kelamin' => 'L'
            ],
            [
                'kode' => 'ASR-PA-002', 
                'nama' => 'Asrama Putra Blok B',
                'wali_asrama' => 'Ustadz Muhammad Hasan',
                'jenis_kelamin' => 'L'
            ],
            [
                'kode' => 'ASR-PI-001',
                'nama' => 'Asrama Putri Blok A',
                'wali_asrama' => 'Ustadzah Siti Aisyah',
                'jenis_kelamin' => 'P'
            ],
            [
                'kode' => 'ASR-PI-002',
                'nama' => 'Asrama Putri Blok B', 
                'wali_asrama' => 'Ustadzah Fatimah Zahra',
                'jenis_kelamin' => 'P'
            ]
        ];

        echo "\nMembuat data asrama...\n";

        foreach ($asramaData as $data) {
            Asrama::create([
                'kode' => $data['kode'],
                'nama' => $data['nama'],
                'wali_asrama' => $data['wali_asrama']
            ]);
            echo "Asrama '{$data['nama']}' berhasil dibuat.\n";
        }

        echo "\nMendistribusikan santri ke asrama berdasarkan jenis kelamin...\n";

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

        // Ambil asrama yang sudah dibuat
        $asramaPutra = Asrama::whereIn('kode', ['ASR-PA-001', 'ASR-PA-002'])->get();
        $asramaPutri = Asrama::whereIn('kode', ['ASR-PI-001', 'ASR-PI-002'])->get();

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
