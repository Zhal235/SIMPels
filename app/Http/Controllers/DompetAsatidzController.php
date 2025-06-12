<?php

namespace App\Http\Controllers;

use App\Models\Dompet;
use App\Models\Pegawai;
use App\Models\TransaksiDompet;
use App\Models\TransaksiKas;
use App\Models\BukuKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DompetAsatidzController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dompetAsatidz = Dompet::with(['asatidz'])
            ->jenisPemilik('asatidz')
            ->aktif()
            ->paginate(20);

        $totalSaldo = Dompet::jenisPemilik('asatidz')->aktif()->sum('saldo');
        $totalDompet = Dompet::jenisPemilik('asatidz')->aktif()->count();

        return view('keuangan.dompet.asatidz.index', compact('dompetAsatidz', 'totalSaldo', 'totalDompet'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil asatidz yang belum punya dompet
        $asatidzTanpaDompet = Pegawai::whereNotIn('id', function($query) {
            $query->select('pemilik_id')
                  ->from('dompet')
                  ->where('jenis_pemilik', 'asatidz');
        })->where('status_pegawai', 'Aktif')->get();

        return view('keuangan.dompet.asatidz.create', compact('asatidzTanpaDompet'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'asatidz_id' => 'required|exists:pegawais,id|unique:dompet,pemilik_id',
            'saldo_awal' => 'required|numeric|min:0',
            'limit_transaksi' => 'nullable|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            // Buat dompet asatidz
            $dompet = Dompet::create([
                'jenis_pemilik' => 'asatidz',
                'pemilik_id' => $request->asatidz_id,
                'nomor_dompet' => Dompet::generateNomorDompet('asatidz', $request->asatidz_id),
                'saldo' => $request->saldo_awal,
                'limit_transaksi' => $request->limit_transaksi,
                'is_active' => true
            ]);

            // Jika ada saldo awal, buat transaksi kas dan transaksi dompet
            if ($request->saldo_awal > 0) {
                // Cari buku kas dompet pegawai
                $bukuKasDompet = BukuKas::where('nama_kas', 'LIKE', '%Dompet Pegawai%')
                    ->orWhere('nama_kas', 'LIKE', '%Dompet Asatidz%')
                    ->first();
                
                if ($bukuKasDompet) {
                    // Buat transaksi kas (pemasukan ke buku kas dompet)
                    $transaksiKas = TransaksiKas::create([
                        'buku_kas_id' => $bukuKasDompet->id,
                        'jenis_transaksi' => 'pemasukan',
                        'kategori' => 'Top Up Dompet Pegawai',
                        'kode_transaksi' => TransaksiKas::generateKodeTransaksi('pemasukan'),
                        'jumlah' => $request->saldo_awal,
                        'keterangan' => 'Saldo awal dompet pegawai: ' . $dompet->asatidz->nama_pegawai,
                        'metode_pembayaran' => 'Tunai',
                        'tanggal_transaksi' => now(),
                        'created_by' => Auth::id(),
                        'status' => 'approved'
                    ]);

                    // Update saldo buku kas
                    $bukuKasDompet->saldo_saat_ini += $request->saldo_awal;
                    $bukuKasDompet->save();
                }

                // Buat transaksi dompet
                TransaksiDompet::create([
                    'kode_transaksi' => TransaksiDompet::generateKodeTransaksi('top_up'),
                    'dompet_id' => $dompet->id,
                    'jenis_transaksi' => 'top_up',
                    'kategori' => 'Saldo Awal',
                    'jumlah' => $request->saldo_awal,
                    'saldo_sebelum' => 0,
                    'saldo_sesudah' => $request->saldo_awal,
                    'keterangan' => 'Saldo awal dompet pegawai',
                    'transaksi_kas_id' => $transaksiKas->id ?? null,
                    'created_by' => Auth::id(),
                    'status' => 'approved',
                    'tanggal_transaksi' => now()
                ]);
            }

            DB::commit();

            return redirect()->route('keuangan.dompet.asatidz.index')
                ->with('success', 'Dompet pegawai berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal membuat dompet: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $dompet = Dompet::with(['asatidz', 'transaksiDompet.creator'])
            ->findOrFail($id);

        $transaksi = $dompet->transaksiDompet()
            ->orderBy('tanggal_transaksi', 'desc')
            ->paginate(20);

        return view('keuangan.dompet.asatidz.show', compact('dompet', 'transaksi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $dompet = Dompet::with('asatidz')->findOrFail($id);
        return view('keuangan.dompet.asatidz.edit', compact('dompet'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $dompet = Dompet::findOrFail($id);

        $request->validate([
            'limit_transaksi' => 'nullable|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $dompet->update([
            'limit_transaksi' => $request->limit_transaksi,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('keuangan.dompet.asatidz.show', $id)
            ->with('success', 'Dompet berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $dompet = Dompet::findOrFail($id);

        if ($dompet->saldo > 0) {
            return back()->with('error', 'Tidak dapat menghapus dompet yang masih memiliki saldo');
        }

        if ($dompet->transaksiDompet()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus dompet yang sudah memiliki riwayat transaksi');
        }

        $dompet->delete();

        return redirect()->route('keuangan.dompet.asatidz.index')
            ->with('success', 'Dompet berhasil dihapus');
    }

    /**
     * Show top up form
     */
    public function topUpForm($id)
    {
        $dompet = Dompet::with('asatidz')->findOrFail($id);
        $bukuKasList = BukuKas::where('is_active', true)->get();
        
        return view('keuangan.dompet.asatidz.top-up', compact('dompet', 'bukuKasList'));
    }

    /**
     * Process top up
     */
    public function topUp(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:1000',
            'buku_kas_id' => 'required|exists:buku_kas,id',
            'metode_pembayaran' => 'required|string',
            'keterangan' => 'nullable|string'
        ]);

        $dompet = Dompet::findOrFail($id);

        DB::beginTransaction();
        try {
            // Update saldo dompet
            $saldoUpdate = $dompet->updateSaldo($request->jumlah, 'tambah');

            // Buat transaksi kas
            $bukuKas = BukuKas::findOrFail($request->buku_kas_id);
            $transaksiKas = TransaksiKas::create([
                'buku_kas_id' => $request->buku_kas_id,
                'jenis_transaksi' => 'pemasukan',
                'kategori' => 'Top Up Dompet Pegawai',
                'kode_transaksi' => TransaksiKas::generateKodeTransaksi('pemasukan'),
                'jumlah' => $request->jumlah,
                'keterangan' => $request->keterangan ?: 'Top up dompet pegawai: ' . $dompet->nama_pemilik,
                'metode_pembayaran' => $request->metode_pembayaran,
                'tanggal_transaksi' => now(),
                'created_by' => Auth::id(),
                'status' => 'approved'
            ]);

            // Update saldo buku kas
            $bukuKas->saldo_saat_ini += $request->jumlah;
            $bukuKas->save();

            // Buat transaksi dompet
            TransaksiDompet::create([
                'kode_transaksi' => TransaksiDompet::generateKodeTransaksi('top_up'),
                'dompet_id' => $dompet->id,
                'jenis_transaksi' => 'top_up',
                'kategori' => 'Top Up',
                'jumlah' => $request->jumlah,
                'saldo_sebelum' => $saldoUpdate['saldo_sebelum'],
                'saldo_sesudah' => $saldoUpdate['saldo_sesudah'],
                'keterangan' => $request->keterangan,
                'transaksi_kas_id' => $transaksiKas->id,
                'created_by' => Auth::id(),
                'status' => 'approved',
                'tanggal_transaksi' => now()
            ]);

            DB::commit();

            return redirect()->route('keuangan.dompet.asatidz.show', $id)
                ->with('success', 'Top up berhasil dilakukan');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal melakukan top up: ' . $e->getMessage());
        }
    }

    /**
     * Get asatidz list with dompet data for main view
     */
    public function getAsatidzWithDompet(Request $request)
    {
        // Get all asatidz with their dompet
        $asatidzList = Pegawai::with(['dompet'])
            ->where('status_pegawai', 'Aktif')
            ->orderBy('nama_pegawai')
            ->get()
            ->map(function ($asatidz) {
                return [
                    'id' => $asatidz->id,
                    'nama' => $asatidz->nama_pegawai,
                    'email' => $asatidz->email,
                    'foto' => $asatidz->foto ? asset('storage/' . $asatidz->foto) : asset('img/default-avatar.png'),
                    'dompet' => $asatidz->dompet ? [
                        'id' => $asatidz->dompet->id,
                        'nomor_dompet' => $asatidz->dompet->nomor_dompet,
                        'saldo' => $asatidz->dompet->saldo,
                        'is_active' => $asatidz->dompet->is_active,
                        'limit_transaksi' => null, // Asatidz tidak ada limit
                    ] : null
                ];
            });

        // Handle AJAX request for transaction history by dompet_id
        if ($request->has('dompet_id')) {
            $dompetId = $request->dompet_id;
            $dompet = Dompet::with('asatidz')->find($dompetId);
            
            if (!$dompet) {
                return response()->json(['transaksi' => []], 404);
            }

            $transaksi = TransaksiDompet::where('dompet_id', $dompetId)
                ->orderBy('tanggal_transaksi', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'kode_transaksi' => $item->kode_transaksi,
                        'jenis_transaksi' => $item->jenis_transaksi,
                        'kategori' => $item->kategori,
                        'jumlah' => $item->jumlah,
                        'saldo_sebelum' => $item->saldo_sebelum,
                        'saldo_sesudah' => $item->saldo_sesudah,
                        'keterangan' => $item->keterangan,
                        'tanggal_transaksi' => $item->tanggal_transaksi,
                        'created_at' => $item->created_at,
                        'status' => $item->status,
                    ];
                });

            return response()->json(['transaksi' => $transaksi]);
        }

        return view('keuangan.dompet.asatidz.main', compact('asatidzList'));
    }

    /**
     * Process topup via AJAX
     */
    public function topupAjax(Request $request)
    {
        $request->validate([
            'dompet_id' => 'required|exists:dompet,id',
            'jumlah' => 'required|numeric|min:1000',
            'keterangan' => 'nullable|string|max:255',
            'kode_transaksi' => 'nullable|string|max:50'
        ]);

        $dompet = Dompet::findOrFail($request->dompet_id);

        DB::beginTransaction();
        try {
            // Update saldo dompet
            $saldoUpdate = $dompet->updateSaldo($request->jumlah, 'tambah');

            // Cari buku kas dompet pegawai
            $bukuKasDompet = BukuKas::where('nama_kas', 'LIKE', '%Dompet Pegawai%')
                ->orWhere('nama_kas', 'LIKE', '%Dompet Asatidz%')
                ->first();
            $transaksiKasId = null;
            
            if ($bukuKasDompet) {
                // Get nama pegawai
                $namaPegawai = 'Pegawai Tidak Diketahui';
                if ($dompet->asatidz) {
                    $namaPegawai = $dompet->asatidz->nama_pegawai;
                } else {
                    // Fallback: ambil langsung dari database
                    $pegawai = \App\Models\Pegawai::find($dompet->pemilik_id);
                    if ($pegawai) {
                        $namaPegawai = $pegawai->nama_pegawai;
                    }
                }
                
                // Buat transaksi kas (pemasukan ke buku kas dompet)
                $transaksiKas = TransaksiKas::create([
                    'buku_kas_id' => $bukuKasDompet->id,
                    'jenis_transaksi' => 'pemasukan',
                    'kategori' => 'Top Up Dompet Pegawai',
                    'kode_transaksi' => TransaksiKas::generateKodeTransaksi('pemasukan'),
                    'jumlah' => $request->jumlah,
                    'keterangan' => 'Top up dompet pegawai' . ($request->keterangan ? ' - ' . $request->keterangan : ''),
                    'nama_pemohon' => $namaPegawai,
                    'metode_pembayaran' => 'Tunai',
                    'tanggal_transaksi' => now(),
                    'created_by' => Auth::id(),
                    'status' => 'approved'
                ]);

                // Update saldo buku kas
                $bukuKasDompet->saldo_saat_ini += $request->jumlah;
                $bukuKasDompet->save();
                
                $transaksiKasId = $transaksiKas->id;
            }

            // Buat transaksi dompet
            TransaksiDompet::create([
                'kode_transaksi' => $request->kode_transaksi ?: TransaksiDompet::generateKodeTransaksi('top_up'),
                'dompet_id' => $dompet->id,
                'jenis_transaksi' => 'top_up',
                'kategori' => 'Top Up',
                'jumlah' => $request->jumlah,
                'saldo_sebelum' => $saldoUpdate['saldo_sebelum'],
                'saldo_sesudah' => $saldoUpdate['saldo_sesudah'],
                'keterangan' => $request->keterangan ?: 'Top up saldo dompet pegawai',
                'transaksi_kas_id' => $transaksiKasId,
                'created_by' => Auth::id(),
                'status' => 'approved',
                'tanggal_transaksi' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Top up berhasil',
                'saldo_baru' => $dompet->saldo
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
     * Process withdraw via AJAX
     */
    public function withdrawAjax(Request $request)
    {
        $request->validate([
            'dompet_id' => 'required|exists:dompet,id',
            'jumlah' => 'required|numeric|min:1000',
            'keterangan' => 'nullable|string|max:255',
            'kode_transaksi' => 'nullable|string|max:50'
        ]);

        $dompet = Dompet::findOrFail($request->dompet_id);

        // Check if balance is sufficient
        if ($dompet->saldo < $request->jumlah) {
            return response()->json([
                'success' => false,
                'message' => 'Saldo tidak mencukupi'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Update saldo dompet
            $saldoUpdate = $dompet->updateSaldo($request->jumlah, 'kurang');

            // Cari buku kas dompet pegawai
            $bukuKasDompet = BukuKas::where('nama_kas', 'LIKE', '%Dompet Pegawai%')
                ->orWhere('nama_kas', 'LIKE', '%Dompet Asatidz%')
                ->first();
            $transaksiKasId = null;
            
            if ($bukuKasDompet) {
                // Get nama pegawai
                $namaPegawai = 'Pegawai Tidak Diketahui';
                if ($dompet->asatidz) {
                    $namaPegawai = $dompet->asatidz->nama_pegawai;
                } else {
                    // Fallback: ambil langsung dari database
                    $pegawai = \App\Models\Pegawai::find($dompet->pemilik_id);
                    if ($pegawai) {
                        $namaPegawai = $pegawai->nama_pegawai;
                    }
                }
                
                // Buat transaksi kas (pengeluaran dari buku kas dompet)
                $transaksiKas = TransaksiKas::create([
                    'buku_kas_id' => $bukuKasDompet->id,
                    'jenis_transaksi' => 'pengeluaran',
                    'kategori' => 'Penarikan Dompet Pegawai',
                    'kode_transaksi' => TransaksiKas::generateKodeTransaksi('pengeluaran'),
                    'jumlah' => $request->jumlah,
                    'keterangan' => 'Penarikan dompet pegawai' . ($request->keterangan ? ' - ' . $request->keterangan : ''),
                    'nama_pemohon' => $namaPegawai,
                    'metode_pembayaran' => 'Tunai',
                    'tanggal_transaksi' => now(),
                    'created_by' => Auth::id(),
                    'status' => 'approved'
                ]);

                // Update saldo buku kas
                $bukuKasDompet->saldo_saat_ini -= $request->jumlah;
                $bukuKasDompet->save();
                
                $transaksiKasId = $transaksiKas->id;
            }

            // Buat transaksi dompet
            TransaksiDompet::create([
                'kode_transaksi' => $request->kode_transaksi ?: TransaksiDompet::generateKodeTransaksi('withdraw'),
                'dompet_id' => $dompet->id,
                'jenis_transaksi' => 'withdraw',
                'kategori' => 'Penarikan',
                'jumlah' => $request->jumlah,
                'saldo_sebelum' => $saldoUpdate['saldo_sebelum'],
                'saldo_sesudah' => $saldoUpdate['saldo_sesudah'],
                'keterangan' => $request->keterangan ?: 'Penarikan saldo dompet pegawai',
                'transaksi_kas_id' => $transaksiKasId,
                'created_by' => Auth::id(),
                'status' => 'approved',
                'tanggal_transaksi' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Penarikan berhasil',
                'saldo_baru' => $dompet->saldo
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan penarikan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle wallet activation status
     */
    public function toggleActivation(Request $request)
    {
        $request->validate([
            'dompet_id' => 'required|exists:dompet,id',
            'is_active' => 'required|boolean'
        ]);

        $dompet = Dompet::findOrFail($request->dompet_id);

        try {
            $dompet->update(['is_active' => $request->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'Status dompet berhasil diubah',
                'is_active' => $dompet->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status dompet: ' . $e->getMessage()
            ], 500);
        }
    }
}
