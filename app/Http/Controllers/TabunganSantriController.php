<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TabunganSantriController extends Controller
{
    public function index()
    {
        return view('keuangan.tabungan-santri.index');
    }
}
