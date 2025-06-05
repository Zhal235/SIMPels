@extends('layouts.admin')

@section('content')
<div class="container px-6 mx-auto grid">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">Tambah Jenis Tagihan</h2>
        <a href="{{ route('keuangan.jenis-tagihan.index') }}" class="text-blue-600 hover:underline">&larr; Kembali</a>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('keuangan.jenis-tagihan.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Tagihan</label>
                <input type="text" name="nama" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                <textarea name="deskripsi" class="w-full border rounded px-3 py-2"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
                <select name="kategori_tagihan" class="w-full border rounded px-3 py-2" required>
                    <option value="Rutin">Rutin</option>
                    <option value="Insidental">Insidental</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Tipe Pembayaran</label>
                <select name="is_bulanan" class="w-full border rounded px-3 py-2" required>
                    <option value="1">Bulanan</option>
                    <option value="0">Sekali Bayar</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nominal Default</label>
                <input type="number" name="nominal" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nominal Per Kelas?</label>
                <select name="is_nominal_per_kelas" class="w-full border rounded px-3 py-2" required>
                    <option value="0">Tidak</option>
                    <option value="1">Ya</option>
                </select>
            </div>
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded shadow">Simpan</button>
        </form>
    </div>
</div>
@endsection
