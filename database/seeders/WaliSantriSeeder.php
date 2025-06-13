<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WaliSantri;
use App\Models\Santri;
use App\Models\Dompet;
use App\Models\DompetLimit;
use App\Models\TagihanSantri;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class WaliSantriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // Create sample wali santri
            $waliSantri = WaliSantri::create([
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@email.com',
                'password' => Hash::make('password123'),
                'phone' => '081234567890',
                'address' => 'Jl. Pendidikan No. 123, Jakarta',
                'nik' => '3175012345678901',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1980-05-15',
                'pekerjaan' => 'Pegawai Swasta',
                'status' => 'active'
            ]);

            // Get active tahun ajaran
            $tahunAjaran = TahunAjaran::where('is_active', true)->first();
            if (!$tahunAjaran) {
                $tahunAjaran = TahunAjaran::create([
                    'nama_tahun_ajaran' => '2024/2025',
                    'tahun_mulai' => 2024,
                    'tahun_selesai' => 2025,
                    'tanggal_mulai' => '2024-07-01',
                    'tanggal_selesai' => '2025-06-30',
                    'is_active' => true,
                    'keterangan' => 'Tahun Ajaran Aktif'
                ]);
            }

            // Create sample santri
            $santri = Santri::create([
                'wali_santri_id' => $waliSantri->id,
                'nis' => '2024001001',
                'nisn' => '1234567890',
                'nama_santri' => 'Ahmad Santoso',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '2010-08-20',
                'alamat' => 'Jl. Pendidikan No. 123, Jakarta',
                'nama_ayah' => 'Budi Santoso',
                'pekerjaan_ayah' => 'Pegawai Swasta',
                'hp_ayah' => '081234567890',
                'nama_ibu' => 'Siti Nurhaliza',
                'pekerjaan_ibu' => 'Ibu Rumah Tangga',
                'hp_ibu' => '081234567891',
                'email_orangtua' => 'budi.santoso@email.com',
                'status' => 'aktif'
            ]);

            // Create dompet for santri
            $dompet = Dompet::create([
                'jenis_pemilik' => 'santri',
                'pemilik_id' => $santri->id,
                'nomor_dompet' => Dompet::generateNomorDompet('santri', $santri->id),
                'saldo' => 500000,
                'limit_transaksi' => 50000,
                'is_active' => true
            ]);

            // Create dompet limit
            DompetLimit::create([
                'dompet_id' => $dompet->id,
                'limit_harian' => 100000,
                'limit_transaksi' => 50000,
                'limit_mingguan' => 500000,
                'limit_bulanan' => 2000000,
                'is_active' => true,
                'catatan' => 'Limit default untuk santri baru',
                'created_by' => 1
            ]);

            // Create sample jenis tagihan if not exists
            $jenisTagihan = JenisTagihan::firstOrCreate(
                ['nama' => 'SPP Bulanan'],
                [
                    'nominal' => 500000,
                    'is_nominal_per_kelas' => false,
                    'is_bulanan' => true,
                    'bulan_pembayaran' => [1,2,3,4,5,6,7,8,9,10,11,12],
                    'deskripsi' => 'SPP Bulanan Santri',
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'kategori_tagihan' => 'rutin',
                    'tipe_pembayaran' => 'bulanan'
                ]
            );

            // Create sample tagihan santri
            $bulanTagihan = ['Januari', 'Februari', 'Maret', 'April', 'Mei'];
            foreach ($bulanTagihan as $index => $bulan) {
                $isLunas = $index < 2; // 2 bulan pertama sudah lunas
                
                TagihanSantri::create([
                    'santri_id' => $santri->id,
                    'jenis_tagihan_id' => $jenisTagihan->id,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'bulan' => $bulan,
                    'nominal_tagihan' => 500000,
                    'nominal_dibayar' => $isLunas ? 500000 : 0,
                    'nominal_keringanan' => 0,
                    'status' => $isLunas ? 'lunas' : 'belum_lunas',
                    'tanggal_jatuh_tempo' => now()->addMonths($index)->endOfMonth()
                ]);
            }

            DB::commit();
            
            $this->command->info('Sample data for Wali Santri created successfully!');
            $this->command->info('Email: budi.santoso@email.com');
            $this->command->info('Password: password123');

        } catch (\Exception $e) {
            DB::rollback();
            $this->command->error('Error creating sample data: ' . $e->getMessage());
        }
    }
}
