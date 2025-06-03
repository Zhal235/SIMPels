<?php

namespace App\Http\Controllers;

use App\Models\JenisPembayaran;
use Illuminate\Http\Request;

class JenisPembayaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jenisPembayarans = JenisPembayaran::latest()->paginate(10);
        return view('keuangan.jenis_pembayaran.index', compact('jenisPembayarans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('keuangan.jenis_pembayaran.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori_pembayaran' => 'required|in:Rutin,Insidental',
            'nominal_tagihan' => 'required|numeric|min:0',
        ]);

        JenisPembayaran::create($request->all());

        return redirect()->route('jenis-pembayaran.index')
            ->with('success', 'Jenis Pembayaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(JenisPembayaran $jenisPembayaran)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JenisPembayaran $jenisPembayaran)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JenisPembayaran $jenisPembayaran)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JenisPembayaran $jenisPembayaran)
    {
        //
    }
}
