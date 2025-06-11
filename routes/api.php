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

// =================================================================
// API untuk Integrasi dengan Sistem ePOS
// =================================================================

// Auth API untuk ePOS
Route::prefix('epos/auth')->group(function () {
    Route::post('/authenticate', [App\Http\Controllers\API\EPOSAuthController::class, 'authenticate']);
    Route::post('/refresh', [App\Http\Controllers\API\EPOSAuthController::class, 'refreshToken'])->middleware('auth:sanctum');
    Route::post('/logout', [App\Http\Controllers\API\EPOSAuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/check', [App\Http\Controllers\API\EPOSAuthController::class, 'checkToken'])->middleware('auth:sanctum');
    Route::get('/config', [App\Http\Controllers\API\EPOSAuthController::class, 'getConfig'])->middleware(\App\Http\Middleware\EPOSApiMiddleware::class);
});

// API Santri untuk ePOS (dengan autentikasi khusus)
Route::prefix('epos/santri')->middleware(\App\Http\Middleware\EPOSApiMiddleware::class)->group(function () {
    Route::get('/rfid/{tag}', [App\Http\Controllers\API\SantriEPOSController::class, 'getByRfid']);
    Route::get('/{santriId}/saldo', [App\Http\Controllers\API\SantriEPOSController::class, 'getSaldo']);
    Route::post('/{santriId}/deduct-saldo', [App\Http\Controllers\API\SantriEPOSController::class, 'deductSaldo']);
    Route::post('/{santriId}/topup-saldo', [App\Http\Controllers\API\SantriEPOSController::class, 'topUpSaldo']);
    Route::get('/{santriId}/transactions', [App\Http\Controllers\API\SantriEPOSController::class, 'getTransactionHistory']);
});

// API Transaksi untuk ePOS
Route::prefix('epos/transactions')->middleware(\App\Http\Middleware\EPOSApiMiddleware::class)->group(function () {
    Route::post('/sync', [App\Http\Controllers\API\TransaksiEPOSController::class, 'syncFromEPOS']);
    Route::get('/santri/{santriId}', [App\Http\Controllers\API\TransaksiEPOSController::class, 'getTransactionHistory']);
    Route::post('/cancel', [App\Http\Controllers\API\TransaksiEPOSController::class, 'cancelTransaction']);
    Route::get('/statistics', [App\Http\Controllers\API\TransaksiEPOSController::class, 'getStatistics']);
});