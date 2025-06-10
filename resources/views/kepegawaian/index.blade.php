@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-semibold text-gray-800">Modul Kepegawaian</h1>
    <p class="mt-2 text-gray-600">Selamat datang di modul Kepegawaian. Silakan pilih sub-menu untuk melanjutkan.</p>
    {{-- Sub-menu links can be added here or directly in sidebar --}}

    <div class="mt-6">
        <h2 class="text-xl font-semibold text-gray-700">Sub Menu:</h2>
        <ul class="list-disc list-inside mt-2">
            <li><a href="{{ route('kepegawaian.tambah-pegawai') }}" class="text-blue-600 hover:underline">Tambah Pegawai</a></li>
            <li><a href="{{ route('kepegawaian.data-kepegawaian') }}" class="text-blue-600 hover:underline">Data Kepegawaian</a></li>
            <li><a href="{{ route('kepegawaian.kelola-struktur') }}" class="text-blue-600 hover:underline">Kelola Struktur Organisasi</a></li>
        </ul>
    </div>
</div>
@endsection
