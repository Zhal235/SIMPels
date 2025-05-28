<?php

namespace App\Http\Controllers;

use App\Models\RfidTag;
use App\Models\Santri;
use App\Models\Kelas;
use Illuminate\Http\Request;

class RfidTagController extends Controller
{
    /**
     * Tampilkan daftar Santri + mapping RFID, 
     * lengkap dengan pencarian & filter.
     */
    public function index(Request $request)
{
    $query = \App\Models\Santri::with(['rfidTag', 'kelasRelasi'])
                   ->where('status', 'aktif') // Tambahkan filter status aktif
                   ->orderBy('nama_siswa');

    if ($s = $request->search) {
        $query->where(function($q) use ($s) {
            $q->where('nama_siswa', 'like', "%{$s}%")
              ->orWhere('nis', 'like', "%{$s}%");
        });
    }

    if ($cid = $request->kelas) {
        $query->whereHas('kelasRelasi', function($q) use ($cid) {
            $q->where('kelas.id', $cid);
        });
    }
    if ($jk = $request->jenis_kelamin) {
        $query->where('jenis_kelamin', $jk);
    }
    if ($st = $request->status) {
        $query->where('status', $st);
    }

    $santris   = $query->paginate(15)->withQueryString();
    $kelasList = \App\Models\Kelas::orderBy('nama')->get();

    return view('rfid_tags.index', compact('santris','kelasList'));
}



    /**
     * Tampilkan form assign RFID untuk satu Santri.
     */
    public function create()
    {
        $santris = Santri::where('status', 'aktif')->orderBy('nama_siswa')->get();
        return view('rfid_tags.create', compact('santris'));
    }

    /**
     * Simpan mapping RFID → Santri.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'santri_id' => 'nullable|exists:santris,id',
            'tag_uid'   => 'required|unique:rfid_tags,tag_uid',
            'pin'       => 'nullable|string',
        ]);

        RfidTag::create($data);

        return redirect()
            ->route('rfid-tags.index')
            ->with('success', 'UID RFID berhasil disimpan.');
    }

    /**
     * Tampilkan form edit mapping RFID.
     */
    public function edit(RfidTag $rfidTag)
    {
        $santris = Santri::orderBy('nama_siswa')->get();
        return view('rfid_tags.edit', compact('rfidTag', 'santris'));
    }

    /**
     * Proses update mapping RFID.
     */
    public function update(Request $request, RfidTag $rfidTag)
    {
        $data = $request->validate([
            'santri_id' => 'nullable|exists:santris,id',
            'tag_uid'   => 'required|unique:rfid_tags,tag_uid,' . $rfidTag->id,
            'pin'       => 'nullable|string',
        ]);

        $rfidTag->update($data);

        return redirect()
            ->route('rfid-tags.index')
            ->with('success', 'Data RFID berhasil diperbarui.');
    }

    /**
     * Hapus mapping RFID.
     */
    public function destroy(RfidTag $rfidTag)
    {
        $rfidTag->delete();

        return back()->with('success', 'UID RFID berhasil dihapus.');
    }
}
