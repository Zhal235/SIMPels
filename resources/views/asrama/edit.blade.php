@extends('layouts.admin')

@section('content')
<div class="max-w-md mx-auto py-8">
    <h2 class="text-2xl font-semibold text-blue-800 mb-6 flex items-center gap-2">
        <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 7V3m8 4V3m-8 4a4 4 0 004 4h0a4 4 0 004-4m-8 4v6a4 4 0 004 4h0a4 4 0 004-4v-6"/>
        </svg>
        Edit Asrama
    </h2>
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded shadow">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('asrama.update', $asrama->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700">Kode Asrama</label>
            <input type="text" name="kode" value="{{ old('kode', $asrama->kode) }}"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
                required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Nama Asrama</label>
            <input type="text" name="nama" value="{{ old('nama', $asrama->nama) }}"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
                required>
        </div>
        <div>
    <label class="block text-sm font-medium text-gray-700">Wali Asrama</label>
    <input type="text" name="wali_asrama" value="{{ old('wali_asrama', $asrama->wali_asrama ?? '') }}"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
        placeholder="(Boleh kosong, bisa diisi nanti)">
</div>

        <div class="pt-4 flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Update
            </button>
            <a href="{{ route('asrama.index') }}" class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
