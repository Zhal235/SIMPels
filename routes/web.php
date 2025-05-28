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
use App\Http\Controllers\SettingPembayaranController;
use App\Http\Controllers\BuktiTransferWaliMuridController;
use App\Http\Controllers\LimitTarikTabunganController;
use App\Http\Controllers\TabunganSantriController;
use App\Http\Controllers\KasBankController;
use App\Http\Controllers\PenggajianController;
use App\Http\Controllers\HutangController;
use App\Http\Controllers\KirimTagihanController;

// Redirect root ke data santri
Route::get('/', fn() => redirect()->route('santris.index'));

Route::middleware(['auth'])->group(function () {

    // Modul Santri
    Route::resource('santris', SantriController::class);
    Route::get('santris-export', [SantriController::class, 'export'])->name('santris.export');
    Route::get('santris-template', [SantriController::class, 'template'])->name('santris.template');
    Route::get('santris-import', [SantriController::class, 'importForm'])->name('santris.import.form');
    Route::post('santris-import', [SantriController::class, 'import'])->name('santris.import');

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

    // Anggota Asrama (nested)
    Route::prefix('asrama/{asrama}')->name('asrama.')->group(function () {
        Route::get('anggota', [AsramaAnggotaController::class, 'index'])->name('anggota.index');
        Route::post('anggota', [AsramaAnggotaController::class, 'store'])->name('anggota.store');
        Route::delete('anggota/{santri}', [AsramaAnggotaController::class, 'destroy'])->name('anggota.destroy');
    });

    Route::resource('rfid-tags', RfidTagController::class)->middleware('auth');

    // Modul Keuangan
    Route::resource('pembayaran-santri', PembayaranSantriController::class);
    Route::resource('setting-pembayaran', SettingPembayaranController::class);
    Route::resource('bukti-transfer-wali-murid', BuktiTransferWaliMuridController::class);
    Route::resource('limit-tarik-tabungan', LimitTarikTabunganController::class);
    Route::resource('tabungan-santri', TabunganSantriController::class);
    Route::resource('kas-bank', KasBankController::class);
    Route::resource('penggajian', PenggajianController::class);
    Route::resource('hutang', HutangController::class);
    Route::resource('kirim-tagihan', KirimTagihanController::class);

});

require __DIR__.'/auth.php';
