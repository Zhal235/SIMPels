<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TunggakanController;

Route::middleware(['auth'])->prefix('keuangan/tunggakan')->name('keuangan.tunggakan.')->group(function () {
    // Santri Aktif
    Route::get('/santri-aktif', [TunggakanController::class, 'santriAktif'])->name('santri-aktif');
    
    // Santri Mutasi
    Route::get('/santri-mutasi', [TunggakanController::class, 'santriMutasi'])->name('santri-mutasi');
    
    // Santri Alumni
    Route::get('/santri-alumni', [TunggakanController::class, 'santriAlumni'])->name('santri-alumni');
    
    // Detail Tunggakan
    Route::get('/detail/{santri_id}', [TunggakanController::class, 'detail'])->name('detail');
    
    // Export Excel
    Route::get('/export-excel', [TunggakanController::class, 'exportExcel'])->name('export-excel');
    
    // Print Laporan
    Route::get('/print', [TunggakanController::class, 'printLaporan'])->name('print');
    
    // Automation Management
    Route::get('/automation', [TunggakanController::class, 'automationManagement'])->name('automation');
    Route::post('/copy-routine', [TunggakanController::class, 'copyRoutineTagihan'])->name('copy-routine');
    Route::post('/preview-copy-routine', [TunggakanController::class, 'previewCopyRoutineTagihan'])->name('preview-copy-routine');
    Route::post('/automation-preview', [TunggakanController::class, 'automationPreview'])->name('automation-preview');
    Route::post('/automation-execute', [TunggakanController::class, 'automationExecute'])->name('automation-execute');
});
