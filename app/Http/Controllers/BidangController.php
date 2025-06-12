<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class BidangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Redirect to kepegawaian dashboard
        return Redirect::route('kepegawaian.index')->with('info', 'Fitur Divisi/Bagian telah dihapus');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Redirect::route('kepegawaian.index')->with('info', 'Fitur Divisi/Bagian telah dihapus');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return Redirect::route('kepegawaian.index')->with('info', 'Fitur Divisi/Bagian telah dihapus');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Redirect::route('kepegawaian.index')->with('info', 'Fitur Divisi/Bagian telah dihapus');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return Redirect::route('kepegawaian.index')->with('info', 'Fitur Divisi/Bagian telah dihapus');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return Redirect::route('kepegawaian.index')->with('info', 'Fitur Divisi/Bagian telah dihapus');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return Redirect::route('kepegawaian.index')->with('info', 'Fitur Divisi/Bagian telah dihapus');
    }

    /**
     * Toggle status of the specified resource.
     */
    public function toggleStatus(string $id)
    {
        return Redirect::route('kepegawaian.index')->with('info', 'Fitur Divisi/Bagian telah dihapus');
    }
}
