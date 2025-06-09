<?php

namespace App\Http\Controllers;

use App\Models\Dompet;
use App\Models\Santri;
use App\Models\TransaksiDompet;
use App\Models\TransaksiKas;
use App\Models\BukuKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DompetSantriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get all santri with their dompet
        $santriList = Santri::with('dompet', 'kelas', 'asrama')
            ->orderBy('nama_santri')
            ->get()
            ->map(function ($santri) {
                return [
                    'id' => $santri->id,
                    'nama' => $santri->nama_santri,
                    'nis' => $santri->nis,
                    'nisn' => $santri->nisn,
                    'tempat_lahir' => $santri->tempat_lahir,
                    'tanggal_lahir' => $santri->tanggal_lahir ? $santri->tanggal_lahir->format('d/m/Y') : null,
                    'jenis_kelamin' => $santri->jenis_kelamin,
                    'kelas' => $santri->kelas->nama_kelas ?? '-',
                    'asrama' => $santri->asrama->nama_asrama ?? '-',
                    'nama_ortu' => ($santri->nama_ayah ?? '') . ' / ' . ($santri->nama_ibu ?? ''),
                    'nama_ayah' => $santri->nama_ayah,
                    'nama_ibu' => $santri->nama_ibu,
                    'no_hp' => $santri->hp_ayah ?: $santri->hp_ibu,
                    'hp_ayah' => $santri->hp_ayah,
                    'hp_ibu' => $santri->hp_ibu,
                    'foto' => $santri->foto ? asset('storage/' . $santri->foto) : asset('img/default-avatar.png'),
                    'dompet' => $santri->dompet ? [
                        'id' => $santri->dompet->id,
                        'nomor_dompet' => $santri->dompet->nomor_dompet,
                        'saldo' => $santri->dompet->saldo,
                        'is_active' => $santri->dompet->is_active,
                        'limit_transaksi' => $santri->dompet->limit_transaksi,
                    ] : null
                ];
            });

        // Handle AJAX request for santri data
        if ($request->ajax()) {
            return response()->json([
                'santri' => $santriList
            ]);
        }

        // Handle AJAX request for transaction history by dompet_id
        if ($request->has('dompet_id')) {
            $dompet = Dompet::with('santri')->find($request->dompet_id);
            
            if (!$dompet) {
                return response()->json(['error' => 'Dompet tidak ditemukan'], 404);
            }

            // Get transaksi history
            $transaksi = TransaksiDompet::where('dompet_id', $dompet->id)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get()
                ->map(function ($t) {
                    return [
                        'id' => $t->id,
                        'type' => $t->jenis_transaksi === 'masuk' ? 'credit' : 'debit',
                        'description' => $t->keterangan,
                        'amount' => $t->jumlah,
                        'balance' => $t->saldo_sesudah,
                        'date' => $t->created_at->format('Y-m-d'),
                        'time' => $t->created_at->format('H:i'),
                        'created_at' => $t->created_at->format('d/m/Y H:i'),
                    ];
                });

            return response()->json([
                'transaksi' => $transaksi
            ]);
        }

        // Handle AJAX request for specific santri dompet detail
        if ($request->has('santri_id')) {
            $santri = Santri::with(['dompet', 'kelas', 'asrama'])->find($request->santri_id);
            
            if (!$santri || !$santri->dompet) {
                return response()->json(['error' => 'Dompet tidak ditemukan'], 404);
            }

            // Get transaksi history
            $transaksi = TransaksiDompet::where('dompet_id', $santri->dompet->id)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get()
                ->map(function ($t) {
                    return [
                        'id' => $t->id,
                        'jenis_transaksi' => $t->jenis_transaksi,
                        'jumlah' => $t->jumlah,
                        'keterangan' => $t->keterangan,
                        'saldo_sebelum' => $t->saldo_sebelum,
                        'saldo_sesudah' => $t->saldo_sesudah,
                        'created_at' => $t->created_at->format('d/m/Y H:i'),
                        'tanggal' => $t->created_at->format('Y-m-d'),
                    ];
                });

            return response()->json([
                'santri' => [
                    'id' => $santri->id,
                    'nama' => $santri->nama_santri,
                    'nis' => $santri->nis,
                    'nisn' => $santri->nisn,
                    'tempat_lahir' => $santri->tempat_lahir,
                    'tanggal_lahir' => $santri->tanggal_lahir,
                    'jenis_kelamin' => $santri->jenis_kelamin,
                    'kelas' => $santri->kelas->nama_kelas ?? '-',
                    'asrama' => $santri->asrama->nama_asrama ?? '-',
                    'nama_ayah' => $santri->nama_ayah,
                    'nama_ibu' => $santri->nama_ibu,
                    'hp_ayah' => $santri->hp_ayah,
                    'hp_ibu' => $santri->hp_ibu,
                    'foto' => $santri->foto ? asset('storage/' . $santri->foto) : asset('img/default-avatar.png'),
                ],
                'dompet' => [
                    'id' => $santri->dompet->id,
                    'nomor_dompet' => $santri->dompet->nomor_dompet,
                    'saldo' => $santri->dompet->saldo,
                    'is_active' => $santri->dompet->is_active,
                    'limit_transaksi' => $santri->dompet->limit_transaksi,
                    'created_at' => $santri->dompet->created_at->format('d/m/Y'),
                ],
                'transaksi' => $transaksi,
                'statistik' => [
                    'total_masuk' => TransaksiDompet::where('dompet_id', $santri->dompet->id)
                        ->where('jenis_transaksi', 'masuk')
                        ->sum('jumlah'),
                    'total_keluar' => TransaksiDompet::where('dompet_id', $santri->dompet->id)
                        ->where('jenis_transaksi', 'keluar')
                        ->sum('jumlah'),
                    'transaksi_bulan_ini' => TransaksiDompet::where('dompet_id', $santri->dompet->id)
                        ->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                        ->count(),
                ]
            ]);
        }

        return view('keuangan.dompet.santri.index', compact('santriList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil santri yang belum punya dompet
        $santriTanpaDompet = Santri::whereNotIn('id', function($query) {
            $query->select('pemilik_id')
                  ->from('dompet')
                  ->where('jenis_pemilik', 'santri');
        })->get();

        return view('keuangan.dompet.santri.create', compact('santriTanpaDompet'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'santri_id' => 'required|exists:santris,id|unique:dompet,pemilik_id',
            'saldo_awal' => 'required|numeric|min:0',
            'limit_transaksi' => 'nullable|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            // Buat dompet santri
            $dompet = Dompet::create([
                'jenis_pemilik' => 'santri',
                'pemilik_id' => $request->santri_id,
                'nomor_dompet' => Dompet::generateNomorDompet('santri', $request->santri_id),
                'saldo' => $request->saldo_awal,
                'limit_transaksi' => $request->limit_transaksi,
                'is_active' => true
            ]);

            // Jika ada saldo awal, buat transaksi kas dan transaksi dompet
            if ($request->saldo_awal > 0) {
                // Cari buku kas dompet santri
                $bukuKasDompet = BukuKas::where('nama_kas', 'LIKE', '%Dompet Santri%')->first();
                
                if ($bukuKasDompet) {
                    // Buat transaksi kas (pemasukan ke buku kas dompet)
                    $transaksiKas = TransaksiKas::create([
                        'buku_kas_id' => $bukuKasDompet->id,
                        'jenis_transaksi' => 'pemasukan',
                        'kategori' => 'Top Up Dompet Santri',
                        'kode_transaksi' => TransaksiKas::generateKodeTransaksi('pemasukan'),
                        'jumlah' => $request->saldo_awal,
                        'keterangan' => 'Saldo awal dompet santri: ' . $dompet->santri->nama_santri,
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
                $transaksiDompet = TransaksiDompet::create([
                    'kode_transaksi' => TransaksiDompet::generateKodeTransaksi('top_up'),
                    'dompet_id' => $dompet->id,
                    'jenis_transaksi' => 'top_up',
                    'kategori' => 'Saldo Awal',
                    'jumlah' => $request->saldo_awal,
                    'saldo_sebelum' => 0,
                    'saldo_sesudah' => $request->saldo_awal,
                    'keterangan' => 'Saldo awal dompet santri',
                    'transaksi_kas_id' => $transaksiKas->id ?? null,
                    'created_by' => Auth::id(),
                    'status' => 'approved',
                    'tanggal_transaksi' => now()
                ]);
            }

            DB::commit();

            return redirect()->route('keuangan.dompet.santri.index')
                ->with('success', 'Dompet santri berhasil dibuat');

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
        $dompet = Dompet::with(['santri', 'transaksiDompet.creator'])
            ->findOrFail($id);

        $transaksi = $dompet->transaksiDompet()
            ->orderBy('tanggal_transaksi', 'desc')
            ->paginate(20);

        return view('keuangan.dompet.santri.show', compact('dompet', 'transaksi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $dompet = Dompet::with('santri')->findOrFail($id);
        return view('keuangan.dompet.santri.edit', compact('dompet'));
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

        return redirect()->route('keuangan.dompet.santri.show', $id)
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

        return redirect()->route('keuangan.dompet.santri.index')
            ->with('success', 'Dompet berhasil dihapus');
    }

    /**
     * Top-up saldo dompet
     */
    public function topup(Request $request)
    {
        $request->validate([
            'dompet_id' => 'required|exists:dompet,id',
            'jumlah' => 'required|numeric|min:1000',
            'keterangan' => 'nullable|string|max:255'
        ]);

        DB::beginTransaction();
        try {
            $dompet = Dompet::findOrFail($request->dompet_id);
            $saldoSebelum = $dompet->saldo;
            $jumlah = $request->jumlah;
            
            // Update saldo
            $dompet->saldo += $jumlah;
            $dompet->save();

            // Catat transaksi
            TransaksiDompet::create([
                'dompet_id' => $dompet->id,
                'jenis_transaksi' => 'masuk',
                'jumlah' => $jumlah,
                'saldo_sebelum' => $saldoSebelum,
                'saldo_sesudah' => $dompet->saldo,
                'keterangan' => $request->keterangan ?? 'Top-up saldo dompet',
                'tanggal_transaksi' => now(),
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Top-up berhasil',
                'saldo_baru' => $dompet->saldo
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Withdraw saldo dompet
     */
    public function withdraw(Request $request)
    {
        $request->validate([
            'dompet_id' => 'required|exists:dompet,id',
            'jumlah' => 'required|numeric|min:1000',
            'keterangan' => 'nullable|string|max:255'
        ]);

        DB::beginTransaction();
        try {
            $dompet = Dompet::findOrFail($request->dompet_id);
            $saldoSebelum = $dompet->saldo;
            $jumlah = $request->jumlah;
            
            // Check saldo cukup
            if ($saldoSebelum < $jumlah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saldo tidak mencukupi'
                ], 400);
            }

            // Update saldo
            $dompet->saldo -= $jumlah;
            $dompet->save();

            // Catat transaksi
            TransaksiDompet::create([
                'dompet_id' => $dompet->id,
                'jenis_transaksi' => 'keluar',
                'jumlah' => $jumlah,
                'saldo_sebelum' => $saldoSebelum,
                'saldo_sesudah' => $dompet->saldo,
                'keterangan' => $request->keterangan ?? 'Penarikan saldo dompet',
                'tanggal_transaksi' => now(),
                'user_id' => auth()->id(),
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
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aktivasi/deaktivasi dompet
     */
    public function aktivasi(Request $request)
    {
        $request->validate([
            'dompet_id' => 'required|exists:dompet,id',
            'is_active' => 'required|boolean'
        ]);

        $dompet = Dompet::findOrFail($request->dompet_id);
        $dompet->is_active = $request->is_active;
        $dompet->save();

        return response()->json([
            'success' => true,
            'message' => 'Status dompet berhasil diubah',
            'is_active' => $dompet->is_active
        ]);
    }
}
