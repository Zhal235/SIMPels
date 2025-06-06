<?php

namespace App\Services;

use App\Models\BukuKas;
use App\Models\JenisBukuKas;
use App\Models\TransaksiKas;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class KeuanganService
{
    /**
     * Get statistical data for Buku Kas grouped by Jenis Kas
     */
    public function getBukuKasStatistics(): Collection
    {
        return BukuKas::with('jenisBukuKas')
            ->select('jenis_kas_id')
            ->selectRaw('SUM(saldo_saat_ini) as total_saldo, COUNT(*) as jumlah_kas')
            ->where('is_active', true)
            ->groupBy('jenis_kas_id')
            ->get()
            ->map(function ($item) {
                $jenisBukuKas = JenisBukuKas::find($item->jenis_kas_id);
                $item->jenis_kas = $jenisBukuKas ? $jenisBukuKas->nama : 'Lainnya';
                return $item;
            });
    }

    /**
     * Get usage count for each Jenis Kas
     */
    public function getJenisKasUsageCount(): array
    {
        $jenisKasList = JenisBukuKas::all();
        $usageCount = [];

        foreach ($jenisKasList as $jenisKas) {
            $usageCount[$jenisKas->id] = BukuKas::where('jenis_kas_id', $jenisKas->id)->count();
        }

        return $usageCount;
    }

    /**
     * Check if Buku Kas can be deleted
     */
    public function canDeleteBukuKas(BukuKas $bukuKas): array
    {
        $jenisTagihanCount = $bukuKas->jenisTagihan()->count();
        $transaksiKasCount = $bukuKas->transaksiKas()->count();

        $canDelete = $jenisTagihanCount === 0 && $transaksiKasCount === 0;
        
        $messages = [];
        if ($jenisTagihanCount > 0) {
            $messages[] = "Terdapat {$jenisTagihanCount} jenis tagihan yang terkait";
        }
        if ($transaksiKasCount > 0) {
            $messages[] = "Terdapat {$transaksiKasCount} transaksi kas yang terkait";
        }

        return [
            'can_delete' => $canDelete,
            'messages' => $messages,
            'related_count' => $jenisTagihanCount + $transaksiKasCount
        ];
    }

    /**
     * Check if Jenis Kas can be deleted
     */
    public function canDeleteJenisKas(JenisBukuKas $jenisKas): array
    {
        $bukuKasCount = BukuKas::where('jenis_kas_id', $jenisKas->id)->count();
        
        return [
            'can_delete' => $bukuKasCount === 0,
            'messages' => $bukuKasCount > 0 ? ["Masih digunakan oleh {$bukuKasCount} buku kas"] : [],
            'related_count' => $bukuKasCount
        ];
    }

    /**
     * Get formatted data for dropdown/select options
     */
    public function getBukuKasForDropdown(): Collection
    {
        return BukuKas::with('jenisBukuKas')
            ->active()
            ->orderBy('nama_kas')
            ->get()
            ->map(function($kas) {
                return [
                    'id' => $kas->id,
                    'nama_kas' => $kas->nama_kas,
                    'kode_kas' => $kas->kode_kas,
                    'jenis_kas' => $kas->jenisBukuKas ? $kas->jenisBukuKas->nama : null,
                    'formatted_name' => "{$kas->kode_kas} - {$kas->nama_kas}"
                ];
            });
    }

    /**
     * Get formatted data for Jenis Kas dropdown
     */
    public function getJenisKasForDropdown(): Collection
    {
        return JenisBukuKas::active()
            ->orderBy('nama')
            ->get(['id', 'nama', 'kode'])
            ->map(function($jenis) {
                return [
                    'id' => $jenis->id,
                    'nama' => $jenis->nama,
                    'kode' => $jenis->kode,
                    'formatted_name' => "{$jenis->kode} - {$jenis->nama}"
                ];
            });
    }

    /**
     * Create a new transaction in Buku Kas
     */
    public function createTransaksiKas(BukuKas $bukuKas, array $data): TransaksiKas
    {
        $transaksi = TransaksiKas::create($data + [
            'buku_kas_id' => $bukuKas->id
        ]);

        // Update saldo buku kas
        $bukuKas->updateSaldo($data['jumlah'], $data['tipe']);

        return $transaksi;
    }

    /**
     * Get summary of financial data
     */
    public function getFinancialSummary(): array
    {
        $totalBukuKas = BukuKas::active()->count();
        $totalJenisKas = JenisBukuKas::active()->count();
        $totalSaldo = BukuKas::active()->sum('saldo_saat_ini');
        $totalTransaksi = TransaksiKas::whereHas('bukuKas', function($query) {
            $query->where('is_active', true);
        })->count();

        return [
            'total_buku_kas' => $totalBukuKas,
            'total_jenis_kas' => $totalJenisKas,
            'total_saldo' => $totalSaldo,
            'formatted_total_saldo' => 'Rp ' . number_format($totalSaldo, 0, ',', '.'),
            'total_transaksi' => $totalTransaksi
        ];
    }

    /**
     * Validate business rules for Buku Kas
     */
    public function validateBukuKasRules(array $data, ?BukuKas $bukuKas = null): array
    {
        $errors = [];

        // Check if jenis kas exists and is active
        $jenisKas = JenisBukuKas::find($data['jenis_kas_id'] ?? null);
        if (!$jenisKas || !$jenisKas->is_active) {
            $errors[] = 'Jenis kas tidak valid atau tidak aktif';
        }

        // Check duplicate kode_kas
        $existingKodeKas = BukuKas::where('kode_kas', $data['kode_kas'] ?? '')
            ->when($bukuKas, function($query) use ($bukuKas) {
                $query->where('id', '!=', $bukuKas->id);
            })
            ->exists();

        if ($existingKodeKas) {
            $errors[] = 'Kode kas sudah digunakan';
        }

        // Validate saldo awal
        if (isset($data['saldo_awal']) && $data['saldo_awal'] < 0) {
            $errors[] = 'Saldo awal tidak boleh negatif';
        }

        return $errors;
    }

    /**
     * Validate business rules for Jenis Kas
     */
    public function validateJenisKasRules(array $data, ?JenisBukuKas $jenisKas = null): array
    {
        $errors = [];

        // Check duplicate kode
        $existingKode = JenisBukuKas::where('kode', $data['kode'] ?? '')
            ->when($jenisKas, function($query) use ($jenisKas) {
                $query->where('id', '!=', $jenisKas->id);
            })
            ->exists();

        if ($existingKode) {
            $errors[] = 'Kode jenis kas sudah digunakan';
        }

        return $errors;
    }
}
