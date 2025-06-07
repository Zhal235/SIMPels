<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Santri;
use App\Models\TagihanSantri;
use Carbon\Carbon;

class PembayaranSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing payment records for clean seeding
        DB::table('pembayarans')->truncate();
        
        // We'll update the tagihan_santris.nominal_dibayar in place
        $allTagihan = TagihanSantri::all();
        $santriIds = Santri::pluck('id')->toArray();

        // Pick 10 random santri for partial/unpaid debt in 2023/2024
        $specialSantris = (array) array_rand(array_flip($santriIds), 10);
        
        // For each special santri, randomly select 1-3 months to leave unpaid
        $unpaidMonths = [];
        foreach ($specialSantris as $santriId) {
            $unpaidCount = rand(1, 3);
            $possibleMonths = ['2023-10', '2023-11', '2023-12', '2024-01', '2024-02', '2024-03'];
            shuffle($possibleMonths);
            $unpaidMonths[$santriId] = array_slice($possibleMonths, 0, $unpaidCount);
        }

        foreach ($allTagihan as $tagihan) {
            $tahunBln = $tagihan->bulan;
            $tahun = explode('-', $tahunBln)[0];
            
            $isSpecial = in_array($tagihan->santri_id, $specialSantris);
            $isUnpaidMonth = $isSpecial && in_array($tahunBln, $unpaidMonths[$tagihan->santri_id] ?? []);
            
            // Calculate payment date (usually between due date and next month)
            $jatuhTempo = $tagihan->tanggal_jatuh_tempo ?? Carbon::now();
            
            if ($isUnpaidMonth) {
                // This is a month that should remain unpaid for special santri
                $dibayar = 0;
                $tanggalBayar = null;
            } else {
                // For regular payment:
                // 1. ≥50% santri fully paid each period
                // 2. Others have random payment amount (10% - 90% paid)
                
                // Calculate future date for payment relative to the current month of the tagihan
                // Expected behavior: payment date is always after the tagihan month and most are after due date
                $paymentMonthOffset = rand(-5, 15); // Days offset from jatuh tempo
                $tanggalBayar = Carbon::parse($jatuhTempo)->addDays($paymentMonthOffset);
                
                // If payment date is in future compared to now, set it to null
                if ($tanggalBayar->greaterThan(Carbon::now())) {
                    $tanggalBayar = null;
                    $dibayar = 0;
                } else {
                    // Full payment for ≥50% tagihan, partial for the rest
                    $paymentProbability = rand(1, 100); 
                    
                    if ($paymentProbability <= 50) {
                        $dibayar = $tagihan->nominal_tagihan;
                    } else {
                        // Random payment between 10%-90% of total
                        $dibayar = round($tagihan->nominal_tagihan * rand(10, 90) / 100, 2);
                    }
                }
            }
            
            // Update tagihan with payment information
            $tagihan->update([
                'nominal_dibayar' => $dibayar,
                'status' => $dibayar >= $tagihan->nominal_tagihan ? 'lunas' : 'aktif',
            ]);
            
            // If there's a payment, create payment record(s)
            if ($dibayar > 0 && $tanggalBayar) {
                // Create 1 or 2 payments based on random chance to simulate split payments
                if ($dibayar == $tagihan->nominal_tagihan && rand(1, 10) > 7) {
                    // Split payment into two transactions
                    $firstAmount = round($dibayar * rand(30, 70) / 100, 2);
                    $secondAmount = $dibayar - $firstAmount;
                    
                    // First payment always closer to the due date
                    $firstDate = $tanggalBayar->copy()->subDays(rand(1, 5));
                    
                    // Insert first payment
                    DB::table('pembayarans')->insert([
                        'tagihan_santri_id' => $tagihan->id,
                        'nominal' => $firstAmount,
                        'metode' => rand(1, 5) > 1 ? 'tunai' : 'transfer',
                        'tanggal' => $firstDate->format('Y-m-d'),
                        'keterangan' => 'Pembayaran pertama',
                        'created_at' => $firstDate,
                        'updated_at' => $firstDate,
                    ]);
                    
                    // Insert second payment
                    DB::table('pembayarans')->insert([
                        'tagihan_santri_id' => $tagihan->id,
                        'nominal' => $secondAmount,
                        'metode' => rand(1, 5) > 1 ? 'tunai' : 'transfer',
                        'tanggal' => $tanggalBayar->format('Y-m-d'),
                        'keterangan' => 'Pelunasan',
                        'created_at' => $tanggalBayar,
                        'updated_at' => $tanggalBayar,
                    ]);
                } else {
                    // Single payment transaction
                    DB::table('pembayarans')->insert([
                        'tagihan_santri_id' => $tagihan->id,
                        'nominal' => $dibayar,
                        'metode' => rand(1, 5) > 1 ? 'tunai' : 'transfer',
                        'tanggal' => $tanggalBayar->format('Y-m-d'),
                        'keterangan' => $dibayar >= $tagihan->nominal_tagihan ? 'Pembayaran lunas' : 'Pembayaran sebagian',
                        'created_at' => $tanggalBayar,
                        'updated_at' => $tanggalBayar,
                    ]);
                }
            }
        }
    }
}
