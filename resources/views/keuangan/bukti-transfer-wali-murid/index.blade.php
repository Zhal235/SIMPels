@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto py-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-bold text-blue-800 flex items-center gap-2">
            <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            Bukti Transfer Wali Murid
        </h2>
        <div class="flex gap-2">
            <a href="{{ route('bukti-transfer-wali-murid.create') }}"
               class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 flex items-center gap-2">
                <span class="text-xl font-bold">+</span> Tambah Bukti Transfer
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
                    <th class="p-3">Santri</th>
                    <th class="p-3">Nama Pengirim</th>
                    <th class="p-3">Jumlah Transfer</th>
                    <th class="p-3">Tanggal Transfer</th>
                    <th class="p-3">Bank Tujuan</th>
                    <th class="p-3">Bukti Gambar</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                {{-- Data bukti transfer wali murid akan ditampilkan di sini --}}
            </tbody>
        </table>
    </div>
</div>
@endsection