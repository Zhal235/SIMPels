@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Tambah Divisi/Bagian Baru</h1>
                <p class="mt-1 text-gray-600">Buat divisi atau bagian baru dalam struktur organisasi</p>
            </div>
            <a href="{{ route('kepegawaian.bidang.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md shadow-sm flex items-center space-x-2 transition-colors duration-200">
                <span class="material-icons-outlined text-lg">arrow_back</span>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Formulir Divisi/Bagian</h3>
            <p class="mt-1 text-sm text-gray-500">Isi semua kolom yang diperlukan</p>
        </div>

        <form action="{{ route('kepegawaian.bidang.store') }}" method="POST" class="px-4 py-5 sm:p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Bidang -->
                <div>
                    <label for="nama_bidang" class="block text-sm font-medium text-gray-700 mb-1">Nama Divisi/Bagian <span class="text-red-600">*</span></label>
                    <input type="text" name="nama_bidang" id="nama_bidang" class="form-input rounded-md shadow-sm w-full @error('nama_bidang') border-red-500 @enderror" value="{{ old('nama_bidang') }}" required>
                    @error('nama_bidang')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Contoh: Divisi Pendidikan, Bagian Keuangan, dll.</p>
                </div>

                <!-- Kode Bidang -->
                <div>
                    <label for="kode_bidang" class="block text-sm font-medium text-gray-700 mb-1">Kode Divisi/Bagian <span class="text-red-600">*</span></label>
                    <input type="text" name="kode_bidang" id="kode_bidang" class="form-input rounded-md shadow-sm w-full @error('kode_bidang') border-red-500 @enderror" value="{{ old('kode_bidang') }}" required maxlength="10">
                    @error('kode_bidang')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Contoh: DIV-PEND, BAG-KEU (maksimal 10 karakter)</p>
                </div>

                <!-- Penanggung Jawab -->
                <div>
                    <label for="naib_penanggung_jawab_id" class="block text-sm font-medium text-gray-700 mb-1">Penanggung Jawab</label>
                    <select name="naib_penanggung_jawab_id" id="naib_penanggung_jawab_id" class="form-select rounded-md shadow-sm w-full @error('naib_penanggung_jawab_id') border-red-500 @enderror">
                        <option value="">-- Pilih Penanggung Jawab --</option>
                        @foreach($pegawais as $pegawai)
                            <option value="{{ $pegawai->id }}" {{ old('naib_penanggung_jawab_id') == $pegawai->id ? 'selected' : '' }}>
                                {{ $pegawai->nama }} {{ $pegawai->jabatan ? '('.$pegawai->jabatan->nama_jabatan.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('naib_penanggung_jawab_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Urutan -->
                <div>
                    <label for="urutan" class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                    <input type="number" name="urutan" id="urutan" class="form-input rounded-md shadow-sm w-full @error('urutan') border-red-500 @enderror" value="{{ old('urutan', 1) }}" min="1">
                    @error('urutan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Urutan untuk menampilkan bidang (angka yang lebih kecil ditampilkan terlebih dahulu)</p>
                </div>
            </div>

            <!-- Deskripsi -->
            <div class="mt-6">
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="4" class="form-textarea rounded-md shadow-sm w-full @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">Deskripsi singkat tentang tugas dan tanggung jawab divisi/bagian</p>
            </div>

            <!-- Status -->
            <div class="mt-6">
                <fieldset>
                    <legend class="text-sm font-medium text-gray-700">Status</legend>
                    <div class="mt-2 space-y-2">
                        <div class="flex items-center">
                            <input id="status-active" name="status" type="radio" value="active" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500" {{ old('status', 'active') === 'active' ? 'checked' : '' }}>
                            <label for="status-active" class="ml-2 block text-sm text-gray-700">Aktif</label>
                        </div>
                        <div class="flex items-center">
                            <input id="status-inactive" name="status" type="radio" value="inactive" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500" {{ old('status') === 'inactive' ? 'checked' : '' }}>
                            <label for="status-inactive" class="ml-2 block text-sm text-gray-700">Non-aktif</label>
                        </div>
                    </div>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </fieldset>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 border-t border-gray-200 pt-6 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md shadow-sm transition-colors duration-200 flex items-center space-x-2">
                    <span class="material-icons-outlined text-lg">save</span>
                    <span>Simpan Divisi/Bagian</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
