@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto py-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-bold text-blue-800 flex items-center gap-2">
            <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.592 1L21 12h-4m-7 0h-4M4 12H3c-1.1 0-2 .9-2 2v5a2 2 0 002 2h14a2 2 0 002-2v-5c0-1.1-.9-2-2-2h-1M4 12V7a2 2 0 012-2h10a2 2 0 012 2v5m-6 0a1 1 0 100 2 1 1 0 000-2z" />
            </svg>
            Limit Tarik Tabungan
        </h2>
        <div class="flex gap-2">
            <a href="{{ route('limit-tarik-tabungan.create') }}"
               class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 flex items-center gap-2">
                <span class="text-xl font-bold">+</span> Tambah Limit
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
                    <th class="p-3">Nama Setting</th>
                    <th class="p-3">Limit Harian</th>
                    <th class="p-3">Limit Bulanan</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                {{-- Data limit tarik tabungan akan ditampilkan di sini --}}
            </tbody>
        </table>
    </div>
</div>
@endsection