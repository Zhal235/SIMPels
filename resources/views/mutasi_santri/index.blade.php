@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 pb-4 border-b border-gray-200">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                Riwayat Mutasi Santri
            </h1>
            <p class="text-sm text-gray-500 mt-1">Daftar semua santri yang telah melakukan mutasi.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200 text-gray-700">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Santri</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Alasan Mutasi</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tujuan Mutasi</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Mutasi</th>
                <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($mutasiList as $mutasi)
            <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">{{ $mutasi->nama }}</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $mutasi->alasan }}</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $mutasi->tujuan_mutasi }}</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm">{{ \Carbon\Carbon::parse($mutasi->tanggal_mutasi)->isoFormat('D MMMM YYYY') }}</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                    <form action="{{ route('mutasi_santri.batal', $mutasi->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin membatalkan mutasi untuk santri bernama {{ $mutasi->nama }}?');" class="inline">
                        @csrf
                        <button type="submit" class="p-2 rounded-full text-red-600 hover:bg-red-100 hover:text-red-700 transition-colors duration-150" title="Batalkan Mutasi">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-12 whitespace-nowrap">
                    <div class="text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Data Mutasi Tidak Ditemukan</h3>
                        <p class="mt-1 text-sm text-gray-500">Belum ada riwayat mutasi santri yang tercatat.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
    <div class="mt-6 bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 rounded-b-xl shadow-md">
        <div class="flex-1 flex justify-between sm:hidden">
            @if ($mutasiList->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-white cursor-default">
                    Sebelumnya
                </span>
            @else
                <a href="{{ $mutasiList->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Sebelumnya
                </a>
            @endif

            @if ($mutasiList->hasMorePages())
                <a href="{{ $mutasiList->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
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
                    <span class="font-medium">{{ $mutasiList->firstItem() }}</span>
                    sampai
                    <span class="font-medium">{{ $mutasiList->lastItem() }}</span>
                    dari
                    <span class="font-medium">{{ $mutasiList->total() }}</span>
                    hasil
                </p>
            </div>
            <div>
                {{ $mutasiList->links('vendor.pagination.tailwind') }}
            </div>
        </div>
    </div>

</div>
@endsection
