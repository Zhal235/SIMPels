<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Jabatan;
use App\Models\Bidang;
use App\Models\User;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@admin.com')->first();
        $adminId = $admin ? $admin->id : 1;

        // Get bidang IDs
        $bidangAkademik = Bidang::where('kode_bidang', 'AKAD')->first();
        $bidangKesantrian = Bidang::where('kode_bidang', 'SANT')->first();
        $bidangAdkeu = Bidang::where('kode_bidang', 'ADKEU')->first();
        $bidangHumas = Bidang::where('kode_bidang', 'HUMAS')->first();
        $bidangSarpras = Bidang::where('kode_bidang', 'SARPRAS')->first();
        $bidangWirausaha = Bidang::where('kode_bidang', 'WIRAUSAHA')->first();

        $jabatans = [
            // Level 1 - Dewan Pengasuh Pesantren
            [
                'nama_jabatan' => 'Ketua Dewan Pengasuh',
                'kode_jabatan' => 'KDP',
                'kategori_jabatan' => 'pengasuh',
                'level_jabatan' => 1,
                'gaji_pokok' => 15000000,
                'tunjangan' => 5000000,
                'status' => 'aktif',
                'is_struktural' => true,
                'created_by' => $adminId
            ],
            [
                'nama_jabatan' => 'Anggota Dewan Pengasuh',
                'kode_jabatan' => 'ADP',
                'kategori_jabatan' => 'pengasuh',
                'level_jabatan' => 1,
                'gaji_pokok' => 12000000,
                'tunjangan' => 3000000,
                'status' => 'aktif',
                'is_struktural' => true,
                'created_by' => $adminId
            ],

            // Level 2 - Pengurus Harian Yayasan
            [
                'nama_jabatan' => 'Ketua Pengurus Harian',
                'kode_jabatan' => 'KPH',
                'kategori_jabatan' => 'pengurus',
                'level_jabatan' => 2,
                'gaji_pokok' => 10000000,
                'tunjangan' => 2500000,
                'status' => 'aktif',
                'is_struktural' => true,
                'created_by' => $adminId
            ],
            [
                'nama_jabatan' => 'Sekretaris Umum',
                'kode_jabatan' => 'SEKUM',
                'kategori_jabatan' => 'pengurus',
                'level_jabatan' => 2,
                'gaji_pokok' => 8000000,
                'tunjangan' => 2000000,
                'status' => 'aktif',
                'is_struktural' => true,
                'created_by' => $adminId
            ],

            // Level 3 - Pimpinan Pesantren
            [
                'nama_jabatan' => 'Mudir Al-Ma\'had (Pimpinan Pesantren)',
                'kode_jabatan' => 'MUDIR',
                'kategori_jabatan' => 'pimpinan',
                'level_jabatan' => 3,
                'gaji_pokok' => 9000000,
                'tunjangan' => 2500000,
                'status' => 'aktif',
                'is_struktural' => true,
                'created_by' => $adminId
            ],

            // Level 4 - Na'ib Mudir (Wakil Pimpinan)
            [
                'nama_jabatan' => 'Na\'ib Mudir Bidang Akademik',
                'kode_jabatan' => 'NAIB-AKAD',
                'kategori_jabatan' => 'naib',
                'level_jabatan' => 4,
                'bidang_id' => $bidangAkademik?->id,
                'gaji_pokok' => 7500000,
                'tunjangan' => 2000000,
                'status' => 'aktif',
                'is_struktural' => true,
                'created_by' => $adminId
            ],
            [
                'nama_jabatan' => 'Na\'ib Mudir Bidang Kesantrian',
                'kode_jabatan' => 'NAIB-SANT',
                'kategori_jabatan' => 'naib',
                'level_jabatan' => 4,
                'bidang_id' => $bidangKesantrian?->id,
                'gaji_pokok' => 7500000,
                'tunjangan' => 2000000,
                'status' => 'aktif',
                'is_struktural' => true,
                'created_by' => $adminId
            ],
            [
                'nama_jabatan' => 'Na\'ib Mudir Bidang Administrasi dan Keuangan',
                'kode_jabatan' => 'NAIB-ADKEU',
                'kategori_jabatan' => 'naib',
                'level_jabatan' => 4,
                'bidang_id' => $bidangAdkeu?->id,
                'gaji_pokok' => 7500000,
                'tunjangan' => 2000000,
                'status' => 'aktif',
                'is_struktural' => true,
                'created_by' => $adminId
            ],
            [
                'nama_jabatan' => 'Na\'ib Mudir Bidang Humas',
                'kode_jabatan' => 'NAIB-HUMAS',
                'kategori_jabatan' => 'naib',
                'level_jabatan' => 4,
                'bidang_id' => $bidangHumas?->id,
                'gaji_pokok' => 7000000,
                'tunjangan' => 1500000,
                'status' => 'aktif',
                'is_struktural' => true,
                'created_by' => $adminId
            ],
            [
                'nama_jabatan' => 'Na\'ib Mudir Bidang Sarana dan Prasarana',
                'kode_jabatan' => 'NAIB-SARPRAS',
                'kategori_jabatan' => 'naib',
                'level_jabatan' => 4,
                'bidang_id' => $bidangSarpras?->id,
                'gaji_pokok' => 7000000,
                'tunjangan' => 1500000,
                'status' => 'aktif',
                'is_struktural' => true,
                'created_by' => $adminId
            ],
            [
                'nama_jabatan' => 'Na\'ib Mudir Bidang Kewirausahaan',
                'kode_jabatan' => 'NAIB-WIRAUSAHA',
                'kategori_jabatan' => 'naib',
                'level_jabatan' => 4,
                'bidang_id' => $bidangWirausaha?->id,
                'gaji_pokok' => 7000000,
                'tunjangan' => 1500000,
                'status' => 'aktif',
                'is_struktural' => true,
                'created_by' => $adminId
            ],

            // Level 5 - Kepala Unit/Bagian (Bidang Akademik)
            [
                'nama_jabatan' => 'Kepala SMK',
                'kode_jabatan' => 'KASMK',
                'kategori_jabatan' => 'kepala',
                'level_jabatan' => 5,
                'bidang_id' => $bidangAkademik?->id,
                'gaji_pokok' => 6000000,
                'tunjangan' => 1500000,
                'status' => 'aktif',
                'is_struktural' => true,
                'created_by' => $adminId
            ],
            [
                'nama_jabatan' => 'Kepala MTs',
                'kode_jabatan' => 'KAMTS',
                'kategori_jabatan' => 'kepala',
                'level_jabatan' => 5,
                'bidang_id' => $bidangAkademik?->id,
                'gaji_pokok' => 6000000,
                'tunjangan' => 1500000,
                'status' => 'aktif',
                'is_struktural' => true,
                'created_by' => $adminId
            ],
            [
                'nama_jabatan' => 'Kepala STA',
                'kode_jabatan' => 'KASTA',
                'kategori_jabatan' => 'kepala',
                'level_jabatan' => 5,
                'bidang_id' => $bidangAkademik?->id,
                'gaji_pokok' => 6000000,
                'tunjangan' => 1500000,
                'status' => 'aktif',
                'is_struktural' => true,
                'created_by' => $adminId
            ],
            [
                'nama_jabatan' => 'Kepala PAUD',
                'kode_jabatan' => 'KAPAUD',
                'kategori_jabatan' => 'kepala',
                'level_jabatan' => 5,
                'bidang_id' => $bidangAkademik?->id,
                'gaji_pokok' => 5500000,
                'tunjangan' => 1200000,
                'status' => 'aktif',
                'is_struktural' => true,
                'created_by' => $adminId
            ],

            // Level 5 - Kepala Unit/Bagian (Bidang Kesantrian)
            [
                'nama_jabatan' => 'Pembina Asrama Putra',
                'kode_jabatan' => 'PASRAMPUT',
                'kategori_jabatan' => 'kepala',
                'level_jabatan' => 5,
                'bidang_id' => $bidangKesantrian?->id,
                'gaji_pokok' => 5000000,
                'tunjangan' => 1500000,
                'status' => 'aktif',
                'is_struktural' => true,
                'created_by' => $adminId
            ],
            [
                'nama_jabatan' => 'Pembina Asrama Putri',
                'kode_jabatan' => 'PASRAMPUTRI',
                'kategori_jabatan' => 'kepala',
                'level_jabatan' => 5,
                'bidang_id' => $bidangKesantrian?->id,
                'gaji_pokok' => 5000000,
                'tunjangan' => 1500000,
                'status' => 'aktif',
                'is_struktural' => true,
                'created_by' => $adminId
            ],

            // Level 5 - Kepala Unit/Bagian (Bidang Administrasi)
            [
                'nama_jabatan' => 'Kepala Bagian Kesekretariatan',
                'kode_jabatan' => 'KASEKRET',
                'kategori_jabatan' => 'kepala',
                'level_jabatan' => 5,
                'bidang_id' => $bidangAdkeu?->id,
                'gaji_pokok' => 5500000,
                'tunjangan' => 1200000,
                'status' => 'aktif',
                'is_struktural' => true,
                'created_by' => $adminId
            ],
            [
                'nama_jabatan' => 'Kepala Bagian Keuangan & KKT',
                'kode_jabatan' => 'KAKEU',
                'kategori_jabatan' => 'kepala',
                'level_jabatan' => 5,
                'bidang_id' => $bidangAdkeu?->id,
                'gaji_pokok' => 6000000,
                'tunjangan' => 1500000,
                'status' => 'aktif',
                'is_struktural' => true,
                'created_by' => $adminId
            ],

            // Level 6 - Staff/Pelaksana
            [
                'nama_jabatan' => 'Guru/Ustadz',
                'kode_jabatan' => 'GURU',
                'kategori_jabatan' => 'staff',
                'level_jabatan' => 6,
                'bidang_id' => $bidangAkademik?->id,
                'gaji_pokok' => 3500000,
                'tunjangan' => 800000,
                'status' => 'aktif',
                'is_struktural' => false,
                'created_by' => $adminId
            ],
            [
                'nama_jabatan' => 'Wali Kelas',
                'kode_jabatan' => 'WALIKELAS',
                'kategori_jabatan' => 'staff',
                'level_jabatan' => 6,
                'bidang_id' => $bidangAkademik?->id,
                'gaji_pokok' => 4000000,
                'tunjangan' => 1000000,
                'status' => 'aktif',
                'is_struktural' => true,
                'created_by' => $adminId
            ],
            [
                'nama_jabatan' => 'Operator Sekolah/Madrasah',
                'kode_jabatan' => 'OPERATOR',
                'kategori_jabatan' => 'staff',
                'level_jabatan' => 6,
                'bidang_id' => $bidangAkademik?->id,
                'gaji_pokok' => 3000000,
                'tunjangan' => 500000,
                'status' => 'aktif',
                'is_struktural' => false,
                'created_by' => $adminId
            ],
            [
                'nama_jabatan' => 'Staff Tata Usaha',
                'kode_jabatan' => 'STAFFTU',
                'kategori_jabatan' => 'staff',
                'level_jabatan' => 6,
                'bidang_id' => $bidangAdkeu?->id,
                'gaji_pokok' => 3000000,
                'tunjangan' => 500000,
                'status' => 'aktif',
                'is_struktural' => false,
                'created_by' => $adminId
            ],
            [
                'nama_jabatan' => 'Bendahara',
                'kode_jabatan' => 'BENDAHARA',
                'kategori_jabatan' => 'staff',
                'level_jabatan' => 6,
                'bidang_id' => $bidangAdkeu?->id,
                'gaji_pokok' => 4000000,
                'tunjangan' => 1000000,
                'status' => 'aktif',
                'is_struktural' => true,
                'created_by' => $adminId
            ],
            [
                'nama_jabatan' => 'Musyrif/Musyrifah Asrama',
                'kode_jabatan' => 'MUSYRIF',
                'kategori_jabatan' => 'staff',
                'level_jabatan' => 6,
                'bidang_id' => $bidangKesantrian?->id,
                'gaji_pokok' => 3500000,
                'tunjangan' => 800000,
                'status' => 'aktif',
                'is_struktural' => false,
                'created_by' => $adminId
            ]
        ];

        foreach ($jabatans as $jabatan) {
            Jabatan::create($jabatan);
        }
    }
}
