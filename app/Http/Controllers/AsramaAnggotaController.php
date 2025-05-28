<?php

namespace App\Http\Controllers;

use App\Models\Asrama;
use App\Models\Santri;
use Illuminate\Http\Request;

class AsramaAnggotaController extends Controller
{
    public function index($asrama_id)
    {
        $asrama = Asrama::findOrFail($asrama_id);

        // Anggota asrama dengan status bukan mutasi
        $anggota = Santri::where('asrama_id', $asrama_id)
                         ->where('status', '<>', 'mutasi')
                         ->get();

        // Santri yang belum punya asrama dan status bukan mutasi
        $santriNotIn = Santri::whereNull('asrama_id')
                             ->where('status', '<>', 'mutasi')
                             ->get();

        return view('asrama.anggota', compact('asrama', 'anggota', 'santriNotIn'));
    }

    public function store(Request $request, $asrama_id)
    {
        $request->validate([
            'santri_id' => 'required|array',
        ]);

        Santri::whereIn('id', $request->santri_id)
              ->update(['asrama_id' => $asrama_id]);

        return redirect()->route('asrama.anggota.index', $asrama_id)
                         ->with('success', 'Santri berhasil ditambahkan ke asrama.');
    }

    public function destroy($asrama_id, $santri_id)
    {
        $santri = Santri::where('id', $santri_id)
                        ->where('asrama_id', $asrama_id)
                        ->firstOrFail();

        $santri->asrama_id = null;
        $santri->save();

        return redirect()->route('asrama.anggota.index', $asrama_id)
                         ->with('success', 'Santri berhasil dikeluarkan dari asrama.');
    }
}
