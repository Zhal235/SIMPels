<?php

namespace App\Http\Controllers;

use App\Models\Asrama;
use App\Models\Santri;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AsramaImport;

class AsramaController extends Controller
{
    public function index()
{
    $asrama = Asrama::withCount(['santris' => function($query) {
        $query->where('status', 'aktif');
    }])->paginate(10);
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
        
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Asrama berhasil ditambahkan.']);
        }
        
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
        
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Asrama berhasil diperbarui.']);
        }
        
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

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Santri berhasil dipindahkan ke asrama baru.']);
        }

        return redirect()->route('asrama.pindah.form')->with('success', 'Santri berhasil dipindahkan ke asrama baru.');
    }

    // API endpoint untuk mendapatkan data santri dengan asrama
    public function getSantrisWithAsrama()
    {
        $santris = Santri::with('asrama:id,kode,nama')
            ->where('status', 'aktif')
            ->select('id', 'nis', 'nama_santri', 'asrama_id')
            ->get();
        
        return response()->json($santris);
    }

    public function importForm()
    {
        return view('asrama.import');
    }

    public function import(Request $request)
    {
        $file = $request->file('file');
        if (!$file) {
            return back()->with('error', 'File tidak ditemukan');
        }
        $data = Excel::toArray(new AsramaImport, $file)[0];
        unset($data[0]);
        foreach ($data as $row) {
            // Pastikan kolom urutan: kode, nama, wali_asrama
            if (count($row) >= 2 && $row[0] && $row[1]) {
                \App\Models\Asrama::create([
                    'kode' => $row[0],
                    'nama' => $row[1],
                    'wali_asrama' => $row[2] ?? null,
                ]);
            }
        }
        return back()->with('success', 'Data asrama berhasil diimport.');
    }

    public function template()
    {
        return response()->download(public_path('templates/asrama_template.xlsx'));
    }
}
