<?php

namespace App\Http\Controllers;

use App\Models\Asrama;
use App\Models\Santri;
use Illuminate\Http\Request;

class AsramaController extends Controller
{
    public function index()
{
    $asrama = Asrama::withCount('santris')->paginate(10);
    return view('asrama.index', compact('asrama'));
}


    public function create()
    {
        return view('asrama.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode' => 'required|unique:asrama,kode',
            'nama' => 'required|string|max:100',
            'wali_asrama' => 'nullable|string|max:100',
        ]);
        Asrama::create($data);
        return redirect()->route('asrama.index')->with('success', 'Asrama berhasil ditambahkan.');
    }

    public function edit(Asrama $asrama)
    {
        return view('asrama.edit', compact('asrama'));
    }

    public function update(Request $request, Asrama $asrama)
    {
        $data = $request->validate([
            'kode' => 'required|unique:asrama,kode,'.$asrama->id,
            'nama' => 'required|string|max:100',
            'wali_asrama' => 'nullable|string|max:100',
        ]);
        $asrama->update($data);
        return redirect()->route('asrama.index')->with('success', 'Asrama berhasil diperbarui.');
    }

    public function destroy($id)
{
    $asrama = Asrama::findOrFail($id);

    // Set asrama_id semua santri yang ada di asrama ini menjadi NULL
    foreach ($asrama->santris as $santri) {
        $santri->asrama_id = null;
        $santri->save();
    }

    // Hapus asrama
    $asrama->delete();

    return redirect()->route('asrama.index')->with('success', 'Asrama dan anggotanya berhasil dihapus!');
}


    // Form pindah asrama
    public function pindahForm()
    {
        $asramaList = Asrama::all();
        $santris = Santri::all();
        return view('asrama.pindah', compact('asramaList', 'santris'));
    }

    // Proses pindah asrama
    public function pindah(Request $request)
    {
        $request->validate([
            'santri_id' => 'required|array',
            'asrama_id' => 'required|exists:asrama,id',
        ]);

        Santri::whereIn('id', $request->santri_id)->update(['asrama_id' => $request->asrama_id]);

        return redirect()->route('asrama.pindah.form')->with('success', 'Santri berhasil dipindahkan ke asrama baru.');
    }
}
