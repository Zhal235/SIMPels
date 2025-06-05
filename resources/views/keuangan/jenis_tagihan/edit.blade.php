@extends('layouts.admin')

@section('content')
    <div class="container px-6 mx-auto grid">
        <div class="flex justify-between items-center">
            <h2 class="my-6 text-2xl font-semibold text-gray-700">
                Edit Jenis Tagihan
            </h2>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <form action="{{ route('keuangan.jenis-tagihan.update', $jenisTagihan->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nama Tagihan</label>
                        <input type="text" name="nama" value="{{ old('nama', $jenisTagihan->nama) }}" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                        <textarea name="deskripsi" class="w-full border rounded px-3 py-2">{{ old('deskripsi', $jenisTagihan->deskripsi) }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
                        <select name="kategori_tagihan" class="w-full border rounded px-3 py-2" required>
                            <option value="Rutin" {{ old('kategori_tagihan', $jenisTagihan->kategori_tagihan) == 'Rutin' ? 'selected' : '' }}>Rutin</option>
                            <option value="Insidental" {{ old('kategori_tagihan', $jenisTagihan->kategori_tagihan) == 'Insidental' ? 'selected' : '' }}>Insidental</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Tipe Pembayaran</label>
                        <select name="is_bulanan" class="w-full border rounded px-3 py-2" required>
                            <option value="1" {{ old('is_bulanan', $jenisTagihan->is_bulanan) == 1 ? 'selected' : '' }}>Bulanan</option>
                            <option value="0" {{ old('is_bulanan', $jenisTagihan->is_bulanan) == 0 ? 'selected' : '' }}>Sekali Bayar</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nominal Default</label>
                        <input type="number" name="nominal" value="{{ old('nominal', $jenisTagihan->nominal) }}" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nominal Per Kelas?</label>
                        <select name="is_nominal_per_kelas" class="w-full border rounded px-3 py-2" required>
                            <option value="0" {{ old('is_nominal_per_kelas', $jenisTagihan->is_nominal_per_kelas) == 0 ? 'selected' : '' }}>Tidak</option>
                            <option value="1" {{ old('is_nominal_per_kelas', $jenisTagihan->is_nominal_per_kelas) == 1 ? 'selected' : '' }}>Ya</option>
                        </select>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('keuangan.jenis-tagihan.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Batal</a>
                        <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded shadow">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
