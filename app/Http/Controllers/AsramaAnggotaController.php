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

        // Anggota asrama dengan status aktif melalui tabel relasi
        $anggota = Santri::whereHas('asrama_anggota', function($query) use ($asrama_id) {
                        $query->where('asrama_id', $asrama_id);
                    })
                    ->where('status', 'aktif')
                    ->get();
        
        // Santri yang belum punya asrama dan status aktif
        $santriNotIn = Santri::whereDoesntHave('asrama_anggota')
                             ->where('status', 'aktif')
                             ->get();

        return view('asrama.anggota', compact('asrama', 'anggota', 'santriNotIn'));
    }

    public function store(Request $request, $asrama_id)
    {
        $request->validate([
            'santri_id' => 'required|array',
        ]);

        $asrama = Asrama::findOrFail($asrama_id);
        $tahunAjaran = \App\Models\TahunAjaran::getActive();
        
        if (!$tahunAjaran) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        // Tambahkan ke tabel asrama_anggotas
        foreach ($request->santri_id as $santri_id) {
            \App\Models\AsramaAnggota::create([
                'santri_id' => $santri_id,
                'asrama_id' => $asrama_id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'tanggal_masuk' => now(),
                'status' => 'aktif'
            ]);
        }

        return redirect()->route('asrama.anggota.index', $asrama_id)
                         ->with('success', 'Santri berhasil ditambahkan ke asrama.');
    }

    public function destroy($asrama_id, $santri_id)
    {
        $asramaAnggota = \App\Models\AsramaAnggota::where('santri_id', $santri_id)
                        ->where('asrama_id', $asrama_id)
                        ->where('status', 'aktif')
                        ->firstOrFail();
        
        // Set status ke 'nonaktif' dan tanggal keluar
        $asramaAnggota->status = 'nonaktif';
        $asramaAnggota->tanggal_keluar = now();
        $asramaAnggota->save();

        return redirect()->route('asrama.anggota.index', $asrama_id)
                         ->with('success', 'Santri berhasil dikeluarkan dari asrama.');
    }
}
