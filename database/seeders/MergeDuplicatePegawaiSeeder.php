<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\PegawaiJabatan;
use Illuminate\Support\Facades\DB;

class MergeDuplicatePegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        
        try {
            // Cari pegawai duplikasi berdasarkan nama
            $duplicates = Pegawai::select('nama_pegawai', DB::raw('COUNT(*) as count'))
                ->groupBy('nama_pegawai')
                ->having('count', '>', 1)
                ->get();

            $this->command->info("Ditemukan " . $duplicates->count() . " nama pegawai yang duplikasi");

            foreach ($duplicates as $duplicate) {
                $pegawaiList = Pegawai::where('nama_pegawai', $duplicate->nama_pegawai)->get();
                
                $this->command->info("\n=== Merging: " . $duplicate->nama_pegawai . " (" . $pegawaiList->count() . " records) ===");
                
                // Ambil pegawai pertama sebagai master
                $masterPegawai = $pegawaiList->first();
                $otherPegawais = $pegawaiList->skip(1);
                
                // Tentukan jabatan utama berdasarkan level tertinggi (angka terkecil = level tertinggi)
                $allJabatans = [];
                
                // Ambil jabatan dari master
                if ($masterPegawai->jabatan_id) {
                    $jabatan = Jabatan::find($masterPegawai->jabatan_id);
                    if ($jabatan) {
                        $allJabatans[] = [
                            'jabatan' => $jabatan,
                            'pegawai' => $masterPegawai
                        ];
                    }
                }
                
                // Ambil jabatan dari duplikasi lainnya
                foreach ($otherPegawais as $otherPegawai) {
                    if ($otherPegawai->jabatan_id) {
                        $jabatan = Jabatan::find($otherPegawai->jabatan_id);
                        if ($jabatan) {
                            $allJabatans[] = [
                                'jabatan' => $jabatan,
                                'pegawai' => $otherPegawai
                            ];
                        }
                    }
                }
                
                // Sort berdasarkan level jabatan (ascending = level tertinggi di awal)
                usort($allJabatans, function($a, $b) {
                    return $a['jabatan']->level_jabatan <=> $b['jabatan']->level_jabatan;
                });
                
                // Update data master dengan data terlengkap
                $this->updateMasterData($masterPegawai, $otherPegawais);
                
                // Create pivot records untuk semua jabatan
                foreach ($allJabatans as $index => $item) {
                    $isUtama = ($index === 0); // Jabatan pertama (level tertinggi) jadi utama
                    
                    // Cek apakah sudah ada record untuk jabatan ini
                    $existing = PegawaiJabatan::where('pegawai_id', $masterPegawai->id)
                        ->where('jabatan_id', $item['jabatan']->id)
                        ->first();
                        
                    if (!$existing) {
                        PegawaiJabatan::create([
                            'pegawai_id' => $masterPegawai->id,
                            'jabatan_id' => $item['jabatan']->id,
                            'is_jabatan_utama' => $isUtama,
                            'tanggal_mulai' => $item['pegawai']->tanggal_masuk ?: now(),
                            'status' => 'aktif'
                        ]);
                        
                        $this->command->info("  ✓ Added jabatan: " . $item['jabatan']->nama_jabatan . 
                            ($isUtama ? " (UTAMA)" : ""));
                    }
                }
                
                // Update jabatan_id master dengan jabatan utama
                if (!empty($allJabatans)) {
                    $masterPegawai->update([
                        'jabatan_id' => $allJabatans[0]['jabatan']->id,
                        'jabatan' => $allJabatans[0]['jabatan']->nama_jabatan,
                        'divisi' => $allJabatans[0]['jabatan']->bidang ? $allJabatans[0]['jabatan']->bidang->nama_bidang : null
                    ]);
                }
                
                // Hapus pegawai duplikasi lainnya
                foreach ($otherPegawais as $otherPegawai) {
                    $this->command->info("  ✗ Deleting duplicate ID: " . $otherPegawai->id);
                    $otherPegawai->delete();
                }
            }
            
            DB::commit();
            $this->command->info("\n✅ Merge pegawai duplikasi berhasil!");
            
        } catch (\Exception $e) {
            DB::rollback();
            $this->command->error("❌ Error: " . $e->getMessage());
            throw $e;
        }
    }
    
    private function updateMasterData($master, $others)
    {
        $updateData = [];
        
        foreach ($others as $other) {
            // Update field yang kosong di master dengan data dari duplikasi
            if (!$master->nik && $other->nik) $updateData['nik'] = $other->nik;
            if (!$master->nip && $other->nip) $updateData['nip'] = $other->nip;
            if (!$master->email && $other->email) $updateData['email'] = $other->email;
            if (!$master->no_hp && $other->no_hp) $updateData['no_hp'] = $other->no_hp;
            if (!$master->foto && $other->foto) $updateData['foto'] = $other->foto;
            if (!$master->alamat && $other->alamat) $updateData['alamat'] = $other->alamat;
            if (!$master->tempat_lahir && $other->tempat_lahir) $updateData['tempat_lahir'] = $other->tempat_lahir;
            if (!$master->tanggal_lahir && $other->tanggal_lahir) $updateData['tanggal_lahir'] = $other->tanggal_lahir;
            if (!$master->agama && $other->agama) $updateData['agama'] = $other->agama;
            if (!$master->pendidikan_terakhir && $other->pendidikan_terakhir) $updateData['pendidikan_terakhir'] = $other->pendidikan_terakhir;
            if (!$master->gaji_pokok && $other->gaji_pokok) $updateData['gaji_pokok'] = $other->gaji_pokok;
        }
        
        if (!empty($updateData)) {
            $master->update($updateData);
            $this->command->info("  ↻ Updated master data with " . count($updateData) . " fields");
        }
    }
}
