<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pegawai;
use App\Models\Jabatan;

class UpdatePegawaiJabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mapping jabatan string ke jabatan_id
        $jabatanMapping = [
            'Ketua Dewan Pengasuh' => 'KDP',
            'Anggota Dewan Pengasuh' => 'ADP',
            'Ketua Pengurus Harian' => 'KPH',
            'Sekretaris Umum' => 'SEKUM',
            'Mudir Al-Ma\'had (Pimpinan Pesantren)' => 'MUDIR',
            'Na\'ib Mudir Bidang Akademik' => 'NAIB-AKAD',
            'Na\'ib Mudir Bidang Kesantrian' => 'NAIB-SANT',
            'Na\'ib Mudir Bidang Administrasi dan Keuangan' => 'NAIB-ADKEU',
            'Na\'ib Mudir Bidang Humas' => 'NAIB-HUMAS',
            'Na\'ib Mudir Bidang Sarana dan Prasarana' => 'NAIB-SARPRAS',
            'Na\'ib Mudir Bidang Kewirausahaan' => 'NAIB-WIRAUSAHA',
            'Kepala SMK' => 'KASMK',
            'Kepala MTs' => 'KAMTS',
            'Kepala STA' => 'KASTA',
            'Kepala PAUD' => 'KAPAUD',
            'Pembina Asrama Putra' => 'PASRAMPUT',
            'Pembina Asrama Putri' => 'PASRAMPUTRI',
            'Kepala Bagian Kesekretariatan' => 'KASEKRET',
            'Kepala Bagian Keuangan & KKT' => 'KAKEU',
            'Guru/Ustadz' => 'GURU',
            'Wali Kelas' => 'WALIKELAS',
            'Operator Sekolah/Madrasah' => 'OPERATOR',
            'Staff Tata Usaha' => 'STAFFTU',
            'Bendahara' => 'BENDAHARA',
            'Musyrif/Musyrifah Asrama' => 'MUSYRIF'
        ];

        // Hapus semua data di tabel pivot untuk fresh start
        \DB::table('pegawai_jabatan')->delete();
        
        // Counter untuk statistik
        $totalUpdated = 0;
        $totalPegawai = Pegawai::count();
        
        foreach ($jabatanMapping as $namaJabatan => $kodeJabatan) {
            $jabatan = Jabatan::where('kode_jabatan', $kodeJabatan)->first();
            
            if ($jabatan) {
                // Update pegawai yang memiliki jabatan tersebut
                $pegawais = Pegawai::where('jabatan', 'like', "%{$namaJabatan}%")->get();
                
                foreach ($pegawais as $pegawai) {
                    // Update jabatan_id di tabel pegawai (untuk backward compatibility)
                    $pegawai->update(['jabatan_id' => $jabatan->id]);
                    
                    // Tambahkan ke tabel pivot (pegawai_jabatan)
                    $pegawai->pegawaiJabatans()->updateOrCreate(
                        [
                            'pegawai_id' => $pegawai->id,
                            'jabatan_id' => $jabatan->id,
                        ],
                        [
                            'is_jabatan_utama' => true,
                            'tanggal_mulai' => $pegawai->tanggal_masuk ?? now(),
                            'status' => 'aktif',
                        ]
                    );
                    
                    $totalUpdated++;
                    $this->command->info("Pegawai {$pegawai->nama_pegawai} ditambahkan jabatan {$jabatan->nama_jabatan}");
                }
                Pegawai::where('jabatan', $namaJabatan)->update([
                    'jabatan_id' => $jabatan->id
                ]);
                
                // Update pegawai yang memiliki jabatan dengan format singkat
                Pegawai::where('jabatan', 'LIKE', '%' . str_replace(['Na\'ib Mudir ', 'Kepala ', 'Pembina '], '', $namaJabatan) . '%')
                    ->whereNull('jabatan_id')
                    ->update([
                        'jabatan_id' => $jabatan->id
                    ]);
            }
        }

        // Update pegawai yang jabatannya mengandung kata kunci tertentu
        $keywordMapping = [
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
            'Musyrifah' => 'MUSYRIF',
            'Wali Kelas' => 'WALIKELAS',
            'Keuangan' => 'KAKEU',
            'Sekretariat' => 'KASEKRET',
            'PENGAWAS' => 'PENGAWAS'
        ];

        foreach ($keywordMapping as $keyword => $kodeJabatan) {
            $jabatan = Jabatan::where('kode_jabatan', $kodeJabatan)->first();
            
            if ($jabatan) {
                // Update pegawai yang memiliki jabatan dengan kata kunci ini
                $pegawais = Pegawai::where('jabatan', 'LIKE', '%' . $keyword . '%')
                    ->whereNull('jabatan_id')
                    ->get();
                
                foreach ($pegawais as $pegawai) {
                    // Update jabatan_id
                    $pegawai->update(['jabatan_id' => $jabatan->id]);
                    
                    // Tambahkan ke tabel pivot
                    $pegawai->pegawaiJabatans()->updateOrCreate(
                        [
                            'pegawai_id' => $pegawai->id,
                            'jabatan_id' => $jabatan->id,
                        ],
                        [
                            'is_jabatan_utama' => true,
                            'tanggal_mulai' => $pegawai->tanggal_masuk ?? now(),
                            'status' => 'aktif',
                        ]
                    );
                    
                    $totalUpdated++;
                    $this->command->info("Pegawai {$pegawai->nama_pegawai} ditambahkan jabatan {$jabatan->nama_jabatan} via keyword");
                }
            }
        }

        // Untuk pegawai yang belum ter-assign jabatan_id, berikan jabatan default (Guru/Ustadz)
        $defaultJabatan = Jabatan::where('kode_jabatan', 'GURU')->first();
        if ($defaultJabatan) {
            $unassignedPegawais = Pegawai::whereNull('jabatan_id')->get();
            
            foreach ($unassignedPegawais as $pegawai) {
                // Update jabatan_id
                $pegawai->update(['jabatan_id' => $defaultJabatan->id]);
                
                // Tambahkan ke tabel pivot
                $pegawai->pegawaiJabatans()->updateOrCreate(
                    [
                        'pegawai_id' => $pegawai->id,
                        'jabatan_id' => $defaultJabatan->id,
                    ],
                    [
                        'is_jabatan_utama' => true,
                        'tanggal_mulai' => $pegawai->tanggal_masuk ?? now(),
                        'status' => 'aktif',
                    ]
                );
                
                $totalUpdated++;
                $this->command->info("Pegawai {$pegawai->nama_pegawai} ditambahkan jabatan default {$defaultJabatan->nama_jabatan}");
            }
        }
        
        // Tampilkan statistik
        $this->command->info("Total {$totalUpdated} jabatan berhasil diassign ke {$totalPegawai} pegawai");
        $this->command->info("Total data di tabel pegawai_jabatan: " . \DB::table('pegawai_jabatan')->count());

        $this->command->info('Jabatan pegawai berhasil diupdate!');
    }
}
