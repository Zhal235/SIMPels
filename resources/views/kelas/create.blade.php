// resources/views/kelas/create.blade.php
@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <h2 class="text-2xl font-semibold text-gray-700 mb-6">➕ Tambah Kelas</h2>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('kelas.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Kode Kelas</label>
                <input type="text" name="kode" value="{{ old('kode') }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Nama Kelas</label>
                <input type="text" name="nama" value="{{ old('nama') }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Tingkat</label>
                <select name="tingkat" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
                    <option value="">-- Pilih Tingkat --</option>
                    <option value="7" {{ old('tingkat')=='7'?'selected':'' }}>7</option>
                    <option value="8" {{ old('tingkat')=='8'?'selected':'' }}>8</option>
                    <option value="9" {{ old('tingkat')=='9'?'selected':'' }}>9</option>
                    <option value="10" {{ old('tingkat')=='10'?'selected':'' }}>10</option>
                    <option value="11" {{ old('tingkat')=='11'?'selected':'' }}>11</option>
                    <option value="12" {{ old('tingkat')=='12'?'selected':'' }}>12</option>
                    
                    <!-- tambahkan sesuai kebutuhan -->
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Wali Kelas</label>
                <select name="wali_kelas"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
                    <option value="">-- Pilih Guru --</option>
                    @foreach($waliList as $guru)
                        <option value="{{ $guru->id }}" {{ old('wali_kelas') == $guru->id ? 'selected' : '' }}>
                            {{ $guru->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-6">
            <button type="submit"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Simpan
            </button>
            <a href="{{ route('kelas.index') }}"
               class="ml-2 bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
               Batal
            </a>
        </div>
    </form>
</div>
@endsection
