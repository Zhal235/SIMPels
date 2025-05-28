<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BuktiTransferWaliMuridController extends Controller
{
    public function index()
    {
        return view('keuangan.bukti-transfer-wali-murid.index');
    }
}
