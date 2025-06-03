@extends('layouts.admin')

@section('content')
<div class="container mx-auto p-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 pb-4 border-b border-gray-200">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Tambah Jenis Pembayaran
            </h1>
            <p class="text-sm text-gray-500 mt-1">Tambahkan jenis pembayaran baru ke sistem.</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('jenis-pembayaran.index') }}" class="w-full sm:w-auto bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition duration-150 ease-in-out flex items-center justify-center gap-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('jenis-pembayaran.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700">Nama Jenis Pembayaran <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" id="nama" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('nama') border-red-500 @enderror" value="{{ old('nama') }}" required>
                    @error('nama')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="kategori_pembayaran" class="block text-sm font-medium text-gray-700">Kategori Pembayaran <span class="text-red-500">*</span></label>
                    <select name="kategori_pembayaran" id="kategori_pembayaran" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('kategori_pembayaran') border-red-500 @enderror" required>
                        <option value="">Pilih Kategori</option>
                        <option value="Rutin" {{ old('kategori_pembayaran') == 'Rutin' ? 'selected' : '' }}>Pembayaran Rutin</option>
                        <option value="Insidental" {{ old('kategori_pembayaran') == 'Insidental' ? 'selected' : '' }}>Pembayaran Insidental</option>
                    </select>
                    @error('kategori_pembayaran')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="nominal_tagihan" class="block text-sm font-medium text-gray-700">Nominal Tagihan <span class="text-red-500">*</span></label>
                    <input type="number" name="nominal_tagihan" id="nominal_tagihan" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('nominal_tagihan') border-red-500 @enderror" value="{{ old('nominal_tagihan') }}" min="0" step="0.01" required>
                    @error('nominal_tagihan')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Simpan Jenis Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>
@endsection