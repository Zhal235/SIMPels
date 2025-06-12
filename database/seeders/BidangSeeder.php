<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Bidang;
use App\Models\User;

class BidangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@admin.com')->first();
        $adminId = $admin ? $admin->id : 1;

        $bidangs = [
            [
                'nama_bidang' => 'Bidang Akademik',
                'kode_bidang' => 'AKAD',
                'deskripsi' => 'Mengelola kegiatan akademik, kurikulum, dan pembelajaran',
                'urutan' => 1,
                'status' => 'aktif',
                'created_by' => $adminId
            ],
            [
                'nama_bidang' => 'Bidang Kesantrian',
                'kode_bidang' => 'SANT',
                'deskripsi' => 'Mengelola kehidupan santri, kedisiplinan, dan pembinaan karakter',
                'urutan' => 2,
                'status' => 'aktif',
                'created_by' => $adminId
            ],
            [
                'nama_bidang' => 'Bidang Administrasi dan Keuangan',
                'kode_bidang' => 'ADKEU',
                'deskripsi' => 'Mengelola administrasi umum, keuangan, dan kesekretariatan',
                'urutan' => 3,
                'status' => 'aktif',
                'created_by' => $adminId
            ],
            [
                'nama_bidang' => 'Bidang Humas',
                'kode_bidang' => 'HUMAS',
                'deskripsi' => 'Mengelola hubungan masyarakat dan komunikasi eksternal',
                'urutan' => 4,
                'status' => 'aktif',
                'created_by' => $adminId
            ],
            [
                'nama_bidang' => 'Bidang Sarana dan Prasarana',
                'kode_bidang' => 'SARPRAS',
                'deskripsi' => 'Mengelola sarana prasarana dan pemeliharaan fasilitas',
                'urutan' => 5,
                'status' => 'aktif',
                'created_by' => $adminId
            ],
            [
                'nama_bidang' => 'Bidang Kewirausahaan',
                'kode_bidang' => 'WIRAUSAHA',
                'deskripsi' => 'Mengelola unit usaha dan pengembangan ekonomi pesantren',
                'urutan' => 6,
                'status' => 'aktif',
                'created_by' => $adminId
            ]
        ];

        foreach ($bidangs as $bidang) {
            Bidang::create($bidang);
        }
    }
}
