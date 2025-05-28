<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Kelas;
use App\Models\Pekerjaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Exports\SantriExport;
use App\Exports\SantriTemplateExport;
use App\Imports\SantriImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\MutasiSantri;
use App\Models\KelasAnggota;   // Atau model relasi kelas yang sesuai
use App\Models\AsramaAnggota;

class SantriController extends Controller
{
    /** List & filter */
    public function index(Request $request)
{
    $query = Santri::query();

    // Contoh pada method index()
    $santris = Santri::belumMutasi()->paginate(15);

    // Filter/cari
    if ($request->filled('search')) {
        $s = $request->search;
        $query->where(function($q) use ($s) {
            $q->where('nama_siswa','like',"%{$s}%")
              ->orWhere('nis','like',"%{$s}%");
        });
    }
    if ($request->filled('kelas')) {
        $query->where('kelas_id', $request->kelas);
    }
    if ($request->filled('jenis_kelamin')) {
        $query->where('jenis_kelamin', $request->jenis_kelamin);
    }
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $santris = $query->paginate(15)->withQueryString();
    $kelasList = Kelas::orderBy('nama')->get();

    return view('santris.index', compact('santris','kelasList'));
}


    /** Form tambah */
    public function create()
    {
        $pekerjaans = Pekerjaan::all();
        $kelasList = Kelas::orderBy('nama')->get();
        return view('santris.create', compact('pekerjaans', 'kelasList'));
    }

