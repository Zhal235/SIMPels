<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\KelasAnggotaController;
use App\Http\Controllers\AsramaController;
use App\Http\Controllers\AsramaAnggotaController;
use App\Http\Controllers\MutasiSantriController;
use App\Http\Controllers\RfidTagController;
use App\Http\Controllers\PembayaranSantriController;
use App\Http\Controllers\JenisTagihanController;
use App\Http\Controllers\TahunAjaranController;
use App\Http\Controllers\BukuKasController;
use Illuminate\Http\Request;


// Redirect root ke data santri
Route::get('/', fn() => redirect()->route('santris.index'));

// Test API endpoint for Buku Kas (no auth required)
Route::get('test/buku-kas', function() {
    try {
        $bukuKas = \App\Models\BukuKas::all();
        return response()->json([
            'success' => true,
            'data' => $bukuKas,
            'count' => $bukuKas->count()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error fetching buku kas'
        ], 500);
    }
})->name('test.buku-kas.list');

Route::get('test/buku-kas/{id}', function($id) {
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
})->name('test.buku-kas');

// Add test endpoint to update buku kas without auth requirements
Route::post('test/buku-kas/{id}', function($id, Request $request) {
    try {
        $bukuKas = \App\Models\BukuKas::findOrFail($id);
        
        // Validate request
        $validated = $request->validate([
            'nama_kas' => 'required|string|max:255|unique:buku_kas,nama_kas,' . $bukuKas->id,
            'kode_kas' => 'required|string|max:50|unique:buku_kas,kode_kas,' . $bukuKas->id,
            'deskripsi' => 'nullable|string',
            'jenis_kas_id' => 'required|exists:jenis_buku_kas,id',
            'saldo_awal' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $bukuKas->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Test update: Buku kas berhasil diperbarui!',
            'data' => $bukuKas
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
})->name('test.buku-kas.update');

// Add test endpoint to delete buku kas without auth requirements
Route::delete('test/buku-kas/{id}', function($id) {
    try {
        $bukuKas = \App\Models\BukuKas::findOrFail($id);
        
        // Check if any jenis tagihan is linked
        if ($bukuKas->jenisTagihan()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus buku kas karena masih terdapat jenis tagihan yang terkait!'
            ], 400);
        }
        
        $bukuKas->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Test delete: Buku kas berhasil dihapus!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
})->name('test.buku-kas.delete');

Route::middleware(['auth'])->group(function () {

    // Modul Santri
    Route::resource('santris', SantriController::class)->middleware(['role:admin']);
    Route::get('santris-export', [SantriController::class, 'export'])->name('santris.export')->middleware(['role:admin']);
    Route::get('santris-template', [SantriController::class, 'template'])->name('santris.template')->middleware(['role:admin']);
    Route::get('santris-import', [SantriController::class, 'importForm'])->name('santris.import.form')->middleware(['role:admin']);
    Route::post('santris-import', [SantriController::class, 'import'])->name('santris.import')->middleware(['role:admin']);

    // Proses mutasi (submit dari modal/popup)
    Route::post('/santris/{id}/mutasi', [MutasiSantriController::class, 'mutasiProses'])->name('santris.mutasi.proses');
    // Batalkan mutasi
    Route::post('/mutasi-santri/{id}/batal', [MutasiSantriController::class, 'batalkanMutasi'])->name('mutasi_santri.batal');
    // Riwayat mutasi (index)
    Route::get('/mutasi-santri', [MutasiSantriController::class, 'index'])->name('mutasi_santri.index');

    // Dashboard & Profile
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Fitur Pindah Kelas (letakkan di atas resource kelas!)
    Route::get('kelas/pindah', [KelasController::class, 'pindahForm'])->name('kelas.pindah.form');
    Route::post('kelas/pindah', [KelasController::class, 'pindah'])->name('kelas.pindah');

    // Modul Kelas (CRUD)
    Route::resource('kelas', KelasController::class)->parameters([
        'kelas' => 'kelas'
    ]);

    // Import Kelas (bukan nested)
    Route::post('kelas-import', [KelasController::class, 'import'])->name('kelas.import');
    Route::get('kelas-import', [KelasController::class, 'importForm'])->name('kelas.import.form');
    Route::get('kelas-template', [KelasController::class, 'template'])->name('kelas.template');

    // Anggota Kelas (nested)
    Route::prefix('kelas/{kelas}')->name('kelas.')->group(function () {
        Route::get('anggota', [KelasAnggotaController::class, 'index'])->name('anggota.index');
        Route::post('anggota', [KelasAnggotaController::class, 'store'])->name('anggota.store');
        Route::delete('anggota/{santri}', [KelasAnggotaController::class, 'destroy'])->name('anggota.destroy');
    });

    // Fitur Pindah Asrama (letakkan di atas resource asrama)
    Route::get('asrama/pindah', [AsramaController::class, 'pindahForm'])->name('asrama.pindah.form');
    Route::post('asrama/pindah', [AsramaController::class, 'pindah'])->name('asrama.pindah');

    // Modul Asrama (CRUD)
    Route::resource('asrama', AsramaController::class);

    // Import Asrama (bukan nested)
    Route::post('asrama-import', [AsramaController::class, 'import'])->name('asrama.import');
    Route::get('asrama-import', [AsramaController::class, 'importForm'])->name('asrama.import.form');
    Route::get('asrama-template', [AsramaController::class, 'template'])->name('asrama.template');

    // Anggota Asrama (nested)
    Route::prefix('asrama/{asrama}')->name('asrama.')->group(function () {
        Route::get('anggota', [AsramaAnggotaController::class, 'index'])->name('anggota.index');
        Route::post('anggota', [AsramaAnggotaController::class, 'store'])->name('anggota.store');
        Route::delete('anggota/{santri}', [AsramaAnggotaController::class, 'destroy'])->name('anggota.destroy');
    });

    Route::resource('rfid-tags', RfidTagController::class)->middleware('auth');

    // Modul Keuangan
    Route::prefix('keuangan')->name('keuangan.')->middleware(['auth'])->group(function () {
        // Jenis Tagihan
        Route::get('jenis-tagihan', [JenisTagihanController::class, 'index'])->name('jenis-tagihan.index')->middleware(['role:admin']);
        Route::get('jenis-tagihan/create', [JenisTagihanController::class, 'create'])->name('jenis-tagihan.create')->middleware(['role:admin']);
        Route::post('jenis-tagihan', [JenisTagihanController::class, 'store'])->name('jenis-tagihan.store')->middleware(['role:admin']);
        Route::get('jenis-tagihan/{id}/edit', [JenisTagihanController::class, 'edit'])->name('jenis-tagihan.edit')->middleware(['role:admin']);
        Route::put('jenis-tagihan/{id}', [JenisTagihanController::class, 'update'])->name('jenis-tagihan.update')->middleware(['role:admin']);
        Route::delete('jenis-tagihan/{id}', [JenisTagihanController::class, 'destroy'])->name('jenis-tagihan.destroy')->middleware(['role:admin']);
        Route::get('jenis-tagihan/{id}/kelas', [JenisTagihanController::class, 'showKelas'])->name('jenis-tagihan.show-kelas')->middleware(['role:admin']);
        Route::put('jenis-tagihan/{id}/kelas', [JenisTagihanController::class, 'updateKelas'])->name('jenis-tagihan.update-kelas')->middleware(['role:admin']);
        
        // Buku Kas
        Route::resource('buku-kas', BukuKasController::class)->middleware(['role:admin']);
        Route::get('buku-kas-list', [BukuKasController::class, 'getBukuKasList'])->name('buku-kas.list');
        
        // Jenis Buku Kas
        Route::resource('jenis-buku-kas', \App\Http\Controllers\JenisBukuKasController::class)->middleware(['role:admin']);
        Route::get('jenis-buku-kas-dropdown', [\App\Http\Controllers\JenisBukuKasController::class, 'getForDropdown'])->name('jenis-buku-kas.dropdown');
        
        // Transaksi Kas
        Route::resource('transaksi-kas', \App\Http\Controllers\TransaksiKasController::class)->middleware(['role:admin|bendahara']);
        Route::post('transaksi-kas/{id}/approve', [\App\Http\Controllers\TransaksiKasController::class, 'approve'])->name('transaksi-kas.approve')->middleware(['role:admin|bendahara']);
        Route::post('transaksi-kas/{id}/reject', [\App\Http\Controllers\TransaksiKasController::class, 'reject'])->name('transaksi-kas.reject')->middleware(['role:admin|bendahara']);
        
        // Debug route for testing
        Route::get('debug/buku-kas/{id}', function($id) {
            $bukuKas = \App\Models\BukuKas::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $bukuKas,
                'debug' => 'This is debug route'
            ]);
        })->name('debug.buku-kas');
        
        // Pembayaran Santri
        Route::middleware(['role:admin|bendahara'])->group(function() {
            Route::get('pembayaran-santri', [PembayaranSantriController::class, 'index'])->name('pembayaran-santri.index');
            Route::get('pembayaran-santri/data/{santriId}', [PembayaranSantriController::class, 'getPaymentData'])->name('pembayaran-santri.data');
            Route::post('pembayaran-santri/process', [PembayaranSantriController::class, 'processPayment'])
                ->name('pembayaran-santri.process');
        });
        
        // Tagihan Santri
        Route::middleware(['role:admin|bendahara'])->group(function() {
            Route::get('tagihan-santri', [App\Http\Controllers\TagihanSantriController::class, 'index'])->name('tagihan-santri.index');
            Route::get('tagihan-santri/{santriId}', [App\Http\Controllers\TagihanSantriController::class, 'show'])->name('tagihan-santri.show');
            Route::get('tagihan-santri-export', [App\Http\Controllers\TagihanSantriController::class, 'export'])->name('tagihan-santri.export');
        });
    });

    // Modul Akademik
    Route::prefix('akademik')->name('akademik.')->middleware(['auth'])->group(function () {
        // Tahun Ajaran
        Route::resource('tahun-ajaran', TahunAjaranController::class)->middleware(['role:admin']);
        Route::post('tahun-ajaran/{tahunAjaran}/activate', [TahunAjaranController::class, 'activate'])
            ->name('tahun-ajaran.activate')
            ->middleware(['role:admin']);
    });

    // User Management
    Route::resource('users', \App\Http\Controllers\UserController::class)->middleware(['role:admin']);
    Route::resource('roles', \App\Http\Controllers\RoleController::class)->middleware(['role:admin']);

    // API untuk mengambil data santri dengan asrama untuk modal pindah
    Route::get('/api/santris-with-asrama', [AsramaController::class, 'getSantrisWithAsrama'])->name('api.santris-with-asrama');
}); // Close auth middleware group

require __DIR__.'/auth.php';
