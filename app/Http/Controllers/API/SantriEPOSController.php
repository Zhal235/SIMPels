<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use App\Models\RfidTag;
use App\Models\Dompet;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SantriEPOSController extends Controller
{
    /**
     * Ambil data santri berdasarkan RFID tag
     */
    public function getByRfid($tag): JsonResponse
    {
        try {
            // Cari RFID tag
            $rfidTag = RfidTag::where('tag_number', $tag)
                             ->where('status', 'aktif')
                             ->first();

            if (!$rfidTag) {
                return response()->json([
                    'success' => false,
                    'message' => 'RFID tag tidak ditemukan atau tidak aktif',
                    'data' => null
                ], 404);
            }

            // Ambil data santri
            $santri = Santri::with(['asrama', 'kelas', 'dompet'])
                           ->where('id', $rfidTag->santri_id)
                           ->where('status', 'aktif')
                           ->first();

            if (!$santri) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data santri tidak ditemukan atau tidak aktif',  
                    'data' => null
                ], 404);
            }

            // Format response
            $responseData = [
                'id' => $santri->id,
                'nis' => $santri->nis,
                'nama_santri' => $santri->nama_santri,
                'kelas' => $santri->kelas->nama ?? null,
                'asrama' => $santri->asrama->nama ?? null,
                'rfid_tag' => $tag,
                'saldo' => $santri->dompet->saldo ?? 0,
                'status' => $santri->status,
                'foto' => $santri->foto
            ];

            return response()->json([
                'success' => true,
                'message' => 'Data santri berhasil ditemukan',
                'data' => $responseData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error mengambil data santri: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Cek saldo dompet santri
     */
    public function getSaldo($santriId): JsonResponse
    {
        try {
            $santri = Santri::with('dompet')->findOrFail($santriId);

            return response()->json([
                'success' => true,
                'message' => 'Saldo berhasil diambil',
                'data' => [
                    'santri_id' => $santri->id,
                    'nama_santri' => $santri->nama_santri,
                    'saldo' => $santri->dompet->saldo ?? 0,
                    'formatted_saldo' => 'Rp ' . number_format($santri->dompet->saldo ?? 0, 0, ',', '.')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Santri tidak ditemukan',
                'data' => null
            ], 404);
        }
    }

    /**
     * Potong saldo santri untuk transaksi ePOS
     */
    public function deductSaldo(Request $request, $santriId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'nominal' => 'required|numeric|min:1',
                'keterangan' => 'required|string|max:255',
                'transaction_ref' => 'required|string|max:255', // Referensi transaksi dari ePOS
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $santri = Santri::with('dompet')->findOrFail($santriId);
            
            if (!$santri->dompet) {
                throw new \Exception('Dompet santri tidak ditemukan');
            }

            $saldoSekarang = $santri->dompet->saldo;
            $nominal = $request->nominal;

            // Cek saldo mencukupi
            if ($saldoSekarang < $nominal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saldo tidak mencukupi',
                    'data' => [
                        'saldo_sekarang' => $saldoSekarang,
                        'nominal_transaksi' => $nominal,
                        'kekurangan' => $nominal - $saldoSekarang
                    ]
                ], 422);
            }

            // Update saldo dompet
            $santri->dompet->saldo -= $nominal;
            $santri->dompet->save();

            // Catat transaksi dompet
            \App\Models\TransaksiDompet::create([
                'santri_id' => $santri->id,
                'jenis_transaksi' => 'keluar',
                'nominal' => $nominal,
                'saldo_sebelum' => $saldoSekarang,
                'saldo_sesudah' => $saldoSekarang - $nominal,
                'keterangan' => 'Pembayaran ePOS: ' . $request->keterangan,
                'referensi' => $request->transaction_ref,
                'tanggal_transaksi' => now()
            ]);

            // Catat ke transaksi keuangan
            \App\Models\Transaksi::create([
                'santri_id' => $santri->id,
                'tipe_pembayaran' => 'epos',
                'nominal' => $nominal,
                'tanggal' => now(),
                'keterangan' => 'Transaksi ePOS: ' . $request->keterangan . ' (Ref: ' . $request->transaction_ref . ')',
                'tahun_ajaran_id' => 1 // Sesuaikan dengan tahun ajaran aktif
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Saldo berhasil dipotong',
                'data' => [
                    'santri_id' => $santri->id,
                    'nama_santri' => $santri->nama_santri,
                    'saldo_sebelum' => $saldoSekarang,
                    'nominal_transaksi' => $nominal,
                    'saldo_sesudah' => $saldoSekarang - $nominal,
                    'transaction_ref' => $request->transaction_ref
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error memotong saldo: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Top up saldo santri (untuk refund atau koreksi)
     */
    public function topUpSaldo(Request $request, $santriId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'nominal' => 'required|numeric|min:1',
                'keterangan' => 'required|string|max:255',
                'transaction_ref' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $santri = Santri::with('dompet')->findOrFail($santriId);
            
            if (!$santri->dompet) {
                // Buat dompet baru jika belum ada
                \App\Models\Dompet::create([
                    'santri_id' => $santri->id,
                    'saldo' => 0
                ]);
                $santri->refresh();
            }

            $saldoSekarang = $santri->dompet->saldo;
            $nominal = $request->nominal;

            // Update saldo dompet
            $santri->dompet->saldo += $nominal;
            $santri->dompet->save();

            // Catat transaksi dompet
            \App\Models\TransaksiDompet::create([
                'santri_id' => $santri->id,
                'jenis_transaksi' => 'masuk',
                'nominal' => $nominal,
                'saldo_sebelum' => $saldoSekarang,
                'saldo_sesudah' => $saldoSekarang + $nominal,
                'keterangan' => 'Top up dari ePOS: ' . $request->keterangan,
                'referensi' => $request->transaction_ref,
                'tanggal_transaksi' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Saldo berhasil ditambah',
                'data' => [
                    'santri_id' => $santri->id,
                    'nama_santri' => $santri->nama_santri,
                    'saldo_sebelum' => $saldoSekarang,
                    'nominal_transaksi' => $nominal,
                    'saldo_sesudah' => $saldoSekarang + $nominal,
                    'transaction_ref' => $request->transaction_ref
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error menambah saldo: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Ambil history transaksi santri
     */
    public function getTransactionHistory($santriId, Request $request): JsonResponse
    {
        try {
            $santri = Santri::findOrFail($santriId);

            $query = \App\Models\TransaksiDompet::where('santri_id', $santriId);

            // Filter berdasarkan tanggal
            if ($request->has('start_date') && $request->start_date) {
                $query->whereDate('tanggal_transaksi', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date) {
                $query->whereDate('tanggal_transaksi', '<=', $request->end_date);
            }

            // Filter berdasarkan jenis transaksi
            if ($request->has('jenis') && $request->jenis) {
                $query->where('jenis_transaksi', $request->jenis);
            }

            $transactions = $query->orderBy('tanggal_transaksi', 'desc')
                                 ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'message' => 'History transaksi berhasil diambil',
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
}
