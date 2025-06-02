<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\KategoriKeuangan;

class PembayaranSantriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua santri aktif dengan relasi asrama dan kelas
        $santris = Santri::where('status', 'aktif')
            ->with(['asrama', 'asrama_anggota_terakhir.asrama', 'kelas_anggota.kelas'])
            ->orderBy('nama_santri')
            ->get()
            ->map(function ($santri) {
                // Ambil kelas terakhir
                $kelasAnggota = $santri->kelas_anggota->last();
                $kelas = $kelasAnggota ? $kelasAnggota->kelas->nama_kelas : 'Belum ada kelas';
                
                // Ambil asrama terakhir
                $asramaAnggota = $santri->asrama_anggota_terakhir;
                $asrama = $asramaAnggota ? $asramaAnggota->asrama->nama_asrama : 'Belum ada asrama';
                
                return [
                    'id' => $santri->id,
                    'nama' => $santri->nama_santri,
                    'nis' => $santri->nis,
                    'tempat_lahir' => $santri->tempat_lahir,
                    'tanggal_lahir' => $santri->tanggal_lahir,
                    'kelas' => $kelas,
                    'asrama' => $asrama,
                    'nama_ortu' => $santri->nama_ayah,
                    'no_hp' => $santri->hp_ayah,
                    'foto' => $santri->foto ? asset('storage/' . $santri->foto) : asset('img/default-avatar.png')
                ];
            });

        // Ambil kategori keuangan untuk jenis pembayaran
        $kategoriKeuangan = KategoriKeuangan::all();

        return view('pembayaran_santri.index', compact('santris', 'kategoriKeuangan'));
    }

    /**
     * Display the receipt page.
     */
    public function kwitansi()
    {
        // Halaman kwitansi hanya menampilkan template, data diambil dari localStorage
        return view('pembayaran_santri.kwitansi');
    }

    // Metode lainnya bisa ditambahkan di sini sesuai kebutuhan
    // seperti create, store, edit, update, destroy, dll.
}