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
    $asrama = Asrama::withCount(['anggota_asrama' => function($query) {
        $query->whereHas('santri', function($q) {
            $q->where('status', 'aktif');
        });
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

        // Nonaktifkan semua anggota asrama yang aktif
        foreach ($asrama->anggota_asrama()->where('status', 'aktif')->get() as $anggota) {
            $anggota->status = 'nonaktif';
            $anggota->tanggal_keluar = now();
            $anggota->save();
        }

        // Hapus asrama
        $asrama->delete();

        return redirect()->route('asrama.index')->with('success', 'Asrama dan anggotanya berhasil dihapus!');
    }
    
    // Menampilkan detail asrama dan anggota
    public function show(Asrama $asrama)
    {
        // Load santri aktif di asrama ini melalui relasi anggota_asrama
        $anggotaAsrama = $asrama->anggota_asrama()
            ->with('santri')
            ->whereHas('santri', function($query) {
                $query->where('status', 'aktif');
            })
            ->where('status', 'aktif')
            ->get();
            
        return view('asrama.show', compact('asrama', 'anggotaAsrama'));
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
        
        $asramaBaruId = $request->asrama_id;
        $tahunAjaran = \App\Models\TahunAjaran::getActive();
        
        if (!$tahunAjaran) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        $successCount = 0;
        
        foreach ($request->santri_id as $santriId) {
            // Nonaktifkan asrama anggota yang lama
            \App\Models\AsramaAnggota::where('santri_id', $santriId)
                ->where('status', 'aktif')
                ->update([
                    'status' => 'nonaktif',
                    'tanggal_keluar' => now()
                ]);
                
            // Buat asrama anggota baru
            \App\Models\AsramaAnggota::create([
                'santri_id' => $santriId,
                'asrama_id' => $asramaBaruId,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'tanggal_masuk' => now(),
                'status' => 'aktif'
            ]);
            
            $successCount++;
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $successCount . ' santri berhasil dipindahkan ke asrama baru.']);
        }

        return redirect()->route('asrama.pindah.form')->with('success', $successCount . ' santri berhasil dipindahkan ke asrama baru.');
    }

    // API endpoint untuk mendapatkan data santri dengan asrama
    public function getSantrisWithAsrama()
    {
        $santris = Santri::with(['asrama_anggota_terakhir.asrama' => function ($query) {
                $query->select('id', 'kode', 'nama');
            }])
            ->where('status', 'aktif')
            ->select('id', 'nis', 'nama_santri')
            ->get()
            ->map(function($santri) {
                return [
                    'id' => $santri->id,
                    'nis' => $santri->nis,
                    'nama_santri' => $santri->nama_santri,
                    'asrama' => $santri->asrama_anggota_terakhir->asrama ?? null
                ];
            });
        
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
