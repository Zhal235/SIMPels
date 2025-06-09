<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Santri;
use App\Models\KelasAnggota;
use App\Http\Controllers\AsramaController;
use App\Http\Controllers\BukuKasController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API untuk mengambil data santri dengan asrama untuk modal pindah
Route::get('/santris-with-asrama', [AsramaController::class, 'getSantrisWithAsrama']);

// API untuk mengambil santri berdasarkan kelas
Route::get('/santri-by-kelas', function (Request $request) {
    $kelasIds = explode(',', $request->get('kelas_ids', ''));
    $kelasIds = array_filter($kelasIds); // Remove empty values
    
    if (empty($kelasIds)) {
        return response()->json([
            'success' => false,
            'message' => 'Kelas IDs tidak ditemukan',
            'santris' => []
        ]);
    }
    
    try {
        // Ambil santri berdasarkan kelas melalui tabel pivot kelas_anggota
        $santris = Santri::whereHas('kelas_anggota', function($query) use ($kelasIds) {
            $query->whereIn('kelas_id', $kelasIds);
        })
        ->with(['kelas_anggota.kelas'])
        ->where('status', 'aktif') // Hanya santri aktif
        ->get()
        ->map(function($santri) {
            // Ambil kelas terakhir santri
            $kelasAnggota = $santri->kelas_anggota->last();
            return [
                'id' => $santri->id,
                'nama_santri' => $santri->nama_santri,
                'nis' => $santri->nis,
                'kelas_nama' => $kelasAnggota ? $kelasAnggota->kelas->nama : 'Tidak ada kelas'
            ];
        });
        
        return response()->json([
            'success' => true,
            'santris' => $santris
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error mengambil data santri: ' . $e->getMessage(),
            'santris' => []
        ]);
    }
});

// API untuk Buku Kas
Route::get('buku-kas/{id}', function($id) {
    try {
        $bukuKas = \App\Models\BukuKas::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $bukuKas
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Buku kas tidak ditemukan'
        ], 404);
    }
})->name('api.buku-kas.show');

// API untuk Manajemen Kategori Keuangan
Route::prefix('keuangan/categories')->group(function () {
    Route::get('/', [App\Http\Controllers\API\KategoriKeuanganController::class, 'index']);
    Route::post('/', [App\Http\Controllers\API\KategoriKeuanganController::class, 'store']);
    Route::get('/{id}', [App\Http\Controllers\API\KategoriKeuanganController::class, 'show']);
    Route::put('/{id}', [App\Http\Controllers\API\KategoriKeuanganController::class, 'update']);
    Route::delete('/{id}', [App\Http\Controllers\API\KategoriKeuanganController::class, 'destroy']);
});

// API untuk Transaksi Kas
Route::prefix('keuangan/transaksi-kas')->group(function () {
    Route::get('/{id}', [App\Http\Controllers\TransaksiKasController::class, 'apiShow']);
});