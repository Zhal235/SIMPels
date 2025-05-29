@extends('layouts.admin')

@section('content')
<div x-data="{ openImportModal: false }">
    <div class="max-w-6xl mx-auto py-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 pb-4 border-b border-gray-200">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Manajemen Kelas
                </h1>
                <p class="text-sm text-gray-500 mt-1">Kelola data kelas, impor, dan pindah kelas.</p>
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <a href="{{ route('kelas.create') }}"
                   class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition duration-150 ease-in-out flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v4h4a1 1 0 110 2h-4v4a1 1 0 11-2 0v-4H5a1 1 0 110-2h4V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Tambah Kelas
                </a>
                <button @click="openImportModal = true"
                         class="w-full sm:w-auto bg-green-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 transition duration-150 ease-in-out flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Import Kelas
                </button>
                <a href="{{ route('kelas.pindah.form') }}"
                   class="w-full sm:w-auto bg-yellow-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50 transition duration-150 ease-in-out flex items-center justify-center">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M8 5a1 1 0 100 2h5.586l-1.293 1.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L13.586 5H8z" />
                        <path d="M12 15a1 1 0 100-2H6.414l1.293-1.293a1 1 0 10-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L6.414 15H12z" />
                    </svg>
                    Pindah Kelas
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded shadow">{{ session('success') }}</div>
        @endif

        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200 text-gray-700">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode Kelas</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Kelas</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tingkat</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Wali Kelas</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Jumlah Siswa</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($kelas as $index => $kls)
                        <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                            <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $kelas->firstItem() + $index }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $kls->kode ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">{{ $kls->nama ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $kls->tingkat ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $kls->wali_kelas ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center">{{ $kls->anggota_count ?? 0 }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                <div class="flex justify-center items-center gap-2">
                                    <a href="{{ route('kelas.edit', $kls->id) }}" class="p-2 rounded-full text-blue-600 hover:bg-blue-100 hover:text-blue-700 transition-colors duration-150" title="Edit Kelas">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                            <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('kelas.anggota.index', $kls->id) }}" class="p-2 rounded-full text-green-600 hover:bg-green-100 hover:text-green-700 transition-colors duration-150" title="Lihat Anggota Kelas">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('kelas.destroy', $kls->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus kelas bernama {{ $kls->nama }}? Data yang terhapus tidak dapat dikembalikan.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-full text-red-600 hover:bg-red-100 hover:text-red-700 transition-colors duration-150" title="Hapus Kelas">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 whitespace-nowrap">
                                <div class="text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2zM12 9v3m0 3h.01" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Data Kelas Tidak Ditemukan</h3>
                                    <p class="mt-1 text-sm text-gray-500">Belum ada data kelas yang tersedia. Silakan tambahkan kelas baru.</p>
                                    <div class="mt-6">
                                        <a href="{{ route('kelas.create') }}"
                                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v4h4a1 1 0 110 2h-4v4a1 1 0 11-2 0v-4H5a1 1 0 110-2h4V4a1 1 0 011-1z" clip-rule="evenodd" />
                                            </svg>
                                            Tambah Kelas Baru
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 rounded-b-xl shadow-md">
            <div class="flex-1 flex justify-between sm:hidden">
                @if ($kelas->onFirstPage())
                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-white cursor-default">
                        Sebelumnya
                    </span>
                @else
                    <a href="{{ $kelas->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Sebelumnya
                    </a>
                @endif

                @if ($kelas->hasMorePages())
                    <a href="{{ $kelas->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Berikutnya
                    </a>
                @else
                    <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-white cursor-default">
                        Berikutnya
                    </span>
                @endif
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Menampilkan
                        <span class="font-medium">{{ $kelas->firstItem() }}</span>
                        sampai
                        <span class="font-medium">{{ $kelas->lastItem() }}</span>
                        dari
                        <span class="font-medium">{{ $kelas->total() }}</span>
                        hasil
                    </p>
                </div>
                <div>
                    {{ $kelas->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Import Kelas -->
<div
    x-show="openImportModal"
    style="display: none"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
    <div @click.away="openImportModal = false"
         class="bg-white rounded-xl p-8 max-w-md w-full shadow-xl border">
        <h3 class="text-xl font-bold mb-4 text-blue-800">Import Data Kelas</h3>
        {{-- Tombol Download Template --}}
        <a href="{{ route('kelas.template') }}"
           class="inline-block mb-4 px-3 py-1 bg-blue-100 hover:bg-blue-200 rounded text-blue-800 text-sm font-semibold shadow transition">
            📄 Download Template
        </a>
        <form action="{{ route('kelas.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="file" name="file" required
                   class="block w-full border rounded p-2" accept=".xlsx,.xls,.csv">
            <div class="flex justify-end gap-2 mt-4">
                <button type="button"
                        @click="openImportModal = false"
                        class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-gray-700">
                    Batal
                </button>
                <button type="submit"
                        class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Import
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
