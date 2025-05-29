<?php

namespace App\Http\Controllers;

use App\Models\JenisBayar;
use App\Models\JenisKas;
use Illuminate\Http\Request;

class JenisBayarController extends Controller
{
    public function index()
    {
        $jenisBayar = JenisBayar::all();
        $jenisKas = JenisKas::all();
        return view('keuangan.setting.jenis-bayar.index', compact('jenisBayar', 'jenisKas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jenis_bayar' => 'required|string|max:255',
            'klasifikasi' => 'required|in:biaya_rutin_bulanan,biaya_incidential',
            'jenis_kas_id' => 'required|exists:jenis_kas,id',
            // 'nominal' => 'required|numeric|min:0', // Nominal bisa jadi tidak wajib saat create, tergantung kebutuhan
        ]);
        JenisBayar::create($request->only(['nama_jenis_bayar','klasifikasi','jenis_kas_id']));
        return redirect()->route('jenis-bayar.index')->with('success', 'Jenis Bayar berhasil ditambahkan.');
    }

    public function edit(JenisBayar $jenis_bayar)
    {
        $jenisKas = JenisKas::all();
        // Mengembalikan view dengan data yang akan diedit dan data untuk dropdown
        // Jika menggunakan modal, Anda mungkin perlu mengembalikan JSON atau menyesuaikan cara data dikirim ke view.
        // Untuk saat ini, kita asumsikan akan ada halaman edit terpisah atau logika modal yang menangani ini.
        return view('keuangan.setting.jenis-bayar.edit', compact('jenis_bayar', 'jenisKas')); 
    }

    public function update(Request $request, JenisBayar $jenis_bayar)
    {
        $request->validate([
            'nama_jenis_bayar' => 'required|string|max:255',
            'klasifikasi' => 'required|in:biaya_rutin_bulanan,biaya_incidential',
            'jenis_kas_id' => 'required|exists:jenis_kas,id',
            // 'nominal' => 'nullable|numeric|min:0', // Sesuaikan jika nominal diperlukan
        ]);

        $jenis_bayar->update($request->only(['nama_jenis_bayar', 'klasifikasi', 'jenis_kas_id']));

        return redirect()->route('jenis-bayar.index')->with('success', 'Jenis Bayar berhasil diupdate.');
    }

    public function destroy(JenisBayar $jenis_bayar)
    {
        $jenis_bayar->delete();
        return redirect()->route('jenis-bayar.index')->with('success', 'Jenis Bayar berhasil dihapus.');
    }
}