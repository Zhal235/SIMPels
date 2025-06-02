<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Santri;
use Illuminate\Http\Request;

class KelasAnggotaController extends Controller
{
    // List anggota kelas + santri yang belum punya kelas
    public function index(Kelas $kelas)
    {
        // Anggota kelas (pivot)
        $anggota = $kelas->santri()->where('status', 'aktif')->get();

        // Santri yang belum punya kelas (tidak ada di pivot)
        $santriNotIn = Santri::whereDoesntHave('kelasRelasi')
                             ->where('status', 'aktif')
                             ->get();

        return view('kelas.anggota', compact('kelas', 'anggota', 'santriNotIn'));
    }

    // Tambah santri ke kelas (isi tabel pivot)
    public function store(Request $request, Kelas $kelas)
    {
        $request->validate([
            'santri_id' => 'required|array'
        ]);
        $kelas->santri()->attach($request->santri_id);

        return redirect()->route('kelas.anggota.index', $kelas)
                         ->with('success', 'Santri berhasil ditambahkan ke kelas.');
    }

    // Keluarkan santri dari kelas (hapus dari pivot)
    public function destroy(Kelas $kelas, Santri $santri)
    {
        $kelas->santri()->detach($santri->id);

        return redirect()->route('kelas.anggota.index', $kelas)
                         ->with('success', 'Santri berhasil dikeluarkan dari kelas.');
    }

    public function create($kelasId)
    {
        $kelas = Kelas::findOrFail($kelasId);
        $santris = Santri::whereDoesntHave('kelasRelasi')->get();

        return view('kelas.anggota.create', compact('kelas', 'santris'));
    }
}
