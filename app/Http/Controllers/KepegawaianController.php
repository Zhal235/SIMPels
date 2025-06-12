<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KepegawaianController extends Controller
{
    public function index()
    {
        return view('kepegawaian.index');
    }    public function tambahPegawai()
    {
        return redirect()->route('kepegawaian.pegawai.index');
    }
    
    public function kelolaStruktur()
    {
        return redirect()->route('kepegawaian.index')->with('info', 'Fitur Divisi/Bagian telah dihapus');
    }
}
