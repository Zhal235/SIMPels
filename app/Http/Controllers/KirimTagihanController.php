<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KirimTagihanController extends Controller
{
    public function index()
    {
        return view('keuangan.kirim-tagihan.index');
    }
}
