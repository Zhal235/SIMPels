<?php

namespace App\Http\Controllers;

use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class JenisTagihanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jenisTagihans = JenisTagihan::with('tahunAjaran')->paginate(10);
        $activeTahunAjaran = TahunAjaran::getActive();
        return view('keuangan.jenis_tagihan.index', compact('jenisTagihans', 'activeTahunAjaran'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tahunAjarans = TahunAjaran::all();
        return view('keuangan.jenis_tagihan.create', compact('tahunAjarans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'nama' => 'required|string|max:255',
            'nominal' => 'nullable|numeric|min:0',
            'deskripsi' => 'nullable|string'
        ];

        // Validasi kondisional berdasarkan jenis pembayaran
        if ($request->jenis_pembayaran === 'rutin') {
            $rules['is_bulanan'] = 'required|boolean';
            if ($request->is_bulanan) {
                $rules['bulan_pembayaran'] = 'required|array|min:1';
                $rules['bulan_pembayaran.*'] = 'integer|between:1,12';
            }
        } elseif ($request->jenis_pembayaran === 'insidentil') {
            $rules['tahun_ajaran_id'] = 'required|exists:tahun_ajarans,id';
            $rules['bulan_pembayaran'] = 'required|array|min:1';
            $rules['bulan_pembayaran.*'] = 'integer|between:1,12';
        }

        $validated = $request->validate($rules);

        // Set default values berdasarkan jenis pembayaran
        if ($request->jenis_pembayaran === 'rutin') {
            $validated['tahun_ajaran_id'] = null;
            if (!$request->is_bulanan) {
                $validated['bulan_pembayaran'] = null;
            }
        } elseif ($request->jenis_pembayaran === 'insidentil') {
            $validated['is_bulanan'] = false;
        }

        JenisTagihan::create($validated);

        return redirect()->route('jenis-tagihan.index')
            ->with('success', 'Jenis pembayaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(JenisTagihan $jenisTagihan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JenisTagihan $jenisTagihan)
    {
        $tahunAjarans = TahunAjaran::all();
        return view('keuangan.jenis_tagihan.edit', compact('jenisTagihan', 'tahunAjarans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JenisTagihan $jenisTagihan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori_tagihan' => 'required|in:rutin,insidentil',
            'jenis_kas' => 'required|string|max:255',
        ]);

        $jenisTagihan->update($validated);

        return redirect()->route('jenis-tagihan.index')
            ->with('success', 'Jenis pembayaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JenisTagihan $jenisTagihan)
    {
        try {
            $jenisTagihan->delete();
            return redirect()->route('jenis-tagihan.index')
                ->with('success', 'Jenis pembayaran berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('jenis-tagihan.index')
                ->with('error', 'Gagal menghapus jenis pembayaran. Data mungkin masih digunakan.');
        }
    }
}
