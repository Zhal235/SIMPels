<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Santri;
use App\Models\KelasAnggota;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

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