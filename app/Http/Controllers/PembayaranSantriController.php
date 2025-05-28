<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PembayaranSantriController extends Controller
{
    public function index()
    {
        return view('keuangan.pembayaran-santri.index');
    }
}
