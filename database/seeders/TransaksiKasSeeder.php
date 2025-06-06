<?php

namespace Database\Seeders;

use App\Models\TransaksiKas;
use App\Models\BukuKas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransaksiKasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dapatkan Buku Kas yang ada
        $bukuKasList = BukuKas::where('is_active', true)->get();
        
        if ($bukuKasList->count() < 2) {
            $this->command->error('Minimal dibutuhkan 2 Buku Kas aktif untuk seeder ini.');
            return;
        }
        
        // Dapatkan Admin User
        $admin = User::role('admin')->first();
        
        if (!$admin) {
            $this->command->error('User dengan role admin tidak ditemukan.');
            return;
        }
        
        $this->command->info('Mulai menambahkan data transaksi kas...');
        
        DB::beginTransaction();
        
        try {
            // Transaksi Pemasukan
            foreach ($bukuKasList as $index => $bukuKas) {
                // Add 3 sample pemasukan for each Buku Kas
                for ($i = 1; $i <= 3; $i++) {
                    $amount = rand(1000000, 5000000);
                    $date = Carbon::now()->subDays(rand(1, 30));
                    
                    $transaksi = TransaksiKas::create([
                        'buku_kas_id' => $bukuKas->id,
                        'jenis_transaksi' => 'pemasukan',
                        'kategori' => $this->getPemasukanKategori(),
                        'kode_transaksi' => TransaksiKas::generateKodeTransaksi('pemasukan'),
                        'jumlah' => $amount,
                        'keterangan' => 'Sample pemasukan #' . $i . ' untuk ' . $bukuKas->nama_kas,
                        'metode_pembayaran' => $this->getRandomMetodePembayaran(),
                        'no_referensi' => 'REF-' . strtoupper(substr(md5(mt_rand()), 0, 8)),
                        'tanggal_transaksi' => $date,
                        'created_by' => $admin->id,
                        'approved_by' => $admin->id,
                        'status' => 'approved',
                    ]);
                    
                    // Update saldo buku kas
                    $bukuKas->updateSaldo($amount, 'masuk');
                }
            }
            
            // Transaksi Pengeluaran
            foreach ($bukuKasList as $index => $bukuKas) {
                // Add 2 sample pengeluaran for each Buku Kas
                for ($i = 1; $i <= 2; $i++) {
                    $amount = rand(500000, 2000000);
                    $date = Carbon::now()->subDays(rand(1, 20));
                    
                    $transaksi = TransaksiKas::create([
                        'buku_kas_id' => $bukuKas->id,
                        'jenis_transaksi' => 'pengeluaran',
                        'kategori' => $this->getPengeluaranKategori(),
                        'kode_transaksi' => TransaksiKas::generateKodeTransaksi('pengeluaran'),
                        'jumlah' => $amount,
                        'keterangan' => 'Sample pengeluaran #' . $i . ' dari ' . $bukuKas->nama_kas,
                        'metode_pembayaran' => $this->getRandomMetodePembayaran(),
                        'no_referensi' => 'REF-' . strtoupper(substr(md5(mt_rand()), 0, 8)),
                        'tanggal_transaksi' => $date,
                        'created_by' => $admin->id,
                        'approved_by' => $admin->id,
                        'status' => 'approved',
                    ]);
                    
                    // Update saldo buku kas
                    $bukuKas->updateSaldo($amount, 'keluar');
                }
            }
            
            // Transaksi Transfer
            for ($i = 1; $i <= 3; $i++) {
                $bukuKasSumber = $bukuKasList->random();
                $bukuKasTujuan = $bukuKasList->where('id', '!=', $bukuKasSumber->id)->random();
                
                $amount = rand(200000, 1000000);
                $date = Carbon::now()->subDays(rand(1, 15));
                
                $transaksi = TransaksiKas::create([
                    'buku_kas_id' => $bukuKasSumber->id,
                    'buku_kas_tujuan_id' => $bukuKasTujuan->id,
                    'jenis_transaksi' => 'transfer',
                    'kategori' => 'Transfer Kas',
                    'kode_transaksi' => TransaksiKas::generateKodeTransaksi('transfer'),
                    'jumlah' => $amount,
                    'keterangan' => 'Sample transfer #' . $i . ' dari ' . $bukuKasSumber->nama_kas . ' ke ' . $bukuKasTujuan->nama_kas,
                    'metode_pembayaran' => 'Transfer',
                    'no_referensi' => 'TRF-' . strtoupper(substr(md5(mt_rand()), 0, 8)),
                    'tanggal_transaksi' => $date,
                    'created_by' => $admin->id,
                    'approved_by' => $admin->id,
                    'status' => 'approved',
                ]);
                
                // Update saldo buku kas
                $bukuKasSumber->updateSaldo($amount, 'keluar');
                $bukuKasTujuan->updateSaldo($amount, 'masuk');
            }
            
            // Transaksi Pending
            for ($i = 1; $i <= 2; $i++) {
                $bukuKas = $bukuKasList->random();
                $jenis = $i % 2 === 0 ? 'pemasukan' : 'pengeluaran';
                $amount = rand(100000, 500000);
                $date = Carbon::now();
                
                $transaksi = TransaksiKas::create([
                    'buku_kas_id' => $bukuKas->id,
                    'jenis_transaksi' => $jenis,
                    'kategori' => $jenis === 'pemasukan' ? $this->getPemasukanKategori() : $this->getPengeluaranKategori(),
                    'kode_transaksi' => TransaksiKas::generateKodeTransaksi($jenis),
                    'jumlah' => $amount,
                    'keterangan' => 'Transaksi pending #' . $i . ' perlu disetujui',
                    'metode_pembayaran' => $this->getRandomMetodePembayaran(),
                    'no_referensi' => 'PENDING-' . strtoupper(substr(md5(mt_rand()), 0, 8)),
                    'tanggal_transaksi' => $date,
                    'created_by' => $admin->id,
                    'status' => 'pending',
                ]);
            }
            
            DB::commit();
            
            $this->command->info('Berhasil menambahkan data sample transaksi kas.');
            
        } catch (\Exception $e) {
            DB::rollback();
            $this->command->error('Gagal menambahkan data: ' . $e->getMessage());
        }
    }
    
    private function getPemasukanKategori()
    {
        $kategori = [
            'Pembayaran Santri',
            'Sumbangan',
            'Hibah',
            'Dana Bantuan',
            'Lainnya'
        ];
        
        return $kategori[array_rand($kategori)];
    }
    
    private function getPengeluaranKategori()
    {
        $kategori = [
            'Operasional',
            'Gaji',
            'Pemeliharaan',
            'Pembangunan',
            'ATK',
            'Konsumsi',
            'Lainnya'
        ];
        
        return $kategori[array_rand($kategori)];
    }
    
    private function getRandomMetodePembayaran()
    {
        $metode = [
            'Tunai',
            'Transfer Bank',
            'QRIS'
        ];
        
        return $metode[array_rand($metode)];
    }
}
