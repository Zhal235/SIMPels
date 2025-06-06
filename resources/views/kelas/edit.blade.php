
@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <h2 class="text-2xl font-semibold text-gray-700 mb-6">✏️ Edit Kelas</h2>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

   <form action="{{ route('kelas.update', $kelas) }}" method="POST">
    @csrf
    @method('PUT')

        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Kode Kelas</label>
                <input type="text" name="kode" value="{{ old('kode', $kelas->kode) }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Nama Kelas</label>
                <input type="text" name="nama" value="{{ old('nama', $kelas->nama) }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Tingkat</label>
                <select name="tingkat" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
                    <option value="7" {{ old('tingkat', $kelas->tingkat)=='7'?'selected':'' }}>7</option>
                    <option value="8" {{ old('tingkat', $kelas->tingkat)=='8'?'selected':'' }}>8</option>
                    <option value="9" {{ old('tingkat', $kelas->tingkat)=='9'?'selected':'' }}>9</option>
                    <option value="10" {{ old('tingkat', $kelas->tingkat)=='10'?'selected':'' }}>10</option>
                    <option value="11" {{ old('tingkat', $kelas->tingkat)=='11'?'selected':'' }}>11</option>
                    <option value="12" {{ old('tingkat', $kelas->tingkat)=='12'?'selected':'' }}>12</option>
                    <!-- tambah sesuai -->
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Wali Kelas</label>
                <input type="text" name="wali_kelas" value="{{ old('wali_kelas', $kelas->wali_kelas) }}" placeholder="Akan diisi ketika menu kepegawaian tersedia"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50" disabled />
                <p class="text-sm text-gray-500 mt-1">*Akan diisi ketika menu kepegawaian sudah tersedia</p>
            </div>
        </div>

        <div class="mt-6">
            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Update
            </button>
            <a href="{{ route('kelas.index') }}"
               class="ml-2 bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
               Batal
            </a>
        </div>
    </form>
</div>
@endsection
