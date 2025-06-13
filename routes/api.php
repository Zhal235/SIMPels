<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Santri;
use App\Models\KelasAnggota;
use App\Http\Controllers\AsramaController;
use App\Http\Controllers\BukuKasController;
use App\Http\Controllers\API\WaliSantriController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\TagihanController;
use App\Http\Controllers\API\TransaksiController;
use App\Http\Controllers\API\AkademikController;
use App\Http\Controllers\API\AsramaController as APIAsramaController;
use App\Http\Controllers\API\PerizinanController;
use App\Http\Controllers\API\WaliSantriDompetController;
use App\Http\Controllers\API\DompetController;
use App\Http\Controllers\API\DompetSantriController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Handle OPTIONS requests for CORS preflight
Route::options('/{any}', function() {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN');
})->where('any', '.*');

// API untuk PWA Wali Santri
Route::prefix('wali-santri')->group(function() {
    // Public routes
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    
    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // User profile
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        
        // PWA API Endpoints - sesuai BACKEND_API_REQUIREMENTS.md
        Route::get('/santri', [WaliSantriController::class, 'getSantri']);
        Route::get('/tagihan', [WaliSantriController::class, 'getTagihan']);
        Route::get('/perizinan', [WaliSantriController::class, 'getPerizinan']);
        Route::post('/perizinan', [WaliSantriController::class, 'createPerizinan']);
        Route::put('/perizinan/{id}', [WaliSantriController::class, 'updatePerizinan']);
        Route::delete('/perizinan/{id}', [WaliSantriController::class, 'deletePerizinan']);
        
        // Edit Data Santri
        Route::put('/santri/{id}', [WaliSantriController::class, 'updateSantri']);
        
        // Dompet Management
        Route::get('/dompet', [WaliSantriController::class, 'getDompetInfo']);
        Route::post('/dompet/topup', [WaliSantriController::class, 'topUpSaldo']);
        Route::put('/dompet/limit/{santriId}', [WaliSantriController::class, 'updateLimitHarian']);
        
        // Pembayaran Tagihan
        Route::post('/tagihan/bayar', [WaliSantriController::class, 'bayarTagihan']);
        
        // Santri profile
        Route::prefix('santri')->group(function () {
            Route::get('/', [ProfileController::class, 'getSantriList']);
            Route::get('/{id}', [ProfileController::class, 'getSantriDetail']);
        });
        
        // Tagihan & Pembayaran
        Route::prefix('tagihan')->group(function () {
            Route::get('/', [TagihanController::class, 'getTagihanList']);
            Route::get('/summary', [TagihanController::class, 'getTagihanSummary']);
            Route::get('/tunggakan', [TagihanController::class, 'getTunggakanList']);
            Route::get('/tunggakan/{id}', [TagihanController::class, 'getTunggakanDetail']);
            Route::get('/{id}', [TagihanController::class, 'getTagihanDetail']);
        });
        
        // Dompet Santri
        Route::prefix('dompet')->group(function () {
            Route::get('/', [DompetSantriController::class, 'getDompetInfo']);
            Route::get('/transaksi', [DompetSantriController::class, 'getDompetTransaksi']);
            Route::get('/transaksi/{santriId}', [DompetSantriController::class, 'getDompetTransaksi']);
            Route::get('/summary', [DompetSantriController::class, 'getDompetSummary']);
            Route::get('/summary/{santriId}', [DompetSantriController::class, 'getDompetSummary']);
            Route::put('/limit/{id}', [WaliSantriDompetController::class, 'updateLimitHarian']);
        });
        
        // Transaksi
        Route::prefix('transaksi')->group(function () {
            Route::get('/', [TransaksiController::class, 'getTransaksiList']);
            Route::get('/{id}', [TransaksiController::class, 'getTransaksiDetail']);
        });
        
        // Keringanan
        Route::prefix('keringanan')->group(function () {
            Route::get('/', [TagihanController::class, 'getKeringananList']);
            Route::get('/{id}', [TagihanController::class, 'getKeringananDetail']);
        });
        
        // Akademik
        Route::prefix('akademik')->group(function () {
            Route::get('/info', [AkademikController::class, 'getAkademikInfo']);
        });
        
        // Asrama
        Route::prefix('asrama')->group(function () {
            Route::get('/info', [APIAsramaController::class, 'getAsramaInfo']); 
        });
        
        // Perizinan
        Route::prefix('perizinan')->group(function () {
            Route::get('/', [PerizinanController::class, 'getPerizinanList']);
            Route::post('/', [PerizinanController::class, 'createPerizinan']);
            Route::get('/{id}', [PerizinanController::class, 'getPerizinanDetail']);
            Route::put('/{id}', [PerizinanController::class, 'updatePerizinan']);
            Route::delete('/{id}', [PerizinanController::class, 'deletePerizinan']);
        });
        
        // Dompet Santri
        Route::prefix('dompet')->group(function () {
            Route::get('/', [DompetController::class, 'getDompetInfo']);
            Route::get('/{id}', [DompetController::class, 'getDompetDetail']);
            Route::get('/{id}/transaksi', [DompetController::class, 'getTransaksiDompet']);
        });
    });
});

// Authentication routes for Wali Santri
Route::prefix('wali-santri')->group(function () {
    Route::post('/login', [App\Http\Controllers\API\AuthWaliSantriController::class, 'login']);
    Route::post('/register', [App\Http\Controllers\API\AuthWaliSantriController::class, 'register']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [App\Http\Controllers\API\AuthWaliSantriController::class, 'user']);
        Route::post('/logout', [App\Http\Controllers\API\AuthWaliSantriController::class, 'logout']);
        Route::put('/profile', [App\Http\Controllers\API\AuthWaliSantriController::class, 'updateProfile']);
        Route::put('/change-password', [App\Http\Controllers\API\AuthWaliSantriController::class, 'changePassword']);
        
        // Santri management
        Route::get('/santri', [App\Http\Controllers\API\WaliSantriController::class, 'getSantri']);
        Route::put('/santri/{id}', [App\Http\Controllers\API\WaliSantriController::class, 'updateSantri']);
        
        // Dompet management
        Route::get('/dompet', [App\Http\Controllers\API\WaliSantriController::class, 'getDompetInfo']);
        Route::post('/dompet/topup', [App\Http\Controllers\API\WaliSantriController::class, 'topUpSaldo']);
        Route::put('/dompet/limit/{santriId}', [App\Http\Controllers\API\WaliSantriController::class, 'updateLimitHarian']);
        
        // Tagihan management
        Route::get('/tagihan', [App\Http\Controllers\API\WaliSantriController::class, 'getTagihan']);
        Route::post('/tagihan/bayar', [App\Http\Controllers\API\WaliSantriController::class, 'bayarTagihan']);
        
        // Perizinan management
        Route::get('/perizinan', [App\Http\Controllers\API\WaliSantriController::class, 'getPerizinan']);
        Route::post('/perizinan', [App\Http\Controllers\API\WaliSantriController::class, 'createPerizinan']);
        Route::put('/perizinan/{id}', [App\Http\Controllers\API\WaliSantriController::class, 'updatePerizinan']);
        Route::delete('/perizinan/{id}', [App\Http\Controllers\API\WaliSantriController::class, 'deletePerizinan']);
    });
});

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