<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use App\Models\WaliSantri;
use App\Models\Dompet;
use App\Models\DompetLimit;
use App\Models\TagihanSantri;
use App\Models\TransaksiDompet;
use App\Models\Perizinan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WaliSantriController extends Controller
{
    /**
     * Mengambil daftar santri yang dimiliki oleh wali santri yang login
     * 
     * @return \Illuminate\Http\JsonResponse
     */public function getSantri()
    {
        $wali = auth()->user();
        $santri = $wali->santri()
            ->with(['asrama_anggota.asrama', 'kelasRelasi'])
            ->get()
            ->map(function ($santri) {
                // Format data santri sesuai kebutuhan PWA
                $kelas = $santri->kelasRelasi->pluck('nama')->join(', ') ?: '';
                $jurusan = $santri->kelas ?? ''; // Ambil jurusan jika tersedia

                // Ambil asrama dan kamar
                $asramaAnggota = $santri->asrama_anggota->last();
                $asrama = $asramaAnggota && $asramaAnggota->asrama ? $asramaAnggota->asrama->nama : '';
                $kamar = $asramaAnggota ? $asramaAnggota->kamar : '';

                // Ambil data musyrif (wali kelas atau pembina asrama)
                $musyrif = $santri->musyrif ?? '';
                $hp_musyrif = $santri->hp_musyrif ?? '';

                return [
                    'id' => $santri->id,
                    'nama' => $santri->nama_santri,
                    'nis' => $santri->nis,
                    'nisn' => $santri->nisn,
                    'kelas' => $kelas,
                    'jurusan' => $jurusan,
                    'asrama' => $asrama,
                    'kamar' => $kamar,
                    'status' => $santri->status,
                    'tempat_lahir' => $santri->tempat_lahir,
                    'tanggal_lahir' => $santri->tanggal_lahir ? $santri->tanggal_lahir->format('Y-m-d') : null,
                    'jenis_kelamin' => $santri->jenis_kelamin,
                    'agama' => 'Islam', // Default untuk pesantren
                    'alamat' => $santri->alamat,
                    'tahun_masuk' => substr($santri->nis, 0, 4), // Asumsi 4 digit pertama NIS adalah tahun masuk
                    'musyrif' => $musyrif,
                    'hp_musyrif' => $hp_musyrif,
                    'nama_ayah' => $santri->nama_ayah,
                    'pekerjaan_ayah' => $santri->pekerjaan_ayah,
                    'hp_ayah' => $santri->hp_ayah,
                    'nama_ibu' => $santri->nama_ibu,
                    'pekerjaan_ibu' => $santri->pekerjaan_ibu,
                    'hp_ibu' => $santri->hp_ibu,
                    'golongan_darah' => $santri->golongan_darah ?? '',
                    'tinggi_badan' => $santri->tinggi_badan ?? null,
                    'berat_badan' => $santri->berat_badan ?? null,
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $santri
        ]);
    }
    
    /**
     * Mengambil daftar tagihan santri untuk wali yang login
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */    public function getTagihan(Request $request)
    {
        try {
            $wali = auth()->user();
            $santri_ids = $wali->santri()->pluck('id')->toArray();
            
            if (empty($santri_ids)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'summary' => [
                        'total_tagihan' => 0,
                        'sudah_bayar' => 0,
                        'belum_bayar' => 0,
                        'jumlah_tagihan_tertunggak' => 0
                    ]
                ]);
            }
            
            // Base query untuk tagihan santri
            $query = TagihanSantri::whereIn('santri_id', $santri_ids)
                ->with(['santri:id,nama_santri', 'jenisTagihan:id,nama,nominal']);
            
            // Filter berdasarkan santri_id
            if ($request->has('santri_id') && $request->santri_id) {
                $query->where('santri_id', $request->santri_id);
            }
            
            // Filter berdasarkan status pembayaran
            if ($request->has('status') && $request->status) {
                $statusFilter = $request->status;
                if ($statusFilter === 'belum_lunas') {
                    $query->where('status', '!=', 'lunas');
                } elseif ($statusFilter === 'lunas') {
                    $query->where('status', 'lunas');
                } elseif ($statusFilter === 'tertunggak') {
                    $query->where('status', '!=', 'lunas')
                          ->where('tanggal_jatuh_tempo', '<', now());
                }
            }
            
            // Filter berdasarkan periode/bulan
            if ($request->has('periode') && $request->periode) {
                $query->where('bulan', 'like', '%' . $request->periode . '%');
            }
            
            // Filter berdasarkan tahun ajaran
            if ($request->has('tahun_ajaran_id') && $request->tahun_ajaran_id) {
                $query->where('tahun_ajaran_id', $request->tahun_ajaran_id);
            }
            
            // Order by tanggal jatuh tempo dan bulan
            $query->orderBy('tanggal_jatuh_tempo', 'asc')
                  ->orderBy('bulan', 'asc');
            
            // Get tagihan
            $tagihan = $query->get();
            
            // Transform data dengan struktur yang benar
            $transformedTagihan = $tagihan->map(function ($item) {
                // Hitung sisa tagihan
                $sisa_tagihan = max(0, $item->nominal_tagihan - $item->nominal_dibayar - $item->nominal_keringanan);
                
                // Tentukan status berdasarkan pembayaran
                $status = 'belum_lunas';
                if ($item->nominal_dibayar >= $item->nominal_tagihan) {
                    $status = 'lunas';
                } elseif ($item->nominal_dibayar > 0) {
                    $status = 'sebagian';
                }
                
                // Check jika tertunggak
                $is_tertunggak = false;
                if ($status !== 'lunas' && $item->tanggal_jatuh_tempo && $item->tanggal_jatuh_tempo < now()) {
                    $is_tertunggak = true;
                }
                
                return [
                    'id' => $item->id,
                    'santri_id' => $item->santri_id,
                    'santri_nama' => $item->santri ? $item->santri->nama_santri : 'N/A',
                    'nis' => $item->santri ? $item->santri->nis : null,
                    'jenis_tagihan' => $item->jenisTagihan ? $item->jenisTagihan->nama : 'Tagihan',
                    'jenis_tagihan_id' => $item->jenis_tagihan_id,
                    'tahun_ajaran_id' => $item->tahun_ajaran_id,
                    'bulan' => $item->bulan,
                    'periode' => $item->bulan,
                    'nominal_tagihan' => (float) $item->nominal_tagihan,
                    'nominal_dibayar' => (float) $item->nominal_dibayar,
                    'nominal_keringanan' => (float) $item->nominal_keringanan,
                    'sisa_tagihan' => (float) $sisa_tagihan,
                    'jumlah' => (float) $item->nominal_tagihan, // alias untuk kompatibilitas
                    'sudah_bayar' => (float) $item->nominal_dibayar, // alias untuk kompatibilitas
                    'status' => $status,
                    'status_pembayaran' => $status, // alias untuk kompatibilitas
                    'is_tertunggak' => $is_tertunggak,
                    'tanggal_jatuh_tempo' => $item->tanggal_jatuh_tempo ? $item->tanggal_jatuh_tempo->format('Y-m-d') : null,
                    'keterangan' => $item->keterangan,
                    'created_at' => $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at' => $item->updated_at ? $item->updated_at->format('Y-m-d H:i:s') : null,
                ];
            });
            
            // Calculate summary yang akurat
            $total_tagihan = $tagihan->sum('nominal_tagihan');
            $total_dibayar = $tagihan->sum('nominal_dibayar');
            $total_keringanan = $tagihan->sum('nominal_keringanan');
            $total_sisa = max(0, $total_tagihan - $total_dibayar - $total_keringanan);
            
            $jumlah_lunas = $tagihan->filter(function($t) {
                return $t->nominal_dibayar >= $t->nominal_tagihan;
            })->count();
            
            $jumlah_belum_lunas = $tagihan->count() - $jumlah_lunas;
            
            $jumlah_tertunggak = $tagihan->filter(function($t) {
                return $t->nominal_dibayar < $t->nominal_tagihan && 
                       $t->tanggal_jatuh_tempo && 
                       $t->tanggal_jatuh_tempo < now();
            })->count();
            
            $summary = [
                'total_tagihan' => (float) $total_tagihan,
                'total_dibayar' => (float) $total_dibayar,
                'total_keringanan' => (float) $total_keringanan,
                'total_sisa' => (float) $total_sisa,
                'sudah_bayar' => (float) $total_dibayar, // alias
                'belum_bayar' => (float) $total_sisa, // alias
                'jumlah_tagihan_total' => $tagihan->count(),
                'jumlah_tagihan_lunas' => $jumlah_lunas,
                'jumlah_tagihan_belum_lunas' => $jumlah_belum_lunas,
                'jumlah_tagihan_tertunggak' => $jumlah_tertunggak
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Data tagihan berhasil diambil',
                'data' => $transformedTagihan,
                'summary' => $summary
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in getTagihan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data tagihan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Helper untuk mapping status pembayaran
     */
    private function mapStatusPembayaran($status)
    {
        $map = [
            'lunas' => 'lunas',
            'belum_bayar' => 'belum_lunas',
            'sebagian' => 'sebagian'
        ];
        
        return $map[$status] ?? $status;
    }
    
    /**
     * Mengambil daftar perizinan santri untuk wali yang login
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */    public function getPerizinan(Request $request)
    {
        $wali = auth()->user();
        $santri_ids = $wali->santri()->pluck('id')->toArray();
        
        // Base query
        $query = Perizinan::whereIn('santri_id', $santri_ids)
            ->with('santri:id,nama_santri');
        
        // Filter by santri_id
        if ($request->has('santri_id')) {
            $query->where('santri_id', $request->santri_id);
        }
        
        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Get data
        $perizinan = $query->orderBy('created_at', 'desc')->get();
        
        // Transform data
        $transformedPerizinan = $perizinan->map(function ($item) {
            return [
                'id' => $item->id,
                'santri_id' => $item->santri_id,
                'santri_nama' => $item->santri->nama_santri,
                'jenis_izin' => $item->jenis_izin,
                'keperluan' => $item->keperluan,
                'tanggal_mulai' => $item->tanggal_mulai->format('Y-m-d'),
                'tanggal_selesai' => $item->tanggal_selesai->format('Y-m-d'),
                'durasi_hari' => $item->durasi_hari,
                'status' => $item->status,
                'keterangan' => $item->keterangan,
                'catatan_admin' => $item->catatan_admin,
                'lampiran' => $item->lampiran,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $transformedPerizinan
        ]);
    }
    
    /**
     * Membuat perizinan baru
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createPerizinan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'santri_id' => 'required|exists:santris,id',
            'jenis_izin' => ['required', Rule::in(['pulang', 'sakit', 'keperluan_lain'])],
            'keperluan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'required|string',
            'lampiran' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
          $wali = auth()->user();
        
        // Verifikasi santri milik wali yang login
        $santri = $wali->santri()->find($request->santri_id);
        if (!$santri) {
            return response()->json([
                'success' => false,
                'message' => 'Santri tidak ditemukan'
            ], 404);
        }
        
        // Handle file upload
        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $lampiranPath = $file->store('public/perizinan');
            $lampiranPath = Storage::url($lampiranPath);
        }
        
        // Create perizinan
        $perizinan = Perizinan::create([
            'santri_id' => $request->santri_id,
            'jenis_izin' => $request->jenis_izin,
            'keperluan' => $request->keperluan,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'keterangan' => $request->keterangan,
            'status' => 'menunggu',
            'lampiran' => $lampiranPath,
            'created_by' => null, // Null karena dibuat oleh wali santri melalui API
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Perizinan berhasil diajukan',
            'data' => [
                'id' => $perizinan->id,
                'santri_id' => $perizinan->santri_id,
                'jenis_izin' => $perizinan->jenis_izin,
                'keperluan' => $perizinan->keperluan,
                'tanggal_mulai' => $perizinan->tanggal_mulai->format('Y-m-d'),
                'tanggal_selesai' => $perizinan->tanggal_selesai->format('Y-m-d'),
                'status' => $perizinan->status,
                'keterangan' => $perizinan->keterangan,
                'created_at' => $perizinan->created_at
            ]
        ], 201);
    }
    
    /**
     * Update perizinan yang statusnya masih "menunggu"
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */    public function updatePerizinan(Request $request, $id)
    {
        $wali = auth()->user();
        $santri_ids = $wali->santri()->pluck('id')->toArray();
        
        // Find perizinan
        $perizinan = Perizinan::whereIn('santri_id', $santri_ids)
            ->where('id', $id)
            ->where('status', 'menunggu')
            ->first();
        
        if (!$perizinan) {
            return response()->json([
                'success' => false,
                'message' => 'Perizinan tidak ditemukan atau tidak dapat diubah'
            ], 404);
        }
        
        // Validate request
        $validator = Validator::make($request->all(), [
            'jenis_izin' => ['required', Rule::in(['pulang', 'sakit', 'keperluan_lain'])],
            'keperluan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'required|string',
            'lampiran' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Handle file upload
        if ($request->hasFile('lampiran')) {
            // Delete old file if exists
            if ($perizinan->lampiran) {
                $oldPath = str_replace('/storage', 'public', $perizinan->lampiran);
                Storage::delete($oldPath);
            }
            
            $file = $request->file('lampiran');
            $lampiranPath = $file->store('public/perizinan');
            $lampiranPath = Storage::url($lampiranPath);
            
            $perizinan->lampiran = $lampiranPath;
        }
        
        // Update perizinan
        $perizinan->update([
            'jenis_izin' => $request->jenis_izin,
            'keperluan' => $request->keperluan,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'keterangan' => $request->keterangan,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Perizinan berhasil diperbarui',
            'data' => [
                'id' => $perizinan->id,
                'santri_id' => $perizinan->santri_id,
                'jenis_izin' => $perizinan->jenis_izin,
                'keperluan' => $perizinan->keperluan,
                'tanggal_mulai' => $perizinan->tanggal_mulai->format('Y-m-d'),
                'tanggal_selesai' => $perizinan->tanggal_selesai->format('Y-m-d'),
                'status' => $perizinan->status,
                'keterangan' => $perizinan->keterangan,
                'updated_at' => $perizinan->updated_at
            ]
        ]);
    }
    
    /**
     * Hapus perizinan yang statusnya masih "menunggu"
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */    public function deletePerizinan($id)
    {
        $wali = auth()->user();
        $santri_ids = $wali->santri()->pluck('id')->toArray();
        
        // Find perizinan
        $perizinan = Perizinan::whereIn('santri_id', $santri_ids)
            ->where('id', $id)
            ->where('status', 'menunggu')
            ->first();
        
        if (!$perizinan) {
            return response()->json([
                'success' => false,
                'message' => 'Perizinan tidak ditemukan atau tidak dapat dihapus'
            ], 404);
        }
        
        // Delete file if exists
        if ($perizinan->lampiran) {
            $path = str_replace('/storage', 'public', $perizinan->lampiran);
            Storage::delete($path);
        }
        
        // Delete perizinan
        $perizinan->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Perizinan berhasil dihapus'
        ]);
    }

    /**
     * Update santri data by wali santri
     */
    public function updateSantri(Request $request, $id)
    {
        try {
            $waliSantri = Auth::user();
            
            // Validate that this santri belongs to the authenticated wali
            $santri = $waliSantri->santri()->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'alamat' => 'string|max:255',
                'hp_ayah' => 'string|max:20',
                'hp_ibu' => 'string|max:20',
                'email_orangtua' => 'email|max:255',
                'no_hp_wali' => 'string|max:20',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Only allow updating specific fields for safety
            $allowedFields = ['alamat', 'hp_ayah', 'hp_ibu', 'email_orangtua', 'no_hp_wali'];
            $updateData = array_intersect_key($request->all(), array_flip($allowedFields));

            $santri->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Data santri berhasil diperbarui',
                'data' => $santri->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data santri: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dompet information for santri
     */
    public function getDompetInfo(Request $request)
    {
        try {
            $waliSantri = Auth::user();
            $santriId = $request->santri_id;

            if ($santriId) {
                // Get specific santri dompet
                $santri = $waliSantri->santri()->findOrFail($santriId);
                $dompets = collect([$santri]);
            } else {
                // Get all santri dompets
                $dompets = $waliSantri->santri();
            }

            $dompetData = $dompets->with(['dompet.dompetLimit', 'dompet.transaksiDompet' => function($query) {
                $query->latest()->limit(10);
            }])->get()->map(function ($santri) {
                $dompet = $santri->dompet;
                if (!$dompet) {
                    return [
                        'santri_id' => $santri->id,
                        'nama_santri' => $santri->nama_santri,
                        'has_dompet' => false,
                        'message' => 'Dompet belum diaktifkan'
                    ];
                }

                return [
                    'santri_id' => $santri->id,
                    'nama_santri' => $santri->nama_santri,
                    'has_dompet' => true,
                    'dompet_id' => $dompet->id,
                    'nomor_dompet' => $dompet->nomor_dompet,
                    'saldo' => $dompet->saldo,
                    'limit_transaksi' => $dompet->limit_transaksi,
                    'limit_harian' => $dompet->dompetLimit?->limit_harian ?? 0,
                    'limit_mingguan' => $dompet->dompetLimit?->limit_mingguan ?? 0,
                    'limit_bulanan' => $dompet->dompetLimit?->limit_bulanan ?? 0,
                    'is_active' => $dompet->is_active,
                    'transaksi_terakhir' => $dompet->transaksiDompet->map(function($transaksi) {
                        return [
                            'id' => $transaksi->id,
                            'jenis_transaksi' => $transaksi->jenis_transaksi,
                            'nominal' => $transaksi->nominal,
                            'keterangan' => $transaksi->keterangan,
                            'created_at' => $transaksi->created_at
                        ];
                    })
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data dompet berhasil diambil',
                'data' => $santriId ? $dompetData->first() : $dompetData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data dompet: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Top up saldo santri
     */
    public function topUpSaldo(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'santri_id' => 'required|exists:santris,id',
                'nominal' => 'required|numeric|min:10000|max:1000000',
                'metode_pembayaran' => 'required|in:transfer,tunai,kartu',
                'keterangan' => 'string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $waliSantri = Auth::user();
            $santri = $waliSantri->santri()->findOrFail($request->santri_id);
            
            if (!$santri->dompet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dompet santri belum diaktifkan'
                ], 400);
            }

            DB::beginTransaction();

            // Update saldo dompet
            $saldoSebelum = $santri->dompet->saldo;
            $santri->dompet->updateSaldo($request->nominal, 'tambah');

            // Create transaction record
            $transaksi = TransaksiDompet::create([
                'dompet_id' => $santri->dompet->id,
                'jenis_transaksi' => 'topup',
                'nominal' => $request->nominal,
                'saldo_sebelum' => $saldoSebelum,
                'saldo_sesudah' => $santri->dompet->saldo,
                'metode_pembayaran' => $request->metode_pembayaran,
                'keterangan' => $request->keterangan ?? 'Top up dari wali santri',
                'status' => 'berhasil',
                'referensi' => 'TOPUP-' . time(),
                'created_by' => $waliSantri->id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Top up berhasil',
                'data' => [
                    'transaksi_id' => $transaksi->id,
                    'nominal' => $request->nominal,
                    'saldo_sebelum' => $saldoSebelum,
                    'saldo_sesudah' => $santri->dompet->saldo,
                    'metode_pembayaran' => $request->metode_pembayaran
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan top up: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update limit harian santri
     */
    public function updateLimitHarian(Request $request, $santriId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'limit_harian' => 'required|numeric|min:0|max:500000',
                'limit_transaksi' => 'numeric|min:0|max:100000',
                'catatan' => 'string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $waliSantri = Auth::user();
            $santri = $waliSantri->santri()->findOrFail($santriId);
            
            if (!$santri->dompet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dompet santri belum diaktifkan'
                ], 400);
            }

            // Update atau create dompet limit
            $dompetLimit = $santri->dompet->dompetLimit;
            
            if ($dompetLimit) {
                $dompetLimit->update([
                    'limit_harian' => $request->limit_harian,
                    'limit_transaksi' => $request->limit_transaksi ?? $dompetLimit->limit_transaksi,
                    'catatan' => $request->catatan,
                    'updated_by' => $waliSantri->id
                ]);
            } else {
                $dompetLimit = DompetLimit::create([
                    'dompet_id' => $santri->dompet->id,
                    'limit_harian' => $request->limit_harian,
                    'limit_transaksi' => $request->limit_transaksi ?? 50000,
                    'catatan' => $request->catatan,
                    'is_active' => true,
                    'created_by' => $waliSantri->id
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Limit berhasil diperbarui',
                'data' => [
                    'limit_harian' => $dompetLimit->limit_harian,
                    'limit_transaksi' => $dompetLimit->limit_transaksi,
                    'catatan' => $dompetLimit->catatan
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui limit: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bayar tagihan (pay bills)
     */
    public function bayarTagihan(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tagihan_id' => 'required|exists:tagihan_santris,id',
                'nominal_bayar' => 'required|numeric|min:1000',
                'metode_pembayaran' => 'required|in:transfer,tunai,dompet',
                'keterangan' => 'string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $waliSantri = Auth::user();
            
            // Validate tagihan belongs to wali santri
            $tagihan = TagihanSantri::whereHas('santri', function($q) use ($waliSantri) {
                $q->where('wali_santri_id', $waliSantri->id);
            })->findOrFail($request->tagihan_id);

            $nominalBayar = $request->nominal_bayar;
            $sisaTagihan = $tagihan->nominal_harus_dibayar - $tagihan->nominal_dibayar;

            if ($nominalBayar > $sisaTagihan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nominal pembayaran melebihi sisa tagihan'
                ], 400);
            }

            DB::beginTransaction();

            // If using dompet, deduct from santri's dompet
            if ($request->metode_pembayaran === 'dompet') {
                $santri = $tagihan->santri;
                if (!$santri->dompet || $santri->dompet->saldo < $nominalBayar) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Saldo dompet tidak mencukupi'
                    ], 400);
                }

                $saldoSebelum = $santri->dompet->saldo;
                $santri->dompet->updateSaldo($nominalBayar, 'kurang');

                // Create dompet transaction
                TransaksiDompet::create([
                    'dompet_id' => $santri->dompet->id,
                    'jenis_transaksi' => 'pembayaran',
                    'nominal' => $nominalBayar,
                    'saldo_sebelum' => $saldoSebelum,
                    'saldo_sesudah' => $santri->dompet->saldo,
                    'metode_pembayaran' => 'dompet',
                    'keterangan' => 'Pembayaran tagihan: ' . $tagihan->jenisTagihan->nama,
                    'status' => 'berhasil',
                    'referensi' => 'PAY-' . $tagihan->id . '-' . time(),
                    'created_by' => $waliSantri->id
                ]);
            }

            // Update tagihan
            $tagihan->nominal_dibayar += $nominalBayar;
            
            if ($tagihan->nominal_dibayar >= $tagihan->nominal_harus_dibayar) {
                $tagihan->status = 'lunas';
            } else {
                $tagihan->status = 'cicilan';
            }
            
            $tagihan->save();

            // Create transaction record
            $transaksi = Transaksi::create([
                'santri_id' => $tagihan->santri_id,
                'tagihan_santri_id' => $tagihan->id,
                'jenis_tagihan_id' => $tagihan->jenis_tagihan_id,
                'tipe_pembayaran' => $request->metode_pembayaran,
                'nominal' => $nominalBayar,
                'tanggal' => now(),
                'keterangan' => $request->keterangan ?? 'Pembayaran dari wali santri',
                'tahun_ajaran_id' => $tagihan->tahun_ajaran_id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil',
                'data' => [
                    'transaksi_id' => $transaksi->id,
                    'tagihan_id' => $tagihan->id,
                    'nominal_bayar' => $nominalBayar,
                    'sisa_tagihan' => $tagihan->nominal_harus_dibayar - $tagihan->nominal_dibayar,
                    'status_tagihan' => $tagihan->status,
                    'metode_pembayaran' => $request->metode_pembayaran
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }
}
