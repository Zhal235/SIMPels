<?php

namespace App\Http\Controllers;

use App\Models\MutasiSantri;
use App\Models\Santri; // <-- Tambahkan ini!
use Illuminate\Http\Request;

class MutasiSantriController extends Controller
{
    public function index()
{
    $mutasiList = MutasiSantri::with('santri')->latest()->paginate(15);
    return view('mutasi_santri.index', [
        'mutasiList' => $mutasiList,
    ]);
}




    public function mutasiProses(Request $request, $id)
{
    $request->validate([
        'alasan' => 'required|string',
        'tujuan_mutasi' => 'required|string',
    ]);

    $santri = Santri::findOrFail($id);
    
    MutasiSantri::create([
        'santri_id'      => $id,
        'nama'           => $santri->nama_santri,
        'alasan'         => $request->alasan,
        'tujuan_mutasi'  => $request->tujuan_mutasi,
        'tanggal_mutasi' => now(),
    ]);
    
    // Update status santri jadi mutasi
    $santri->status = 'mutasi';
    $santri->save();

    
    // dd($santri); 
    
    // Remove student from dormitory and class
    // $santri->update([
    //     'asrama_id' => null,
    //     'kelas_id' => null
    // ]);
   
    return redirect()->route('mutasi_santri.index')->with('success', 'Mutasi berhasil!');
}


public function batalkanMutasi($id)
{
    $mutasi = MutasiSantri::findOrFail($id);
    $santri = Santri::findOrFail($mutasi->santri_id);

    // Reset status santri jadi aktif
    $santri->update(['status' => 'aktif']);

    // Hapus data mutasi
    $mutasi->delete();

    return redirect()->route('mutasi_santri.index')->with('success', 'Mutasi dibatalkan!');
}







}
