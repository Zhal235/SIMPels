@extends('layouts.admin')

@section('content')
<div class="max-w-lg mx-auto py-8">
    <h2 class="text-xl font-bold mb-4 text-blue-800">Import Data Asrama</h2>
    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">{{ session('error') }}</div>
    @endif
    <form action="{{ route('asrama.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label class="block mb-1 font-semibold">File Excel (.xlsx)</label>
            <input type="file" name="file" accept=".xlsx" required class="border rounded px-3 py-2 w-full">
        </div>
        <div class="flex gap-2 items-center">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Import</button>
            <a href="{{ route('asrama.template') }}" class="text-blue-700 underline">Download Template</a>
        </div>
    </form>
    <div class="mt-6 text-gray-600 text-sm">
        <p>Pastikan urutan kolom pada file Excel: <strong>Kode, Nama, Wali Asrama (opsional)</strong></p>
    </div>
</div>
@endsection