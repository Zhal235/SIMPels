<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Santri;
use Illuminate\Http\Request;

class KelasAnggotaController extends Controller
{
    // List anggota kelas + santri yang belum punya kelas, filter status != mutasi
    public function index(Kelas $kelas)
    {
        // Anggota kelas dengan status bukan mutasi
        $anggota = $kelas->siswa()
                         ->where('status', '!=', 'mutasi')
                         ->get();

        // Santri yang belum punya kelas dan status bukan mutasi
        $santriNotIn = Santri::whereNull('kelas_id')
                             ->where('status', '!=', 'mutasi')
                             ->get();

        return view('kelas.anggota', compact('kelas', 'anggota', 'santriNotIn'));
    }

    // Tambah santri ke kelas
    public function store(Request $request, Kelas $kelas)
    {
        $request->validate([
            'santri_id' => 'required|array'
        ]);
        Santri::whereIn('id', $request->santri_id)
              ->update(['kelas_id' => $kelas->id]);

        return redirect()->route('kelas.anggota.index', $kelas)
                         ->with('success', 'Santri berhasil ditambahkan ke kelas.');
    }

    // Keluarkan santri dari kelas
    public function destroy(Kelas $kelas, Santri $santri)
    {
        if ($santri->kelas_id == $kelas->id) {
            $santri->kelas_id = null;
            $santri->save();
        }
        return redirect()->route('kelas.anggota.index', $kelas)
                         ->with('success', 'Santri berhasil dikeluarkan dari kelas.');
    }

    // Optional: create method jika dibutuhkan
    public function create($kelasId)
    {
        $kelas = Kelas::findOrFail($kelasId);
        $santris = Santri::whereNull('kelas_id')
                         ->where('status', '!=', 'mutasi')
                         ->get();

        return view('kelas.anggota.create', compact('kelas', 'santris'));
    }
}