    /** Simpan baru */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nis'               => 'required|unique:santris,nis',
            'nisn'              => 'nullable',
            'nik_siswa'         => 'nullable',
            'nama_siswa'        => 'required',
            'tempat_lahir'      => 'nullable',
            'tanggal_lahir'     => 'nullable|date',
            'jenis_kelamin'     => 'nullable',
            'kelas_id'          => 'nullable|exists:kelas,id',
            'asal_sekolah'      => 'nullable',
            'hobi'              => 'nullable',
            'cita_cita'         => 'nullable',
            'jumlah_saudara'    => 'nullable|integer',
            'alamat'            => 'nullable',
            'provinsi'          => 'nullable',
            'kabupaten'         => 'nullable',
            'kecamatan'         => 'nullable',
            'desa'              => 'nullable',
            'kode_pos'          => 'nullable',
            'no_kk'             => 'nullable',
            'nama_ayah'         => 'nullable',
            'nik_ayah'          => 'nullable',
            'pendidikan_ayah'   => 'nullable',
            'pekerjaan_ayah'    => 'nullable|exists:pekerjaans,id',
            'hp_ayah'           => 'nullable',
            'nama_ibu'          => 'nullable',
            'nik_ibu'           => 'nullable',
            'pendidikan_ibu'    => 'nullable',
            'pekerjaan_ibu'     => 'nullable|exists:pekerjaans,id',
            'hp_ibu'            => 'nullable',
            'no_bpjs'           => 'nullable',
            'no_pkh'            => 'nullable',
            'no_kip'            => 'nullable',
            'npsn_sekolah'      => 'nullable',
            'no_blanko_skhu'    => 'nullable',
            'no_seri_ijazah'    => 'nullable',
            'total_nilai_un'    => 'nullable|numeric',
            'tanggal_kelulusan' => 'nullable|date',
            'status'            => 'nullable',
            'foto'              => 'nullable|image|mimes:jpeg,png,jpg|max:8192',
        ]);

        // handle pas foto (optional)
        if ($request->hasFile('foto')) {
            $file     = $request->file('foto');
            $nameOnly = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = time().'_'.$nameOnly.'.jpg';
            $imageData = file_get_contents($file->getRealPath());
            $res = imagecreatefromstring($imageData);
            if ($res) {
                $w = imagesx($res);
                if ($w > 800) {
                    $h = imagesy($res);
                    $newH = intval($h * (800/$w));
                    $resized = imagescale($res,800,$newH);
                    imagedestroy($res);
                    $res = $resized;
                }
                ob_start();
                imagejpeg($res,null,75);
                $comp = ob_get_clean();
                imagedestroy($res);
                Storage::disk('public')->put("foto-santri/{$filename}", $comp);
            } else {
                $file->storeAs('foto-santri',$filename,'public');
            }
            $data['foto']="foto-santri/{$filename}";
        }

        Santri::create($data);

        return redirect()->route('santris.index')->with('success','Santri berhasil ditambah!');
    }

    /** Form edit */
    public function edit($id)
    {
        $santri     = Santri::findOrFail($id);
        $pekerjaans = Pekerjaan::all();
        $kelasList  = Kelas::orderBy('nama')->get();
        return view('santris.edit', compact('santri','pekerjaans', 'kelasList'));
    }

    /** Preview detail */
    public function show($id)
    {
        $santri = Santri::with(['pekerjaanAyah','pekerjaanIbu'])->findOrFail($id);
        return view('santris.show', compact('santri'));
    }

    /** Update data */
    public function update(Request $request, $id)
    {
        $santri = Santri::findOrFail($id);
        $data = $request->validate([
            'nis'               => 'required|unique:santris,nis,'.$id,
            'nisn'              => 'nullable',
            'nik_siswa'         => 'nullable',
            'nama_siswa'        => 'required',
            'tempat_lahir'      => 'nullable',
            'tanggal_lahir'     => 'nullable|date',
            'jenis_kelamin'     => 'nullable',
            'kelas_id'          => 'nullable|exists:kelas,id',
            'asal_sekolah'      => 'nullable',
            'hobi'              => 'nullable',
            'cita_cita'         => 'nullable',
            'jumlah_saudara'    => 'nullable|integer',
            'alamat'            => 'nullable',
            'provinsi'          => 'nullable',
            'kabupaten'         => 'nullable',
            'kecamatan'         => 'nullable',
            'desa'              => 'nullable',
            'kode_pos'          => 'nullable',
            'no_kk'             => 'nullable',
            'nama_ayah'         => 'nullable',
            'nik_ayah'          => 'nullable',
            'pendidikan_ayah'   => 'nullable',
            'pekerjaan_ayah'    => 'nullable|exists:pekerjaans,id',
            'hp_ayah'           => 'nullable',
            'nama_ibu'          => 'nullable',
            'nik_ibu'           => 'nullable',
            'pendidikan_ibu'    => 'nullable',
            'pekerjaan_ibu'     => 'nullable|exists:pekerjaans,id',
            'hp_ibu'            => 'nullable',
            'no_bpjs'           => 'nullable',
            'no_pkh'            => 'nullable',
            'no_kip'            => 'nullable',
            'npsn_sekolah'      => 'nullable',
            'no_blanko_skhu'    => 'nullable',
            'no_seri_ijazah'    => 'nullable',
            'total_nilai_un'    => 'nullable|numeric',
            'tanggal_kelulusan' => 'nullable|date',
            'status'            => 'nullable',
            'foto'              => 'nullable|image|mimes:jpeg,png,jpg|max:8192',
        ]);

        // handle update foto
        if ($request->hasFile('foto')) {
            if ($santri->foto && Storage::disk('public')->exists($santri->foto)) {
                Storage::disk('public')->delete($santri->foto);
            }
            $file     = $request->file('foto');
            $nameOnly = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = time().'_'.$nameOnly.'.jpg';
            $imageData = file_get_contents($file->getRealPath());
            $res = imagecreatefromstring($imageData);
            if ($res) {
                $w = imagesx($res);
                if ($w > 800) {
                    $h = imagesy($res);
                    $newH = intval($h * (800/$w));
                    $resized = imagescale($res,800,$newH);
                    imagedestroy($res);
                    $res = $resized;
                }
                ob_start();
                imagejpeg($res,null,75);
                $comp = ob_get_clean();
                imagedestroy($res);
                Storage::disk('public')->put("foto-santri/{$filename}", $comp);
            } else {
                $file->storeAs('foto-santri',$filename,'public');
            }
            $data['foto']="foto-santri/{$filename}";
        }

        $santri->update($data);

        return redirect()->route('santris.index')->with('success','Santri berhasil diperbarui!');
    }

    /** Export Excel (semua atau per kelas) */
    public function export(Request $request)
{
    $kelasId = $request->input('kelas_id');

    return \Maatwebsite\Excel\Facades\Excel::download(
        new \App\Exports\SantriExport($kelasId),
        $kelasId ? "santri_kelas_{$kelasId}.xlsx" : 'santris.xlsx'
    );
}



    /** Download template import */
    public function template()
    {
        return Excel::download(new SantriTemplateExport, 'template_import_santri.xlsx');
    }

    /** Form import (popup/modal) */
    public function importForm()
    {
        $kelasList = Kelas::orderBy('nama')->get();
        return view('santris.import', compact('kelasList'));
    }

    /** Proses import */
    public function import(Request $request)
    {
        $request->validate([
            'file'=>'required|file|mimes:xlsx,xls,csv',
        ]);
        Excel::import(new SantriImport, $request->file('file'));
        return redirect()->route('santris.index')->with('success','Data santri berhasil diimport!');
    }

    /** Hapus data & foto */
    public function destroy($id)
    {
        $santri = Santri::findOrFail($id);
        if ($santri->foto && Storage::disk('public')->exists($santri->foto)) {
            Storage::disk('public')->delete($santri->foto);
        }
        $santri->delete();
        return redirect()->route('santris.index')->with('success','Santri berhasil dihapus!');
    }
   
    


}
