<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Santri;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $jumlahSantri = Santri::count();
        return view('dashboard', compact('jumlahSantri'));
    }
}
