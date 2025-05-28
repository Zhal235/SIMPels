<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LimitTarikTabunganController extends Controller
{
    public function index()
    {
        return view('keuangan.limit-tarik-tabungan.index');
    }
}
