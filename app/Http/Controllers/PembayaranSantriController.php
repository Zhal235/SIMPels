<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\JenisTagihan;
use App\Models\Transaksi;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;

class PembayaranSantriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua santri aktif dengan relasi asrama dan kelas
        $santris = Santri::where('status', 'aktif')
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

        // Ambil kategori keuangan untuk jenis pembayaran berdasarkan tahun ajaran aktif
        $activeTahunAjaran = TahunAjaran::getActive();
        $jenisTagihans = JenisTagihan::activeYear()->get();
        

        return view('keuangan.pembayaran_santri.index', compact('santris', 'jenisTagihans', 'activeTahunAjaran'));
    }

    /**
     * Get payment data for a specific student
     */
    public function getPaymentData($santriId)
    {
        // Ambil data transaksi untuk santri tertentu berdasarkan tahun ajaran aktif
        $transaksi = Transaksi::where('santri_id', $santriId)
            ->activeYear()
            ->with(['jenisTagihan', 'tahunAjaran'])
            ->orderBy('tanggal', 'desc')
            ->get();
            
        return response()->json($transaksi);
    }

    /**
     * Process payment for student
     */
    public function processPayment(Request $request)
    {
        $validated = $request->validate([
            'santri_id' => 'required|exists:santris,id',
            'payments' => 'required|array',
            'total_amount' => 'required|numeric',
            'received_amount' => 'required|numeric',
            'save_to_wallet' => 'boolean',
        ]);
        
        DB::beginTransaction();
        
        try {
            $activeTahunAjaran = TahunAjaran::getActive();
            if (!$activeTahunAjaran) {
                return response()->json(['success' => false, 'message' => 'Tidak ada tahun ajaran aktif'], 400);
            }

            foreach ($request->payments as $payment) {
                Transaksi::create([
                    'santri_id' => $request->santri_id,
                    'jenis_pembayaran_id' => $payment['jenis_pembayaran_id'],
                    'tipe_pembayaran' => $payment['tipe_pembayaran'] ?? 'penuh',
                    'nominal' => $payment['nominal'],
                    'tanggal' => now(),
                    'keterangan' => $payment['keterangan'] ?? 'Pembayaran ' . ($payment['jenis_pembayaran'] ?? $payment['jenis_pembayaran_id']),
                    'tahun_ajaran_id' => $activeTahunAjaran->id,
                ]);
            }
            
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pembayaran berhasil disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan pembayaran: ' . $e->getMessage()], 500);
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