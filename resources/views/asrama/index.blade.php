@extends('layouts.admin')

@section('content')
<div x-data="{ openImportModal: false }" class="max-w-6xl mx-auto py-6">
    {{-- Header + tombol tambah --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 pb-4 border-b border-gray-200">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Manajemen Asrama
            </h1>
            <p class="text-sm text-gray-500 mt-1">Kelola data asrama, impor, dan pindah asrama.</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('asrama.create') }}"
               class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out flex items-center justify-center gap-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Tambah Asrama
            </a>
            <button @click="openImportModal = true"
                    class="w-full sm:w-auto bg-white text-blue-600 border border-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 transition duration-150 ease-in-out flex items-center justify-center gap-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                Import Asrama
            </button>
            <a href="{{ route('asrama.pindah.form') }}"
               class="w-full sm:w-auto bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition duration-150 ease-in-out flex items-center justify-center gap-2 shadow-sm">
                 <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                    <path d="M6.99 11L3 15l3.99 4v-3H14v-2H6.99v-3zM21 9l-3.99-4v3H10v2h7.01v3L21 9z"/>
                </svg>
                Pindah Asrama
            </a>
        </div>
    </div>

    {{-- Modal Import --}}
    <div x-show="openImportModal" style="display: none;" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40">
        <div @click.away="openImportModal = false" class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
            <button @click="openImportModal = false" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <h3 class="text-lg font-bold text-blue-800 mb-4">Import Data Asrama</h3>
            <div class="mb-4">
                <a href="{{ asset('templates/asrama_template.xlsx') }}" class="inline-flex items-center px-3 py-1 rounded bg-blue-100 text-blue-700 font-semibold text-sm hover:bg-blue-200 transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Download Template
                </a>
            </div>
            <form action="{{ route('asrama.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <input type="file" name="file" accept=".xlsx" required class="border rounded px-3 py-2 w-full" />
                </div>
                <div class="text-xs text-gray-600 mb-2">
                    Kolom: Kode, Nama, Wali Asrama
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="openImportModal = false" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Import</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto bg-white rounded-xl shadow-lg border border-gray-200 mt-2">
        <table class="min-w-full text-sm text-left text-gray-700">
            <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-600 sticky top-0 z-10">
                <tr>
                    <th class="px-4 py-3 text-center">No</th>
                    <th class="px-4 py-3 text-center">Kode Asrama</th>
                    <th class="px-4 py-3 text-left">Nama Asrama</th>
                    <th class="px-4 py-3 text-left">Wali Asrama</th>
                    <th class="px-4 py-3 text-center">Jumlah Santri</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($asrama as $no => $row)
                <tr>
                    <td class="px-4 py-3 text-center font-medium">{{ $asrama->firstItem() + $no }}</td>
                    <td class="px-4 py-3 text-center">{{ $row->kode }}</td>
                    <td class="px-4 py-3">{{ $row->nama }}</td>
                    <td class="px-4 py-3">{{ $row->wali_asrama ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">{{ $row->santris_count }}</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex justify-center items-center gap-2">
                        <a href="{{ route('asrama.edit', $row->id) }}" title="Edit Asrama"
                           class="p-2 rounded-full text-blue-600 hover:bg-blue-100 transition duration-150 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                            </svg>
                        </a>
                        <a href="{{ route('asrama.anggota.index', $row->id) }}" title="Lihat Anggota"
                           class="p-2 rounded-full text-green-600 hover:bg-green-100 transition duration-150 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                            </svg>
                        </a>
                        <form action="{{ route('asrama.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus asrama bernama ' + '{{ addslashes($row->nama) }}' + '?')" class="inline-block">
                            @csrf @method('DELETE')
                            <button type="submit" title="Hapus Asrama"
                                    class="p-2 rounded-full text-red-600 hover:bg-red-100 transition duration-150 ease-in-out">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                </svg>
                            </button>
                        </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-6 px-4 pb-4">
            {{ $asrama->links() }}
        </div>
    </div>
</div>
@endsection
