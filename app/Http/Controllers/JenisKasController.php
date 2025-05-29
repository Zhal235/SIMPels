<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JenisKas;

class JenisKasController extends Controller
{
    public function index()
    {
        $jenisKas = JenisKas::all();
        return view('keuangan.setting.jenis-kas.index', compact('jenisKas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_kas' => 'required|string|max:50|unique:jenis_kas,kode_kas',
            'nama_jenis_kas' => 'required|string|max:255',
            'keterangan' => 'required|string|max:255',
        ]);
        JenisKas::create($request->only('kode_kas', 'nama_jenis_kas', 'keterangan'));
        return redirect()->route('jenis-kas.index')->with('success', 'Jenis Kas berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_kas' => 'required|string|max:50|unique:jenis_kas,kode_kas,' . $id,
            'nama_jenis_kas' => 'required|string|max:255',
            'keterangan' => 'required|string|max:255',
        ]);
        $jenisKas = JenisKas::findOrFail($id);
        $jenisKas->update($request->only('kode_kas', 'nama_jenis_kas', 'keterangan'));
        return redirect()->route('jenis-kas.index')->with('success', 'Jenis Kas berhasil diupdate.');
    }

    public function destroy($id)
    {
        $jenisKas = JenisKas::findOrFail($id);
        $jenisKas->delete();
        return redirect()->route('jenis-kas.index')->with('success', 'Jenis Kas berhasil dihapus.');
    }
}