<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\TagihanSantri;
use App\Models\Transaksi;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;

class PembayaranSantriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check if santri_id is provided in the request
        $selectedSantriId = $request->input('santri_id');
        
        // Ambil semua santri aktif dengan relasi asrama dan kelas
        $santris = Santri::where(function($query) {
            // Include active students by default
            $query->where('status', 'aktif');
            
            // Also include mutasi and alumni students if they have unpaid bills
            $query->orWhereHas('tagihan', function($tagihan) {
                $tagihan->whereRaw('nominal_dibayar + nominal_keringanan < nominal_tagihan')
                       ->where('status', 'aktif');
            });
        })
            ->with(['asrama', 'asrama_anggota_terakhir.asrama', 'kelasRelasi'])
            ->orderBy('nama_santri')
            ->get()
            ->map(function ($santri) {
                // Ambil kelas menggunakan kelasRelasi (sama seperti di RFID tags)
                $kelas = $santri->kelasRelasi->pluck('nama')->join(', ') ?: 'Belum ada kelas';
                
                // Ambil asrama terakhir
                $asramaAnggota = $santri->asrama_anggota_terakhir;
                $asrama = $asramaAnggota ? $asramaAnggota->asrama->nama_asrama : 'Belum ada asrama';
                
                return [
                    'id' => $santri->id,
                    'nama' => $santri->nama_santri,
                    'nis' => $santri->nis,
                    'tempat_lahir' => $santri->tempat_lahir,
                    'tanggal_lahir' => $santri->tanggal_lahir,
                    'kelas' => $kelas,
                    'asrama' => $asrama,
                    'nama_ortu' => $santri->nama_ayah,
                    'no_hp' => $santri->hp_ayah,
                    'foto' => $santri->foto ? asset('storage/' . $santri->foto) : asset('img/default-avatar.png')
                ];
            });

        // Ambil tahun ajaran aktif
        $activeTahunAjaran = TahunAjaran::getActive();
        
        // Ambil tagihan santri untuk referensi jenis tagihan yang tersedia
        $jenisTagihans = collect();
        if ($activeTahunAjaran) {
            $jenisTagihans = TagihanSantri::where('tahun_ajaran_id', $activeTahunAjaran->id)
                ->with('jenisTagihan')
                ->distinct('jenis_tagihan_id')
                ->get()
                ->pluck('jenisTagihan')
                ->unique('id')
                ->values();
        }
        
        // Handle selected santri if santri_id is provided
        $selectedSantri = null;
        if ($selectedSantriId) {
            $selectedSantri = $santris->where('id', $selectedSantriId)->first();
            
            // If found, pre-select this santri's payment data
            if ($selectedSantri) {
                $this->getPaymentData($selectedSantriId); // This data will be loaded via AJAX
            }
        }

        return view('keuangan.pembayaran_santri.index', compact(
            'santris', 
            'jenisTagihans', 
            'activeTahunAjaran',
            'selectedSantri',
            'selectedSantriId'
        ));
    }

    /**
     * Get payment data for a specific student
     */
    public function getPaymentData($santriId)
    {
        try {
            \Log::info('getPaymentData called for santri_id: ' . $santriId);
            
            $activeTahunAjaran = TahunAjaran::getActive();
            if (!$activeTahunAjaran) {
                \Log::error('No active tahun ajaran found');
                return response()->json(['error' => 'Tidak ada tahun ajaran aktif'], 400);
            }
            
            \Log::info('Active tahun ajaran: ' . $activeTahunAjaran->id);

            // Ambil tagihan santri untuk santri tertentu berdasarkan tahun ajaran aktif
            $tagihanSantri = TagihanSantri::where('santri_id', $santriId)
                ->where('tahun_ajaran_id', $activeTahunAjaran->id)
                ->with(['jenisTagihan', 'tahunAjaran', 'transaksis'])
                ->orderBy('jenis_tagihan_id')
                ->get();
                
            \Log::info('Found tagihan count: ' . $tagihanSantri->count());
            
            $payments = $tagihanSantri->map(function ($tagihan) {
                    return [
                        'id' => $tagihan->id,
                        'jenis_tagihan' => $tagihan->jenisTagihan->nama,
                        'jenis_tagihan_id' => $tagihan->jenis_tagihan_id,
                        'bulan' => $tagihan->bulan,
                        'nominal_tagihan' => $tagihan->nominal_tagihan,
                        'nominal_dibayar' => $tagihan->nominal_dibayar,
                        'sisa_tagihan' => $tagihan->sisa_tagihan,
                        'status_pembayaran' => $tagihan->status_pembayaran,
                        'tanggal_jatuh_tempo' => $tagihan->tanggal_jatuh_tempo,
                        'keterangan' => $tagihan->keterangan,
                        'kategori_tagihan' => $tagihan->jenisTagihan->kategori_tagihan ?? 'Rutin',
                        'is_bulanan' => $tagihan->jenisTagihan->is_bulanan ?? false,
                        'transaksis' => $tagihan->transaksis->map(function ($transaksi) {
                            return [
                                'id' => $transaksi->id,
                                'nominal' => $transaksi->nominal,
                                'tanggal' => $transaksi->tanggal,
                                'keterangan' => $transaksi->keterangan,
                                'tipe_pembayaran' => $transaksi->tipe_pembayaran,
                            ];
                        })
                    ];
                });
                
            return response()->json($payments);
        } catch (\Exception $e) {
            \Log::error('Error in getPaymentData: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Process payment for student
     */
    public function processPayment(Request $request)
    {
        try {
            // Log request data untuk debugging
            \Log::info('Payment request data:', $request->all());
            
            $validated = $request->validate([
                'santri_id' => 'required|exists:santris,id',
                'payments' => 'required|array|min:1',
                'payments.*.tagihan_santri_id' => 'required|exists:tagihan_santris,id',
                'payments.*.nominal' => 'required|numeric|min:1',
                'payments.*.keterangan' => 'nullable|string',
                'total_amount' => 'required|numeric|min:1',
                'received_amount' => 'required|numeric|min:1',
                'save_to_wallet' => 'nullable|boolean',
            ]);
            
            DB::beginTransaction();
            
            try {
                $activeTahunAjaran = TahunAjaran::getActive();
                if (!$activeTahunAjaran) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Tidak ada tahun ajaran aktif'], 400);
                }
                
                // Ambil data santri untuk referensi
                $santri = Santri::find($request->santri_id);
                if (!$santri) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Santri tidak ditemukan'], 400);
                }

                foreach ($request->payments as $payment) {
                    // Ambil data tagihan santri
                    $tagihanSantri = TagihanSantri::find($payment['tagihan_santri_id']);
                    if (!$tagihanSantri) {
                        DB::rollBack();
                        return response()->json(['success' => false, 'message' => 'Tagihan santri tidak ditemukan'], 400);
                    }

                    // Validasi nominal tidak melebihi sisa tagihan
                    if ($payment['nominal'] > $tagihanSantri->sisa_tagihan) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false, 
                            'message' => 'Nominal pembayaran melebihi sisa tagihan untuk ' . $tagihanSantri->jenisTagihan->nama_tagihan
                        ], 400);
                    }

                    // Tentukan tipe pembayaran berdasarkan karakteristik tagihan
                    $jenisTagihan = $tagihanSantri->jenisTagihan;
                    $tipePembayaran = 'sekali_bayar'; // default
                    
                    if ($jenisTagihan->is_bulanan) {
                        $tipePembayaran = 'bulanan';
                    } else if ($jenisTagihan->kategori_tagihan === 'Rutin') {
                        // Rutin non-bulanan bisa tahunan atau sekali bayar
                        $tipePembayaran = 'tahunan';
                    } else {
                        // Insidentil umumnya sekali bayar
                        $tipePembayaran = 'sekali_bayar';
                    }
                    
                    // Jika pembayaran tidak penuh, maka ini cicilan
                    if ($payment['nominal'] < $tagihanSantri->sisa_tagihan) {
                        $tipePembayaran = 'cicilan';
                    }

                    // Buat transaksi baru
                    $transaksi = Transaksi::create([
                        'santri_id' => $request->santri_id,
                        'tagihan_santri_id' => $payment['tagihan_santri_id'],
                        'tipe_pembayaran' => $tipePembayaran,
                        'nominal' => $payment['nominal'],
                        'tanggal' => now(),
                        'keterangan' => $payment['keterangan'] ?? 'Pembayaran ' . $tagihanSantri->jenisTagihan->nama_tagihan,
                        'tahun_ajaran_id' => $activeTahunAjaran->id,
                    ]);

                    // Update tagihan santri
                    $tagihanSantri->updatePembayaran();
                    
                    // Buat Transaksi Kas untuk mencatat pemasukan di buku kas terkait
                    if ($jenisTagihan->buku_kas_id) {
                        // Buat TransaksiKas baru untuk mencatat pemasukan pembayaran
                        $transaksiKas = new \App\Models\TransaksiKas();
                        $transaksiKas->buku_kas_id = $jenisTagihan->buku_kas_id;
                        $transaksiKas->jenis_transaksi = 'pemasukan';
                        $transaksiKas->kategori = 'Pembayaran Santri';
                        $transaksiKas->kode_transaksi = \App\Models\TransaksiKas::generateKodeTransaksi('pemasukan');
                        $transaksiKas->jumlah = $payment['nominal'];
                        $transaksiKas->keterangan = 'Pembayaran ' . $jenisTagihan->nama . ' oleh ' . $santri->nama_santri . ' (' . $santri->nis . ')';
                        $transaksiKas->metode_pembayaran = 'Tunai';
                        $transaksiKas->tanggal_transaksi = now();
                        $transaksiKas->status = 'approved';
                        $transaksiKas->created_by = auth()->id();
                        $transaksiKas->approved_by = auth()->id();
                        $transaksiKas->tagihan_santri_id = $payment['tagihan_santri_id'];
                        $transaksiKas->save();
                        
                        // Update saldo buku kas
                        $bukuKas = \App\Models\BukuKas::find($jenisTagihan->buku_kas_id);
                        if ($bukuKas) {
                            $bukuKas->updateSaldo($payment['nominal'], 'masuk');
                        }
                    } else {
                        \Log::warning('Pembayaran tagihan tanpa buku kas terkait: Tagihan #' . $payment['tagihan_santri_id']);
                    }
                }
                
                DB::commit();
                return response()->json([
                    'success' => true, 
                    'message' => 'Pembayaran berhasil disimpan'
                ]);
                
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Database error in processPayment:', ['error' => $e->getMessage()]);
                return response()->json([
                    'success' => false, 
                    'message' => 'Gagal menyimpan pembayaran: ' . $e->getMessage()
                ], 500);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in processPayment:', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('General error in processPayment:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the receipt page.
     */
    public function kwitansi()
    {
        // Halaman kwitansi hanya menampilkan template, data diambil dari localStorage
        return view('pembayaran_santri.kwitansi');
    }
}