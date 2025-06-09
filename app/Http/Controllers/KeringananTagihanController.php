<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Services\KeringananTagihanService;
use App\Models\KeringananTagihan;
use App\Models\Santri;
use App\Models\TahunAjaran;
use App\Models\JenisTagihan;
use App\Models\TagihanSantri;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KeringananTagihanController extends Controller
{
    protected $keringananService;

    public function __construct(KeringananTagihanService $keringananService)
    {
        $this->keringananService = $keringananService;
    }
    
    /**
     * Menampilkan halaman daftar keringanan
     */
    public function index(Request $request)
    {
        $activeTahunAjaran = TahunAjaran::getActive();
        
        if (!$activeTahunAjaran) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran aktif');
        }
        
        // Query santri dengan keringanan
        $santrisWithKeringanan = Santri::whereHas('keringananTagihans', function($q) use ($activeTahunAjaran) {
            $q->where('tahun_ajaran_id', $activeTahunAjaran->id);
        })->with(['kelasRelasi', 'asrama_anggota_terakhir.asrama'])->get();
        
        // Query semua santri aktif untuk bisa ditambahkan keringanan
        $allSantris = Santri::where('status', 'aktif')
            ->with(['kelasRelasi', 'asrama_anggota_terakhir.asrama'])
            ->orderBy('nama_santri')
            ->get();
        
        // Query jenis tagihan untuk form tambah keringanan
        $jenisTagihans = JenisTagihan::orderBy('nama')->get();
        
        return view('keuangan.keringanan_tagihan.index', compact('santrisWithKeringanan', 'allSantris', 'jenisTagihans', 'activeTahunAjaran'));
    }
    
    /**
     * Menampilkan detail keringanan santri
     */
    public function show($santriId)
    {
        $activeTahunAjaran = TahunAjaran::getActive();
        
        if (!$activeTahunAjaran) {
            return response()->json(['error' => 'Tidak ada tahun ajaran aktif'], 400);
        }
        
        $santri = Santri::with(['kelasRelasi', 'asrama_anggota_terakhir.asrama'])->findOrFail($santriId);
        
        // Ambil data keringanan
        $keringanan = $this->keringananService->getKeringananSantri($santriId, $activeTahunAjaran->id);
        
        // Ambil data tagihan dengan keringanan
        $tagihan = TagihanSantri::where('santri_id', $santriId)
            ->where('tahun_ajaran_id', $activeTahunAjaran->id)
            ->where('nominal_keringanan', '>', 0)
            ->with(['jenisTagihan'])
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'jenis_tagihan' => $item->jenisTagihan->nama,
                    'bulan' => $item->bulan_tahun,
                    'nominal_tagihan' => $item->nominal_tagihan,
                    'nominal_keringanan' => $item->nominal_keringanan,
                    'nominal_harus_dibayar' => $item->nominal_harus_dibayar,
                    'nominal_dibayar' => $item->nominal_dibayar,
                    'sisa_tagihan' => $item->sisa_tagihan,
                    'status_pembayaran' => $item->status_pembayaran
                ];
            });
        
        return response()->json([
            'santri' => [
                'id' => $santri->id,
                'nama_santri' => $santri->nama_santri,
                'nis' => $santri->nis,
                'kelas' => $santri->kelasRelasi->pluck('nama')->join(', ') ?: 'Belum ada kelas',
                'asrama' => $santri->asrama_anggota_terakhir ? $santri->asrama_anggota_terakhir->asrama->nama_asrama : 'Belum ada asrama'
            ],
            'keringanan' => $keringanan,
            'tagihan' => $tagihan
        ]);
    }
    
    /**
     * Menyimpan keringanan baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'santri_id' => 'required|exists:santris,id',
            'jenis_keringanan' => 'required|in:potongan_persen,potongan_nominal,pembebasan,bayar_satu_gratis_satu',
            'nilai_potongan' => 'required_if:jenis_keringanan,potongan_persen,potongan_nominal|numeric|nullable',
            'jenis_tagihan_id' => 'nullable|exists:jenis_tagihans,id',
            'keterangan' => 'nullable|string|max:255',
            'santri_tertanggung_id' => 'required_if:jenis_keringanan,bayar_satu_gratis_satu|nullable|exists:santris,id',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $activeTahunAjaran = TahunAjaran::getActive();
        
        if (!$activeTahunAjaran) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran aktif');
        }
        
        try {
            // Tambahkan tahun ajaran aktif ke data
            $data = $request->all();
            $data['tahun_ajaran_id'] = $activeTahunAjaran->id;
            
            // Buat keringanan baru
            $keringanan = $this->keringananService->tambahKeringanan($data);
            
            return redirect()->route('keuangan.keringanan-tagihan.index')
                ->with('success', 'Keringanan tagihan berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan keringanan tagihan: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Update status keringanan
     */
    public function update(Request $request, $id)
    {
        $keringanan = KeringananTagihan::findOrFail($id);
        
        // Update status
        $keringanan->status = $request->status === 'aktif' ? 'aktif' : 'nonaktif';
        $keringanan->save();
        
        // Jika nonaktifkan, batalkan keringanan
        if ($keringanan->status === 'nonaktif') {
            $this->keringananService->batalkanKeringanan($keringanan->id);
        } else {
            // Jika aktifkan kembali, aplikasikan lagi
            $this->keringananService->aplikasikanKeringanan($keringanan);
        }
        
        return redirect()->route('keuangan.keringanan-tagihan.index')
            ->with('success', 'Status keringanan berhasil diperbarui');
    }
    
    /**
     * Hapus keringanan
     */
    public function destroy($id)
    {
        try {
            // Batalkan dulu keringanannya
            $this->keringananService->batalkanKeringanan($id);
            
            // Hapus data keringanan
            KeringananTagihan::destroy($id);
            
            return redirect()->route('keuangan.keringanan-tagihan.index')
                ->with('success', 'Keringanan tagihan berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus keringanan tagihan: ' . $e->getMessage());
        }
    }
}
