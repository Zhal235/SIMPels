<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HutangController extends Controller
{
    public function index()
    {
        return view('keuangan.hutang.index');
    }
}
