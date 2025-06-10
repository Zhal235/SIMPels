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

    public function dataKepegawaian()
    {
        return view('kepegawaian.data-kepegawaian.index');
    }

    public function kelolaStruktur()
    {
        return view('kepegawaian.kelola-struktur.index');
    }
}
