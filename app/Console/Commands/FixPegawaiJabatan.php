<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pegawai;
use App\Models\Jabatan;

class FixPegawaiJabatan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pegawai:fix-jabatan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix pegawai jabatan_id based on jabatan string';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memperbaiki data jabatan_id pegawai...');
        
        $pegawais = Pegawai::whereNull('jabatan_id')
                          ->orWhere('jabatan_id', 0)
                          ->get();

        $updated = 0;
        $defaultJabatan = Jabatan::where('kode_jabatan', 'GURU')->first();

        foreach ($pegawais as $pegawai) {
            $jabatanId = null;
            
            if ($pegawai->jabatan) {
                // Cari berdasarkan nama jabatan yang mirip
                $jabatan = Jabatan::where('nama_jabatan', 'LIKE', '%' . $pegawai->jabatan . '%')
                                 ->orWhere('nama_jabatan', $pegawai->jabatan)
                                 ->first();
                
                if (!$jabatan) {
                    // Cari berdasarkan kata kunci
                    $keywords = [
                        'SMK' => 'KASMK',
                        'MTs' => 'KAMTS', 
                        'STA' => 'KASTA',
                        'PAUD' => 'KAPAUD',
                        'Bendahara' => 'BENDAHARA',
                        'Operator' => 'OPERATOR',
                        'Tata Usaha' => 'STAFFTU',
                        'Staff' => 'STAFFTU',
                        'Guru' => 'GURU',
                        'Ustadz' => 'GURU',
                        'Musyrif' => 'MUSYRIF',
                        'Wali Kelas' => 'WALIKELAS',
                        'Keuangan' => 'KAKEU',
                        'Sekretariat' => 'KASEKRET'
                    ];
                    
                    foreach ($keywords as $keyword => $kodeJabatan) {
                        if (str_contains($pegawai->jabatan, $keyword)) {
                            $jabatan = Jabatan::where('kode_jabatan', $kodeJabatan)->first();
                            break;
                        }
                    }
                }
                
                if ($jabatan) {
                    $jabatanId = $jabatan->id;
                }
            }
            
            // Jika tidak ditemukan, gunakan default
            if (!$jabatanId && $defaultJabatan) {
                $jabatanId = $defaultJabatan->id;
            }
            
            if ($jabatanId) {
                $pegawai->update(['jabatan_id' => $jabatanId]);
                $updated++;
                $this->line("Updated: {$pegawai->nama_pegawai} -> {$jabatanId}");
            }
        }
        
        $this->info("Berhasil memperbaiki {$updated} data pegawai.");
        
        return 0;
    }
}
