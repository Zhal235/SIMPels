@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 pb-4 border-b border-gray-200">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h8m-8 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2" />
                </svg>
                Manajemen Tahun Ajaran
            </h1>
            <p class="text-sm text-gray-500 mt-1">Kelola tahun ajaran dan atur tahun ajaran aktif untuk referensi administrasi keuangan.</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('tahun-ajaran.create') }}" 
               class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out flex items-center justify-center gap-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Tambah Tahun Ajaran
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Active Academic Year Card -->
    @php
        $activeTahunAjaran = $tahunAjarans->where('is_active', true)->first();
    @endphp
    
    @if($activeTahunAjaran)
        <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-green-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Tahun Ajaran Aktif
                    </h3>
                    <p class="text-green-700 mt-1">
                        <span class="font-medium">{{ $activeTahunAjaran->nama_tahun_ajaran }}</span>
                        <span class="text-sm">({{ $activeTahunAjaran->tahun_mulai }}/{{ $activeTahunAjaran->tahun_selesai }})</span>
                    </p>
                    <p class="text-sm text-green-600 mt-1">
                        {{ $activeTahunAjaran->tanggal_mulai->format('d M Y') }} - {{ $activeTahunAjaran->tanggal_selesai->format('d M Y') }}
                    </p>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <svg class="w-2 h-2 mr-1 fill-current" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3"/>
                        </svg>
                        Aktif
                    </span>
                </div>
            </div>
        </div>
    @else
        <div class="mb-6 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <span class="font-medium">Peringatan:</span> Belum ada tahun ajaran yang aktif. Silakan aktifkan salah satu tahun ajaran.
            </div>
        </div>
    @endif

    <!-- Table -->
    <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Tahun Ajaran</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($tahunAjarans as $i => $tahunAjaran)
                    <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $tahunAjarans->firstItem() + $i }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $tahunAjaran->nama_tahun_ajaran }}</div>
                            @if($tahunAjaran->keterangan)
                                <div class="text-sm text-gray-500">{{ Str::limit($tahunAjaran->keterangan, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $tahunAjaran->tahun_mulai }}/{{ $tahunAjaran->tahun_selesai }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div>{{ $tahunAjaran->tanggal_mulai->format('d M Y') }}</div>
                            <div class="text-gray-500">{{ $tahunAjaran->tanggal_selesai->format('d M Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($tahunAjaran->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-2 h-2 mr-1 fill-current" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3"/>
                                    </svg>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Tidak Aktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-center space-x-2">
                                @if(!$tahunAjaran->is_active)
                                    <form action="{{ route('tahun-ajaran.set-active', $tahunAjaran) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="text-green-600 hover:text-green-900 transition-colors duration-150"
                                                title="Aktifkan"
                                                onclick="return confirm('Yakin ingin mengaktifkan tahun ajaran ini?')">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                                
                                <a href="{{ route('tahun-ajaran.edit', $tahunAjaran) }}" 
                                   class="text-blue-600 hover:text-blue-900 transition-colors duration-150"
                                   title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                </a>
                                
                                @if(!$tahunAjaran->is_active)
                                    <form action="{{ route('tahun-ajaran.destroy', $tahunAjaran) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-900 transition-colors duration-150"
                                                title="Hapus"
                                                onclick="return confirm('Yakin ingin menghapus tahun ajaran ini?')">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9zM4 5a2 2 0 012-2h8a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zM8 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm4 0a1 1 0 112 0v4a1 1 0 11-2 0V8z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 whitespace-nowrap">
                            <div class="text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h8m-8 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum Ada Tahun Ajaran</h3>
                                <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan tahun ajaran pertama.</p>
                                <div class="mt-6">
                                    <a href="{{ route('tahun-ajaran.create') }}"
                                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                        </svg>
                                        Tambah Tahun Ajaran
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($tahunAjarans->hasPages())
        <div class="mt-6">
            {{ $tahunAjarans->links('vendor.pagination.tailwind') }}
        </div>
    @endif
</div>
@endsection