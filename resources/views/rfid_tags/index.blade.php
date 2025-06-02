@extends('layouts.admin')

@section('content')
<div x-data="{ openFilter:false, openModal:false, selected:{id:null,nis:'',nama_santri:''} }"
     class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 pb-4 border-b border-gray-200">
    <div class="mb-4 sm:mb-0">
        <h1 class="text-3xl font-bold text-gray-800 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4a8 8 0 00-8 8c0 3.733 2.604 6.862 6.063 7.794" />
            </svg>
            Manajemen RFID Santri
        </h1>
        <p class="text-sm text-gray-500 mt-1">Kelola data RFID, filter, dan tetapkan RFID untuk santri.</p>
    </div>
    <div class="flex items-center gap-2 w-full sm:w-auto">
        <form id="formCari" method="GET" action="{{ route('rfid-tags.index') }}" class="relative w-full sm:w-64">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
            </div>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari Nama / NIS..."
                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm shadow-sm" />
        </form>
        <button @click="openFilter = true"
                class="w-full sm:w-auto bg-white text-gray-700 px-4 py-2 border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
            </svg>
            Filter
        </button>
    </div>
</div>

  <div x-show="openFilter" @click.away="openFilter=false" x-cloak x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white p-6 rounded-xl shadow-2xl max-w-md w-full transform transition-all" x-show="openFilter" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-800">Filter Data Santri</h3>
            <button @click="openFilter=false" class="text-gray-400 hover:text-gray-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form method="GET" action="{{ route('rfid-tags.index') }}" class="space-y-4">
            <div>
                <label for="filter_kelas" class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                <select id="filter_kelas" name="kelas" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ request('kelas') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="filter_jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                <select id="filter_jenis_kelamin" name="jenis_kelamin" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">Semua Jenis Kelamin</option>
                    <option value="L" {{ request('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ request('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            <div>
                <label for="filter_status" class="block text-sm font-medium text-gray-700 mb-1">Status Santri</label>
                <select id="filter_status" name="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="alumni" {{ request('status') == 'alumni' ? 'selected' : '' }}>Alumni</option>
                    <option value="cuti" {{ request('status') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                </select>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <button type="submit" class="w-full sm:w-auto flex-grow justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    Terapkan Filter
                </button>
                <a href="{{ route('rfid-tags.index') }}" class="w-full sm:w-auto flex-grow justify-center items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition duration-150 ease-in-out text-center">
                    Reset Filter
                </a>
            </div>
        </form>
    </div>
</div>

  <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 mt-2">
    <table class="min-w-full divide-y divide-gray-200 text-gray-700">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">NIS</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Santri</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kelas</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nomor RFID/UID</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">PIN</th>
                <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($santris as $i => $santri)
                <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                    <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $santris->firstItem() + $i }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $santri->nis }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">{{ $santri->nama_santri }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                        {{ $santri->kelasRelasi->pluck('nama')->join(', ') ?: '-' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $santri->rfidTag->tag_uid ?? '-' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $santri->rfidTag->pin ?? '-' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                        <button @click="openModal=true; selected.id={{$santri->id}}; selected.nis='{{$santri->nis}}'; selected.nama_santri='{{addslashes($santri->nama_santri)}}';"
                                class="p-2 rounded-full text-blue-600 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition-colors duration-150" title="Set RFID & PIN">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 2a1 1 0 00-1 1v1H3a1 1 0 000 2h1v1a1 1 0 001 1h10a1 1 0 001-1V6h1a1 1 0 100-2h-1V3a1 1 0 00-1-1H5zm0 4h10v10H5V6zm2 3a1 1 0 011-1h4a1 1 0 110 2H8a1 1 0 01-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 whitespace-nowrap">
                        <div class="text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4a8 8 0 00-8 8c0 3.733 2.604 6.862 6.063 7.794" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Data Santri Tidak Ditemukan</h3>
                            <p class="mt-1 text-sm text-gray-500">Tidak ada data santri yang cocok dengan filter atau pencarian Anda.</p>
                            <div class="mt-6">
                                <a href="{{ route('rfid-tags.index') }}"
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Reset Filter & Pencarian
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
        @if ($santris->onFirstPage())
            <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-white cursor-default">
                Sebelumnya
            </span>
        @else
            <a href="{{ $santris->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Sebelumnya
            </a>
        @endif

        @if ($santris->hasMorePages())
            <a href="{{ $santris->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
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
                <span class="font-medium">{{ $santris->firstItem() ?? 0 }}</span>
                sampai
                <span class="font-medium">{{ $santris->lastItem() ?? 0 }}</span>
                dari
                <span class="font-medium">{{ $santris->total() }}</span>
                hasil
            </p>
        </div>
        <div>
            {{ $santris->links('vendor.pagination.tailwind') }}
        </div>
    </div>
</div>

  <div x-show="openModal" @click.away="openModal=false" x-cloak x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white p-6 rounded-xl shadow-2xl w-full max-w-md transform transition-all" x-show="openModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-800">Set RFID untuk Santri</h3>
            <button @click="openModal=false" class="text-gray-400 hover:text-gray-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <p class="mb-4 text-sm text-gray-600">Santri: <strong x-text="selected.nis" class="text-gray-800"></strong> — <span x-text="selected.nama_santri" class="text-gray-800"></span></p>
        <form action="{{ route('rfid-tags.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="santri_id" :value="selected.id">
            <div>
                <label for="tag_uid" class="block text-sm font-medium text-gray-700 mb-1">Nomor RFID/UID</label>
                <input type="text" id="tag_uid" name="tag_uid" required autofocus
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
            </div>
            <div>
                <label for="pin" class="block text-sm font-medium text-gray-700 mb-1">PIN</label>
                <input type="text" id="pin" name="pin" required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
            </div>
            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <button type="submit" class="w-full sm:w-auto flex-grow justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    Simpan RFID
                </button>
                <button type="button" @click="openModal=false" class="w-full sm:w-auto flex-grow justify-center items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition duration-150 ease-in-out">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

</div>
@endsection
