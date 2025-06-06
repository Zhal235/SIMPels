<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KeringananTagihanController;

// Routes untuk keringanan tagihan
Route::prefix('keuangan')->name('keuangan.')->middleware(['auth', 'role:admin|bendahara'])->group(function () {
    Route::resource('keringanan-tagihan', KeringananTagihanController::class);
    Route::get('keringanan-santri/{santriId}', [KeringananTagihanController::class, 'show'])->name('keringanan-santri.show');
});
