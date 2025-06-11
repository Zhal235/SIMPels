<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\TransaksiDompet;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TransaksiEPOSController extends Controller
{
    /**
     * Terima dan simpan transaksi dari sistem ePOS
     */
    public function syncFromEPOS(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'epos_transaction_id' => 'required|string|unique:keuangan_transaksis,keterangan',
                'santri_id' => 'required|exists:santris,id',
                'total_amount' => 'required|numeric|min:1',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|string',
                'items.*.product_name' => 'required|string',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.subtotal' => 'required|numeric|min:0',
                'payment_method' => 'required|in:rfid,cash,card',
                'transaction_date' => 'nullable|date',
                'cashier_name' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Format keterangan dengan detail items
            $itemsDetail = [];
            foreach ($request->items as $item) {
                $itemsDetail[] = $item['product_name'] . ' (' . $item['quantity'] . 'x)';
            }
            $keterangan = 'Transaksi ePOS #' . $request->epos_transaction_id . ' - ' . implode(', ', $itemsDetail);
            
            if ($request->cashier_name) {
                $keterangan .= ' | Kasir: ' . $request->cashier_name;
            }

            // Simpan ke tabel transaksi utama
            $transaksi = Transaksi::create([
                'santri_id' => $request->santri_id,
                'tipe_pembayaran' => 'epos_' . $request->payment_method,
                'nominal' => $request->total_amount,
                'tanggal' => $request->transaction_date ?? now(),
                'keterangan' => $keterangan,
                'tahun_ajaran_id' => 1 // Sesuaikan dengan tahun ajaran aktif
            ]);

            // Jika pembayaran dengan RFID, catat juga di transaksi dompet
            if ($request->payment_method === 'rfid') {
                $santri = \App\Models\Santri::with('dompet')->findOrFail($request->santri_id);
                
                if ($santri->dompet) {
                    $saldoSekarang = $santri->dompet->saldo;
                    
                    TransaksiDompet::create([
                        'santri_id' => $request->santri_id,
                        'jenis_transaksi' => 'keluar',
                        'nominal' => $request->total_amount,
                        'saldo_sebelum' => $saldoSekarang,
                        'saldo_sesudah' => $saldoSekarang - $request->total_amount,
                        'keterangan' => 'Transaksi ePOS #' . $request->epos_transaction_id,
                        'referensi' => $request->epos_transaction_id,
                        'tanggal_transaksi' => $request->transaction_date ?? now()
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disinkronisasi',
                'data' => [
                    'simpels_transaction_id' => $transaksi->id,
                    'epos_transaction_id' => $request->epos_transaction_id,
                    'santri_id' => $request->santri_id,
                    'total_amount' => $request->total_amount,
                    'sync_date' => now()
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error sinkronisasi transaksi: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Ambil transaksi santri untuk ePOS
     */
    public function getTransactionHistory($santriId, Request $request): JsonResponse
    {
        try {
            $santri = \App\Models\Santri::findOrFail($santriId);

            $query = Transaksi::where('santri_id', $santriId)
                             ->where('tipe_pembayaran', 'LIKE', 'epos%');

            // Filter berdasarkan tanggal
            if ($request->has('start_date') && $request->start_date) {
                $query->whereDate('tanggal', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date) {
                $query->whereDate('tanggal', '<=', $request->end_date);
            }

            $transactions = $query->orderBy('tanggal', 'desc')
                                 ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'message' => 'History transaksi ePOS berhasil diambil',
                'data' => [
                    'santri' => [
                        'id' => $santri->id,
                        'nama_santri' => $santri->nama_santri,
                        'nis' => $santri->nis
                    ],
                    'transactions' => $transactions
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error mengambil history transaksi: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Batalkan transaksi ePOS (untuk refund)
     */
    public function cancelTransaction(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'epos_transaction_id' => 'required|string',
                'refund_amount' => 'required|numeric|min:1',
                'reason' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Cari transaksi asli
            $originalTransaction = Transaksi::where('keterangan', 'LIKE', '%' . $request->epos_transaction_id . '%')
                                          ->first();

            if (!$originalTransaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan',
                    'data' => null
                ], 404);
            }

            // Buat transaksi refund
            $refundTransaction = Transaksi::create([
                'santri_id' => $originalTransaction->santri_id,
                'tipe_pembayaran' => 'epos_refund',
                'nominal' => -$request->refund_amount, // Negatif untuk refund
                'tanggal' => now(),
                'keterangan' => 'Refund ePOS #' . $request->epos_transaction_id . ' - ' . $request->reason,
                'tahun_ajaran_id' => $originalTransaction->tahun_ajaran_id
            ]);

            // Jika transaksi asli menggunakan RFID, kembalikan saldo
            if (str_contains($originalTransaction->tipe_pembayaran, 'rfid')) {
                $santri = \App\Models\Santri::with('dompet')->findOrFail($originalTransaction->santri_id);
                
                if ($santri->dompet) {
                    $saldoSekarang = $santri->dompet->saldo;
                    
                    // Update saldo dompet
                    $santri->dompet->saldo += $request->refund_amount;
                    $santri->dompet->save();
                    
                    // Catat transaksi dompet
                    TransaksiDompet::create([
                        'santri_id' => $originalTransaction->santri_id,
                        'jenis_transaksi' => 'masuk',
                        'nominal' => $request->refund_amount,
                        'saldo_sebelum' => $saldoSekarang,
                        'saldo_sesudah' => $saldoSekarang + $request->refund_amount,
                        'keterangan' => 'Refund ePOS #' . $request->epos_transaction_id,
                        'referensi' => $request->epos_transaction_id . '_REFUND',
                        'tanggal_transaksi' => now()
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibatalkan dan saldo dikembalikan',
                'data' => [
                    'original_transaction_id' => $originalTransaction->id,
                    'refund_transaction_id' => $refundTransaction->id,
                    'refund_amount' => $request->refund_amount,
                    'refund_date' => now()
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error membatalkan transaksi: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Ambil statistik transaksi ePOS
     */
    public function getStatistics(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', today()->format('Y-m-d'));
            $endDate = $request->get('end_date', today()->format('Y-m-d'));

            $stats = [
                'total_transactions' => Transaksi::where('tipe_pembayaran', 'LIKE', 'epos%')
                                                ->whereBetween('tanggal', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                                                ->count(),
                
                'total_revenue' => Transaksi::where('tipe_pembayaran', 'LIKE', 'epos%')
                                          ->where('nominal', '>', 0) // Exclude refunds
                                          ->whereBetween('tanggal', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                                          ->sum('nominal'),
                
                'total_refunds' => abs(Transaksi::where('tipe_pembayaran', 'epos_refund')
                                              ->whereBetween('tanggal', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                                              ->sum('nominal')),
                
                'rfid_transactions' => Transaksi::where('tipe_pembayaran', 'epos_rfid')
                                                ->whereBetween('tanggal', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                                                ->count(),
                
                'cash_transactions' => Transaksi::where('tipe_pembayaran', 'epos_cash')
                                                ->whereBetween('tanggal', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                                                ->count(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Statistik transaksi ePOS berhasil diambil',
                'data' => [
                    'period' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate
                    ],
                    'statistics' => $stats
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error mengambil statistik: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
