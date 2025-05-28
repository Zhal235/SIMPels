@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    <h1 class="text-2xl font-bold text-gray-700 mb-6">📊 Dashboard</h1>

    {{-- Grid untuk ringkasan --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Kartu Total Santri --}}
        <div class="bg-gradient-to-br from-blue-600 to-blue-400 text-white p-4 rounded-md shadow flex items-center justify-between">
            <div>
                <div class="text-sm font-semibold opacity-90">Total Santri</div>
                <div class="text-xl font-bold">{{ $jumlahSantri }} Santri</div>
            </div>
            <div class="ml-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5.121 17.804A13.937 13.937 0 0112 15c2.485 0 4.779.657 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>

        {{-- Kartu Placeholder 2 --}}
        <div class="bg-gradient-to-br from-green-600 to-green-400 text-white p-4 rounded-md shadow">
            <div class="text-sm font-semibold opacity-90">Total Alumni</div>
            <div class="text-xl font-bold">–</div>
        </div>

        {{-- Kartu Placeholder 3 --}}
        <div class="bg-gradient-to-br from-yellow-600 to-yellow-400 text-white p-4 rounded-md shadow">
            <div class="text-sm font-semibold opacity-90">Pembayaran Siswa</div>
            <div class="text-xl font-bold">–</div>
        </div>

        {{-- Kartu Placeholder 4 --}}
        <div class="bg-gradient-to-br from-red-600 to-red-400 text-white p-4 rounded-md shadow">
            <div class="text-sm font-semibold opacity-90">Tabungan Siswa</div>
            <div class="text-xl font-bold">–</div>
        </div>
    </div>
</div>
@endsection
