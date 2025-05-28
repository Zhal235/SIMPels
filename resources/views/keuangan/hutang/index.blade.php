@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto py-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-bold text-blue-800 flex items-center gap-2">
            <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Hutang
        </h2>
        <div class="flex gap-2">
            <a href="{{ route('hutang.create') }}"
               class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 flex items-center gap-2">
                <span class="text-xl font-bold">+</span> Tambah Hutang
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded shadow">{{ session('success') }}</div>
    @endif

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full text-sm text-left border">
            <thead class="bg-gray-100 text-xs font-semibold uppercase">
                <tr>
                    <th class="p-3">No</th>
                    <th class="p-3">Nama Peminjam</th>
                    <th class="p-3">Jumlah Hutang</th>
                    <th class="p-3">Tanggal Pinjam</th>
                    <th class="p-3">Tanggal Jatuh Tempo</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                {{-- Data hutang akan ditampilkan di sini --}}
            </tbody>
        </table>
    </div>
</div>
@endsection