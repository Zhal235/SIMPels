<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Santri;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\KelasImport;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class KelasController extends Controller
{
    // List Kelas
    public function index()
    {
        $kelas = Kelas::withCount(['anggota' => function($query) {
            $query->whereHas('santri', function($q) {
                $q->where('status', 'aktif');
            });
        }])->paginate(10);
        return view('kelas.index', compact('kelas'));
    }

    // Form Buat Kelas
    public function create()
{
    // Kosongkan dulu karena menu kepegawaian belum dibuat
    // Nanti akan diisi ketika menu kepegawaian sudah tersedia
    $waliList = [];

    return view('kelas.create', compact('waliList'));
}

    // Simpan Kelas
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required|string|max:100',
                'kode' => 'required|string|max:20|unique:kelas,kode',
                'tingkat' => 'required',
                'wali_kelas' => 'nullable|string|max:100' // Sementara string karena menu kepegawaian belum ada
            ]);
            
            $kelas = Kelas::create($request->all());
            
            // Tambahkan kelas baru sebagai aktif di tabel pivot kelas_anggota
            DB::table('kelas_anggota')->insert([
                'kelas_id' => $kelas->id,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Check if request is AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kelas berhasil ditambahkan.',
                    'kelas' => $kelas
                ]);
            }
            
            return redirect()->route('kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
            
        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }
    }

    // Edit Kelas
    public function edit(Kelas $kelas)
    {
        // Kosongkan dulu karena menu kepegawaian belum dibuat
        // Nanti akan diisi ketika menu kepegawaian sudah tersedia
        $waliList = [];
        return view('kelas.edit', compact('kelas', 'waliList'));
    }

    // Update Kelas
    public function update(Request $request, Kelas $kelas)
    {
        try {
            $request->validate([
                'nama' => 'required|string|max:100',
                'kode' => 'required|string|max:20|unique:kelas,kode,'.$kelas->id,
                'tingkat' => 'required',
                'wali_kelas' => 'nullable|string|max:100'
            ]);
            
            $kelas->update($request->all());
            
            // Handle AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kelas berhasil diperbarui.',
                    'kelas' => $kelas
                ]);
            }
            
            return redirect()->route('kelas.index')->with('success', 'Kelas berhasil diperbarui.');
            
        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }
    }

    // Hapus Kelas
    public function destroy(Kelas $kelas)
    {
        $kelas->delete();
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }

    // ---------------- PINDAH KELAS (EXTRA FEATURE) ----------------
    // Tampilkan Form Pindah
    public function pindahForm()
    {
        $kelasList = Kelas::all();
        $santris = Santri::all();
        return view('kelas.pindah', compact('kelasList', 'santris'));
    }

    // Proses Pindah Kelas
    public function pindah(Request $request)
    {
        $request->validate([
            'santri_id' => 'required|array',
            'kelas_id' => 'required|exists:kelas,id'
        ]);
        Santri::whereIn('id', $request->santri_id)->update(['kelas_id' => $request->kelas_id]);
        return redirect()->route('kelas.pindah.form')->with('success', 'Santri berhasil dipindahkan!');
    }
    public function importForm()
{
    return view('kelas.import'); // bikin file import.blade.php
}

public function import(Request $request)
{
    $file = $request->file('file');
    if (!$file) {
        return back()->with('error', 'File tidak ditemukan');
    }
    $data = Excel::toArray(new KelasImport, $file)[0];

    // Skip header
    unset($data[0]);
    foreach ($data as $row) {
        // Pastikan kolom urutan: kode, nama, tingkat
        if (count($row) >= 3 && $row[0] && $row[1] && $row[2]) {
            \App\Models\Kelas::create([
                'kode' => $row[0],
                'nama' => $row[1],
                'tingkat' => $row[2],
            ]);
        }
    }
    return back()->with('success', 'Data kelas berhasil diimport.');
}

// KelasController.php
public function template()
{
    return response()->download(public_path('templates/kelas_template.xlsx'));
}


}
