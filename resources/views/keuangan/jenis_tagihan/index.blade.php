@extends('layouts.admin')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="max-w-6xl mx-auto py-6">
    {{-- Header + Actions --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 pb-4 border-b border-gray-200">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-purple-600" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Jenis Tagihan
            </h1>
            <p class="text-sm text-gray-500 mt-1">Kelola jenis tagihan dan nominal pembayaran untuk santri.</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button onclick="openAutomationModal()"
               class="w-full sm:w-auto bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-150 ease-in-out flex items-center justify-center gap-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
                Otomatisasi Tagihan
            </button>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="mb-6">
        <nav class="flex space-x-8" aria-label="Tabs">
            <button id="tab-rutin" onclick="switchTab('rutin')" 
                    class="tab-button border-transparent text-gray-500 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-purple-600 focus:border-purple-500 transition duration-150 ease-in-out">
                <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Tagihan Rutin
            </button>
            <button id="tab-insidental" onclick="switchTab('insidental')" 
                    class="tab-button border-transparent text-gray-500 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-purple-600 focus:border-purple-500 transition duration-150 ease-in-out">
                <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                Tagihan Insidental
            </button>
        </nav>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <p class="font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- Tab Content: Tagihan Rutin --}}
    <div id="content-rutin" class="tab-content">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-700">Tagihan Rutin</h3>
            <button onclick="openCreateModal()"
               class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition duration-150 ease-in-out flex items-center gap-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Tambah Tagihan Rutin
            </button>
        </div>

        {{-- Data Table for Tagihan Rutin --}}
        <div class="overflow-x-auto bg-white rounded-xl shadow-lg border border-gray-200">
            <div class="w-full overflow-x-auto">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <table class="min-w-full text-sm text-left text-gray-700">
                        <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-600 sticky top-0 z-10">
                            <tr>
                                <th class="px-4 py-3 text-left">Nama Tagihan</th>
                                <th class="px-4 py-3 text-center">Nominal Default</th>
                                <th class="px-4 py-3 text-center">Tipe Pembayaran</th>
                                <th class="px-4 py-3 text-center">Buku Kas</th>
                                <th class="px-4 py-3 text-center">Jatuh Tempo</th>
                                <th class="px-4 py-3 text-center">Nominal per Kelas</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($jenisTagihans->where('kategori_tagihan', 'Rutin') as $tagihan)
                            <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                <td class="px-4 py-3">
                                    <div class="flex items-center text-sm">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $tagihan->nama }}</p>
                                            @if($tagihan->deskripsi)
                                                <p class="text-xs text-gray-600">{{ $tagihan->deskripsi }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center font-medium text-gray-900">
                                    Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full {{ $tagihan->is_bulanan ? 'bg-green-100 text-green-700' : 'bg-purple-100 text-purple-700' }}">
                                        {{ $tagihan->is_bulanan ? 'Bulanan' : 'Sekali Bayar' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($tagihan->bukuKas)
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">
                                        {{ $tagihan->bukuKas->nama_kas }}
                                    </span>
                                    @else
                                    <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                        Tanggal 10 setiap bulan
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($tagihan->is_nominal_per_kelas)
                                        <button onclick="window.location.href='{{ route('keuangan.jenis-tagihan.show-kelas', $tagihan->id) }}'"
                                            class="px-3 py-1 text-xs font-medium text-white bg-purple-600 border border-transparent rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-offset-2 transition-colors duration-150">
                                            Lihat Nominal
                                        </button>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center items-center gap-2">
                                        <form action="{{ route('keuangan.jenis-tagihan.generate', $tagihan->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" 
                                                    class="p-2 rounded-full text-green-600 hover:bg-green-100 transition duration-150 ease-in-out"
                                                    title="Generate Tagihan"
                                                    onclick="return confirm('Apakah Anda yakin ingin generate tagihan untuk {{ addslashes($tagihan->nama) }}? Ini akan membuat tagihan untuk semua santri yang belum memiliki tagihan ini.')">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path>
                                                </svg>
                                            </button>
                                        </form>
                                        <form action="{{ route('keuangan.jenis-tagihan.cancel', $tagihan->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" 
                                                    class="p-2 rounded-full text-orange-600 hover:bg-orange-100 transition duration-150 ease-in-out"
                                                    title="Batal Generate"
                                                    onclick="return confirm('Apakah Anda yakin ingin membatalkan tagihan untuk {{ addslashes($tagihan->nama) }}? Ini akan menghapus semua tagihan yang belum dibayar.')">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                </svg>
                                            </button>
                                        </form>
                                        <a href="#" onclick="openEditRutinModal({{ $tagihan->id }})" 
                                           class="p-2 rounded-full text-purple-600 hover:bg-purple-100 transition duration-150 ease-in-out"
                                           title="Edit">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('keuangan.jenis-tagihan.destroy', $tagihan->id) }}" method="POST" class="inline-block" onsubmit="return confirmDelete('{{ addslashes($tagihan->nama) }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="p-2 rounded-full text-red-600 hover:bg-red-100 transition duration-150 ease-in-out"
                                                    title="Hapus">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-10 text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="font-semibold text-lg">Belum ada Tagihan Rutin</p>
                                        <p class="text-sm">Belum ada tagihan rutin yang ditambahkan dalam sistem.</p>
                                        <button onclick="openCreateModal()" class="mt-4 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition duration-150 ease-in-out">
                                            Tambah Tagihan Rutin Pertama
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab Content: Tagihan Insidental --}}
    <div id="content-insidental" class="tab-content hidden">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-700">Tagihan Insidental</h3>
            <button onclick="openInsidentalModal()"
               class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition duration-150 ease-in-out flex items-center gap-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Tambah Tagihan Insidental
            </button>
        </div>

        {{-- Data Table for Tagihan Insidental --}}
        <div class="overflow-x-auto bg-white rounded-xl shadow-lg border border-gray-200">
            <div class="w-full overflow-x-auto">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <table class="min-w-full text-sm text-left text-gray-700">
                        <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-600 sticky top-0 z-10">
                            <tr>
                                <th class="px-4 py-3 text-left">Nama Tagihan</th>
                                <th class="px-4 py-3 text-center">Nominal Default</th>
                                <th class="px-4 py-3 text-center">Bulan Berlaku</th>
                                <th class="px-4 py-3 text-center">Buku Kas</th>
                                <th class="px-4 py-3 text-center">Jatuh Tempo</th>
                                <th class="px-4 py-3 text-center">Tahun Ajaran</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($jenisTagihans->where('kategori_tagihan', 'Insidental') as $tagihan)
                            <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                <td class="px-4 py-3">
                                    <div class="flex items-center text-sm">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $tagihan->nama }}</p>
                                            @if($tagihan->deskripsi)
                                                <p class="text-xs text-gray-600">{{ $tagihan->deskripsi }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center font-medium text-gray-900">
                                    Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($tagihan->bulan_pembayaran && is_array($tagihan->bulan_pembayaran))
                                        <div class="flex flex-wrap gap-1 justify-center">
                                            @foreach($tagihan->bulan_pembayaran as $bulan)
                                                <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-700">
                                                    {{ $tagihan->bulan_names[array_search($bulan, $tagihan->bulan_pembayaran_list)] ?? $bulan }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($tagihan->bukuKas)
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">
                                        {{ $tagihan->bukuKas->nama_kas }}
                                    </span>
                                    @else
                                    <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-700">
                                        @php
                                            $tanggal = $tagihan->tanggal_jatuh_tempo ?? 10;
                                            $bulanTambahan = $tagihan->bulan_jatuh_tempo ?? 0;
                                        @endphp
                                        <div class="flex flex-col">
                                            <span>Tanggal {{ $tanggal }}</span>
                                            @if($bulanTambahan > 0)
                                                <span class="text-xs mt-0.5">
                                                    @if($bulanTambahan == 1)
                                                        (bulan berikutnya)
                                                    @else
                                                        (+{{ $bulanTambahan }} bulan)
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($tagihan->tahunAjaran)
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700">
                                        {{ $tagihan->tahunAjaran->nama_tahun_ajaran }}
                                    </span>
                                    @else
                                    <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center items-center gap-2">
                                        <button onclick="previewInsidental({{ $tagihan->id }})" 
                                                class="p-2 rounded-full text-blue-600 hover:bg-blue-100 transition duration-150 ease-in-out"
                                                title="Preview Tagihan">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                        <button onclick="generateInsidental({{ $tagihan->id }})" 
                                                class="p-2 rounded-full text-green-600 hover:bg-green-100 transition duration-150 ease-in-out"
                                                title="Generate Tagihan">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                        <a href="#" onclick="openEditInsidentalModal({{ $tagihan->id }})" 
                                           class="p-2 rounded-full text-purple-600 hover:bg-purple-100 transition duration-150 ease-in-out"
                                           title="Edit">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('keuangan.jenis-tagihan.destroy', $tagihan->id) }}" method="POST" class="inline-block" onsubmit="return confirmDelete('{{ addslashes($tagihan->nama) }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="p-2 rounded-full text-red-600 hover:bg-red-100 transition duration-150 ease-in-out"
                                                    title="Hapus">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-10 text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                        <p class="font-semibold text-lg">Belum ada Tagihan Insidental</p>
                                        <p class="text-sm">Belum ada tagihan insidental yang ditambahkan dalam sistem.</p>
                                        <button onclick="openInsidentalModal()" class="mt-4 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition duration-150 ease-in-out">
                                            Tambah Tagihan Insidental Pertama
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal for Add Tagihan Rutin --}}
<div id="createModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Tambah Tagihan Rutin</h3>
                <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <form id="createForm" class="mt-4 space-y-4">
                @csrf
                <input type="hidden" name="kategori_tagihan" value="Rutin">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tagihan <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" id="nama" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                    <span class="text-red-500 text-xs hidden" id="rutin-error-nama"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"></textarea>
                    <span class="text-red-500 text-xs hidden" id="rutin-error-deskripsi"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Pembayaran <span class="text-red-500">*</span></label>
                    <select name="is_bulanan" id="is_bulanan" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <option value="">Pilih Tipe</option>
                        <option value="1">Bulanan</option>
                        <option value="0">Sekali Bayar</option>
                    </select>
                    <span class="text-red-500 text-xs hidden" id="rutin-error-is_bulanan"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Default <span class="text-red-500">*</span></label>
                    <input type="number" name="nominal" id="nominal" required min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                    <span class="text-red-500 text-xs hidden" id="rutin-error-nominal"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Per Kelas?</label>
                    <select name="is_nominal_per_kelas" id="is_nominal_per_kelas"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <option value="0">Tidak</option>
                        <option value="1">Ya</option>
                    </select>
                    <span class="text-red-500 text-xs hidden" id="rutin-error-is_nominal_per_kelas"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buku Kas <span class="text-red-500">*</span></label>
                    <select name="buku_kas_id" id="buku_kas_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <option value="">Pilih Buku Kas</option>
                        @foreach($bukuKasList as $bukuKas)
                        <option value="{{ $bukuKas->id }}">{{ $bukuKas->nama_kas }} ({{ $bukuKas->jenis_kas }})</option>
                        @endforeach
                    </select>
                    <span class="text-red-500 text-xs hidden" id="rutin-error-buku_kas_id"></span>
                </div>

                <div class="bg-blue-50 border-l-4 border-blue-400 p-3 mt-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Info:</strong> Untuk tagihan rutin, jatuh tempo otomatis diatur tanggal 10 setiap bulan.
                            </p>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end pt-4 space-x-2">
                <button onclick="closeCreateModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-150 ease-in-out">
                    Batal
                </button>
                <button onclick="submitRutinForm()" 
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition duration-150 ease-in-out flex items-center gap-2">
                    <svg class="w-4 h-4 hidden animate-spin" id="rutin-loading-spinner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span id="rutin-submit-text">Simpan</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal for Add Tagihan Insidental --}}
<div id="insidentalModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-5 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white mb-10">
        <div class="mt-3">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Tambah Tagihan Insidental</h3>
                <button onclick="closeInsidentalModal()" class="text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <form id="insidentalForm" class="mt-4 space-y-6">
                @csrf
                
                {{-- Basic Information --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tagihan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" id="insidental_nama" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        <span class="text-red-500 text-xs hidden" id="insidental-error-nama"></span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nominal <span class="text-red-500">*</span></label>
                        <input type="number" name="nominal" id="insidental_nominal" required min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        <span class="text-red-500 text-xs hidden" id="insidental-error-nominal"></span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" id="insidental_deskripsi" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"></textarea>
                    <span class="text-red-500 text-xs hidden" id="insidental-error-deskripsi"></span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buku Kas <span class="text-red-500">*</span></label>
                    <select name="buku_kas_id" id="insidental_buku_kas_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        <option value="">Pilih Buku Kas</option>
                        @foreach($bukuKasList as $bukuKas)
                        <option value="{{ $bukuKas->id }}">{{ $bukuKas->nama_kas }} ({{ $bukuKas->jenis_kas }})</option>
                        @endforeach
                    </select>
                    <span class="text-red-500 text-xs hidden" id="insidental-error-buku_kas_id"></span>
                </div>

                {{-- Bulan Pembayaran --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bulan Diberlakukan <span class="text-red-500">*</span></label>
                    @if($activeTahunAjaran)
                        <div class="mb-2 text-sm text-blue-600 bg-blue-50 p-2 rounded">
                            Tahun Ajaran: {{ $activeTahunAjaran->nama_tahun_ajaran }} ({{ Carbon\Carbon::parse($activeTahunAjaran->tanggal_mulai)->format('M Y') }} - {{ Carbon\Carbon::parse($activeTahunAjaran->tanggal_selesai)->format('M Y') }})
                        </div>
                    @endif
                    <div class="grid grid-cols-3 md:grid-cols-4 gap-2">
                        @foreach($academicYearMonths as $value => $name)
                        <label class="flex items-center">
                            <input type="checkbox" name="bulan_pembayaran[]" value="{{ $value }}" 
                                   class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                            <span class="ml-2 text-sm text-gray-700">{{ $name }}</span>
                        </label>
                        @endforeach
                    </div>
                    <span class="text-red-500 text-xs hidden" id="insidental-error-bulan_pembayaran"></span>
                </div>

                {{-- Jatuh Tempo --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jatuh Tempo</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Tanggal (1-31)</label>
                            <input type="number" name="tanggal_jatuh_tempo" id="insidental_tanggal_jatuh_tempo" required min="1" max="31" value="10"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                            <span class="text-red-500 text-xs hidden" id="insidental-error-tanggal_jatuh_tempo"></span>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Tambahan Bulan (opsional)</label>
                            <select name="bulan_jatuh_tempo" id="insidental_bulan_jatuh_tempo"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                                <option value="0" selected>Bulan yang sama</option>
                                <option value="1">Bulan berikutnya</option>
                                <option value="2">2 bulan kemudian</option>
                                <option value="3">3 bulan kemudian</option>
                                <option value="6">6 bulan kemudian</option>
                            </select>
                            <span class="text-red-500 text-xs hidden" id="insidental-error-bulan_jatuh_tempo"></span>
                        </div>
                    </div>
                </div>

                {{-- Target Tagihan --}}
                <div class="border-t pt-4">
                    <h4 class="text-md font-semibold text-gray-800 mb-3">Target Tagihan</h4>
                    
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="radio" name="target_type" value="all" id="target_all"
                                   class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                            <span class="ml-2 text-sm font-medium text-gray-700">Semua Santri</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="radio" name="target_type" value="kelas" id="target_kelas"
                                   class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                            <span class="ml-2 text-sm font-medium text-gray-700">Per Kelas</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="radio" name="target_type" value="santri" id="target_santri"
                                   class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                            <span class="ml-2 text-sm font-medium text-gray-700">Individu Santri</span>
                        </label>
                    </div>
                    <span class="text-red-500 text-xs hidden" id="insidental-error-target_type"></span>
                </div>

                {{-- Target Semua --}}
                <div id="target-semua" class="hidden">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-sm text-green-700 font-medium">Tagihan akan dibuat untuk semua santri aktif</p>
                        </div>
                    </div>
                </div>

                {{-- Target Kelas --}}
                <div id="target-kelas" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Kelas <span class="text-red-500">*</span></label>
                    <select id="target_kelas_select" multiple
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                            style="height: 120px;">
                        <!-- Kelas options will be loaded here -->
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Tahan Ctrl (Windows) atau Cmd (Mac) untuk memilih beberapa kelas</p>
                    <span class="text-red-500 text-xs hidden" id="insidental-error-target_kelas"></span>
                </div>

                {{-- Target Santri --}}
                <div id="target-santri" class="hidden">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cari dan Pilih Santri <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="text" 
                                       id="santri_search" 
                                       placeholder="Ketik nama santri untuk mencari..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                                <div id="santri_dropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg mt-1 max-h-60 overflow-auto hidden">
                                    <!-- Search results will appear here -->
                                </div>
                            </div>
                            
                            <!-- Selected santri tags -->
                            <div id="selected_santri_tags" class="mt-3 flex flex-wrap gap-2">
                                <!-- Selected santri tags will appear here -->
                            </div>
                            
                            <p class="text-xs text-gray-500 mt-2">Ketik nama santri untuk mencari, lalu klik untuk memilih. Santri yang dipilih akan muncul sebagai tag di atas.</p>
                        </div>
                    </div>
                    <span class="text-red-500 text-xs hidden" id="insidental-error-target_santri"></span>
                </div>
            </form>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end pt-4 space-x-2">
                <button onclick="closeInsidentalModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-150 ease-in-out">
                    Batal
                </button>
                <button onclick="submitInsidentalForm()" 
                        class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition duration-150 ease-in-out flex items-center gap-2">
                    <svg class="w-4 h-4 hidden animate-spin" id="insidental-loading-spinner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span id="insidental-submit-text">Simpan</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal for Edit Tagihan Rutin --}}
<div id="editRutinModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Edit Tagihan Rutin</h3>
                <button onclick="closeEditRutinModal()" class="text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <form id="editRutinForm" class="mt-4 space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_rutin_id">
                <input type="hidden" name="kategori_tagihan" value="Rutin">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tagihan <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" id="edit_rutin_nama" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                    <span class="text-red-500 text-xs hidden" id="edit-rutin-error-nama"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" id="edit_rutin_deskripsi" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"></textarea>
                    <span class="text-red-500 text-xs hidden" id="edit-rutin-error-deskripsi"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Pembayaran <span class="text-red-500">*</span></label>
                    <select name="is_bulanan" id="edit_rutin_is_bulanan" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <option value="">Pilih Tipe</option>
                        <option value="1">Bulanan</option>
                        <option value="0">Sekali Bayar</option>
                    </select>
                    <span class="text-red-500 text-xs hidden" id="edit-rutin-error-is_bulanan"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Default <span class="text-red-500">*</span></label>
                    <input type="number" name="nominal" id="edit_rutin_nominal" required min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                    <span class="text-red-500 text-xs hidden" id="edit-rutin-error-nominal"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Per Kelas?</label>
                    <select name="is_nominal_per_kelas" id="edit_rutin_is_nominal_per_kelas"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <option value="0">Tidak</option>
                        <option value="1">Ya</option>
                    </select>
                    <span class="text-red-500 text-xs hidden" id="edit-rutin-error-is_nominal_per_kelas"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buku Kas <span class="text-red-500">*</span></label>
                    <select name="buku_kas_id" id="edit_rutin_buku_kas_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <option value="">Pilih Buku Kas</option>
                        @foreach($bukuKasList as $bukuKas)
                        <option value="{{ $bukuKas->id }}">{{ $bukuKas->nama_kas }} ({{ $bukuKas->jenis_kas }})</option>
                        @endforeach
                    </select>
                    <span class="text-red-500 text-xs hidden" id="edit-rutin-error-buku_kas_id"></span>
                </div>

                <div class="bg-blue-50 border-l-4 border-blue-400 p-3 mt-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Info:</strong> Untuk tagihan rutin, jatuh tempo otomatis diatur tanggal 10 setiap bulan.
                            </p>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end pt-4 space-x-2">
                <button onclick="closeEditRutinModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-150 ease-in-out">
                    Batal
                </button>
                <button onclick="submitEditRutinForm()" 
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition duration-150 ease-in-out flex items-center gap-2">
                    <svg class="w-4 h-4 hidden animate-spin" id="edit-rutin-loading-spinner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span id="edit-rutin-submit-text">Simpan Perubahan</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal for Edit Tagihan Insidental --}}
<div id="editInsidentalModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-5 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white mb-10">
        <div class="mt-3">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Edit Tagihan Insidental</h3>
                <button onclick="closeEditInsidentalModal()" class="text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <form id="editInsidentalForm" class="mt-4 space-y-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_insidental_id">
                <input type="hidden" name="kategori_tagihan" value="Insidental">
                
                {{-- Basic Information --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tagihan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" id="edit_insidental_nama" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        <span class="text-red-500 text-xs hidden" id="edit-insidental-error-nama"></span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nominal <span class="text-red-500">*</span></label>
                        <input type="number" name="nominal" id="edit_insidental_nominal" required min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        <span class="text-red-500 text-xs hidden" id="edit-insidental-error-nominal"></span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" id="edit_insidental_deskripsi" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"></textarea>
                    <span class="text-red-500 text-xs hidden" id="edit-insidental-error-deskripsi"></span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buku Kas <span class="text-red-500">*</span></label>
                    <select name="buku_kas_id" id="edit_insidental_buku_kas_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        <option value="">Pilih Buku Kas</option>
                        @foreach($bukuKasList as $bukuKas)
                        <option value="{{ $bukuKas->id }}">{{ $bukuKas->nama_kas }} ({{ $bukuKas->jenis_kas }})</option>
                        @endforeach
                    </select>
                    <span class="text-red-500 text-xs hidden" id="edit-insidental-error-buku_kas_id"></span>
                </div>

                {{-- Bulan Pembayaran --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bulan Diberlakukan <span class="text-red-500">*</span></label>
                    @if($activeTahunAjaran)
                        <div class="mb-2 text-sm text-blue-600 bg-blue-50 p-2 rounded">
                            Tahun Ajaran: {{ $activeTahunAjaran->nama_tahun_ajaran }} ({{ Carbon\Carbon::parse($activeTahunAjaran->tanggal_mulai)->format('M Y') }} - {{ Carbon\Carbon::parse($activeTahunAjaran->tanggal_selesai)->format('M Y') }})
                        </div>
                    @endif
                    <div class="grid grid-cols-3 md:grid-cols-4 gap-2" id="edit-bulan-pembayaran-container">
                        @foreach($academicYearMonths as $value => $name)
                        <label class="flex items-center">
                            <input type="checkbox" name="bulan_pembayaran[]" value="{{ $value }}" 
                                   class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                            <span class="ml-2 text-sm text-gray-700">{{ $name }}</span>
                        </label>
                        @endforeach
                    </div>
                    <span class="text-red-500 text-xs hidden" id="edit-insidental-error-bulan_pembayaran"></span>
                </div>

                {{-- Jatuh Tempo --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jatuh Tempo</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Tanggal (1-31)</label>
                            <input type="number" name="tanggal_jatuh_tempo" id="edit_insidental_tanggal_jatuh_tempo" required min="1" max="31" value="10"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                            <span class="text-red-500 text-xs hidden" id="edit-insidental-error-tanggal_jatuh_tempo"></span>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Tambahan Bulan (opsional)</label>
                            <select name="bulan_jatuh_tempo" id="edit_insidental_bulan_jatuh_tempo"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                                <option value="0" selected>Bulan yang sama</option>
                                <option value="1">Bulan berikutnya</option>
                                <option value="2">2 bulan kemudian</option>
                                <option value="3">3 bulan kemudian</option>
                                <option value="6">6 bulan kemudian</option>
                            </select>
                            <span class="text-red-500 text-xs hidden" id="edit-insidental-error-bulan_jatuh_tempo"></span>
                        </div>
                    </div>
                </div>

                {{-- Target Tagihan --}}
                <div class="border-t pt-4">
                    <h4 class="text-md font-semibold text-gray-800 mb-3">Target Tagihan</h4>
                    
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="radio" name="target_type" value="all" id="edit_target_all"
                                   class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                            <span class="ml-2 text-sm font-medium text-gray-700">Semua Santri</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="radio" name="target_type" value="kelas" id="edit_target_kelas"
                                   class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                            <span class="ml-2 text-sm font-medium text-gray-700">Per Kelas</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="radio" name="target_type" value="santri" id="edit_target_santri"
                                   class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                            <span class="ml-2 text-sm font-medium text-gray-700">Individu Santri</span>
                        </label>
                    </div>
                    <span class="text-red-500 text-xs hidden" id="edit-insidental-error-target_type"></span>
                </div>

                {{-- Target Selection Options --}}
                <div id="edit-target-selection" class="hidden mt-4">
                    {{-- Per Kelas --}}
                    <div id="edit-kelas-selection" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Kelas</label>
                        <select multiple name="kelas_ids[]" id="edit_kelas_ids" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm h-32">                        @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}">{{ $kelas->tingkat }} - {{ $kelas->nama }}</option>
                        @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Tahan Ctrl/Cmd untuk memilih beberapa kelas</p>
                        <span class="text-red-500 text-xs hidden" id="edit-insidental-error-kelas_ids"></span>
                    </div>

                    {{-- Individu Santri --}}
                    <div id="edit-santri-selection" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Santri</label>
                        <div class="relative">
                            <input type="text" id="edit_santri_search" placeholder="Ketik nama santri untuk mencari..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                            <div id="edit-santri-dropdown" class="hidden absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-48 overflow-y-auto mt-1">
                                <!-- Search results will be populated here -->
                            </div>
                        </div>
                        
                        {{-- Selected Santri Tags --}}
                        <div id="edit-selected-santri-tags" class="mt-3 flex flex-wrap gap-2">
                            <!-- Selected santri tags will appear here -->
                        </div>
                        
                        {{-- Hidden inputs for selected santri --}}
                        <div id="edit-santri-hidden-inputs">
                            <!-- Hidden inputs will be added here dynamically -->
                        </div>
                        
                        <span class="text-red-500 text-xs hidden" id="edit-insidental-error-santri_ids"></span>
                    </div>
                </div>
            </form>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end pt-4 space-x-2">
                <button onclick="closeEditInsidentalModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-150 ease-in-out">
                    Batal
                </button>
                <button onclick="submitEditInsidentalForm()" 
                        class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition duration-150 ease-in-out flex items-center gap-2">
                    <svg class="w-4 h-4 hidden animate-spin" id="edit-insidental-loading-spinner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span id="edit-insidental-submit-text">Simpan Perubahan</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Otomatisasi Tagihan -->
<div id="automationModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    Otomatisasi Tagihan Rutin
                </h3>
                <button onclick="closeAutomationModal()" class="text-green-100 hover:text-white transition duration-150 ease-in-out">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="px-6 py-6 max-h-[70vh] overflow-y-auto">
                <!-- Informasi -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Otomatisasi Tagihan Rutin:</strong> Fitur ini akan menyalin semua tagihan rutin dari tahun ajaran sebelumnya ke tahun ajaran yang aktif saat ini. Hanya berlaku untuk santri yang masih aktif.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Otomatisasi -->
                <form id="automationForm" onsubmit="submitAutomation(event)">
                    @csrf
                    
                    <!-- Pilihan Tahun Ajaran -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="tahun_ajaran_asal" class="block text-sm font-medium text-gray-700 mb-2">
                                Tahun Ajaran Asal
                            </label>
                            <select id="tahun_ajaran_asal" name="tahun_ajaran_asal" 
                                    class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" required>
                                <option value="">Pilih Tahun Ajaran Asal</option>
                                @foreach($tahunAjarans ?? [] as $ta)
                                    @if(!$ta->is_active)
                                    <option value="{{ $ta->id }}">{{ $ta->nama_tahun_ajaran }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="tahun_ajaran_tujuan" class="block text-sm font-medium text-gray-700 mb-2">
                                Tahun Ajaran Tujuan
                            </label>
                            <select id="tahun_ajaran_tujuan" name="tahun_ajaran_tujuan" 
                                    class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 bg-gray-100" readonly>
                                @foreach($tahunAjarans ?? [] as $ta)
                                    @if($ta->is_active)
                                    <option value="{{ $ta->id }}" selected>{{ $ta->nama_tahun_ajaran }} (Aktif)</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Filter Kategori -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Filter Kategori Tagihan
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="kategori_tagihan[]" value="Rutin" 
                                       class="rounded border-gray-300 text-green-600 focus:ring-green-500" checked>
                                <span class="ml-2 text-sm text-gray-700">Tagihan Rutin</span>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Hanya tagihan rutin yang akan disalin secara otomatis</p>
                    </div>

                    <!-- Opsi Tambahan -->
                    <div class="mb-6">

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Opsi Tambahan
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="replace_existing" value="1" 
                                       class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <span class="ml-2 text-sm text-gray-700">Ganti tagihan yang sudah ada</span>
                            </label>
                            <p class="text-xs text-gray-500 ml-6">Jika dicentang, tagihan yang sudah ada akan diganti dengan yang baru</p>
                        </div>
                    </div>

                    <!-- Preview Section -->
                    <div id="previewSection" class="hidden mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3">Preview Data yang akan Disalin</h4>
                        <div id="previewContent" class="bg-gray-50 rounded-lg p-4">
                            <!-- Preview content will be loaded here -->
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-between items-center pt-4">
                        <button type="button" onclick="previewAutomation()" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Preview
                        </button>
                        
                        <div class="flex gap-2">
                            <button type="button" onclick="closeAutomationModal()" 
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-150 ease-in-out">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-150 ease-in-out flex items-center gap-2">
                                <svg class="w-4 h-4 hidden animate-spin" id="automation-loading-spinner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <span id="automation-submit-text">Jalankan Otomatisasi</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Tab switching functionality
function switchTab(tabName) {
    // Remove active class from all tabs and hide all content
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-purple-500', 'text-purple-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Show active tab and content
    const activeTab = document.getElementById(`tab-${tabName}`);
    const activeContent = document.getElementById(`content-${tabName}`);
    
    if (activeTab && activeContent) {
        activeTab.classList.remove('border-transparent', 'text-gray-500');
        activeTab.classList.add('border-purple-500', 'text-purple-600');
        activeContent.classList.remove('hidden');
    }
}

// Initialize with "rutin" tab active on page load
document.addEventListener('DOMContentLoaded', function() {
    switchTab('rutin');
});

// Add event listeners when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners for target type radio buttons
    const targetRadios = document.querySelectorAll('#insidentalModal input[name="target_type"]');
    targetRadios.forEach(radio => {
        radio.addEventListener('change', handleTargetChange);
    });
    
    // Add event listeners for edit modal target type radio buttons
    const editTargetRadios = document.querySelectorAll('#editInsidentalModal input[name="target_type"]');
    editTargetRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            handleEditTargetTypeChange(this.value);
        });
    });
});

function handleTargetChange() {
    const selectedTarget = document.querySelector('#insidentalModal input[name="target_type"]:checked');
    if (!selectedTarget) return;
    
    const targetSelection = document.getElementById('target-selection');
    const allTargets = document.getElementById('target-semua');
    const kelasTargets = document.getElementById('target-kelas');
    const santriTargets = document.getElementById('target-santri');
    
    // Hide all sections first
    if (targetSelection) targetSelection.classList.add('hidden');
    if (allTargets) allTargets.classList.add('hidden');
    if (kelasTargets) kelasTargets.classList.add('hidden');
    if (santriTargets) santriTargets.classList.add('hidden');
    
    // Show appropriate section based on selection
    if (selectedTarget.value === 'semua') {
        if (targetSelection) targetSelection.classList.remove('hidden');
        if (allTargets) allTargets.classList.remove('hidden');
    } else if (selectedTarget.value === 'kelas') {
        if (targetSelection) targetSelection.classList.remove('hidden');
        if (kelasTargets) kelasTargets.classList.remove('hidden');
    } else if (selectedTarget.value === 'santri') {
        if (targetSelection) targetSelection.classList.remove('hidden');
        if (santriTargets) santriTargets.classList.remove('hidden');
        // Initialize santri search
        initializeSantriSearch();
    }
}

function handleEditTargetTypeChange(targetType) {
    const targetSelection = document.getElementById('edit-target-selection');
    const kelasSelection = document.getElementById('edit-kelas-selection');
    const santriSelection = document.getElementById('edit-santri-selection');
    
    // Hide all sections first
    if (targetSelection) targetSelection.classList.add('hidden');
    if (kelasSelection) kelasSelection.classList.add('hidden');
    if (santriSelection) santriSelection.classList.add('hidden');
    
    // Show appropriate section based on selection
    if (targetType === 'kelas') {
        if (targetSelection) targetSelection.classList.remove('hidden');
        if (kelasSelection) kelasSelection.classList.remove('hidden');
    } else if (targetType === 'santri') {
        if (targetSelection) targetSelection.classList.remove('hidden');
        if (santriSelection) santriSelection.classList.remove('hidden');
        // Initialize santri search for edit modal
        initializeEditSantriSearch();
    }
}

// Modal functions for Tagihan Rutin
function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
    clearForm();
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
    clearForm();
}

function clearForm() {
    const form = document.getElementById('createForm');
    if (form) {
        form.reset();
        // Clear error messages
        const errorElements = form.querySelectorAll('.text-red-500');
        errorElements.forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });
    }
}

// Modal functions for Edit Tagihan Rutin
function openEditRutinModal(id) {
    console.log('Opening edit rutin modal for ID:', id);
    
    // Show loading state
    const editModal = document.getElementById('editRutinModal');
    if (editModal) {
        editModal.classList.remove('hidden');
    }
    
    // Fetch data for the selected jenis tagihan
    fetch(`/keuangan/jenis-tagihan/${id}/edit`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response URL:', response.url);
        if (!response.ok) {
            return response.text().then(text => {
                console.error('Response text:', text);
                throw new Error(`HTTP error! status: ${response.status} - ${text}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            console.log('Rutin tagihan data:', data.jenisTagihan);
            
            // Populate form fields for rutin
            document.getElementById('edit_rutin_id').value = data.jenisTagihan.id;
            document.getElementById('edit_rutin_nama').value = data.jenisTagihan.nama;
            document.getElementById('edit_rutin_deskripsi').value = data.jenisTagihan.deskripsi || '';
            document.getElementById('edit_rutin_is_bulanan').value = data.jenisTagihan.is_bulanan;
            document.getElementById('edit_rutin_nominal').value = data.jenisTagihan.nominal;
            document.getElementById('edit_rutin_is_nominal_per_kelas').value = data.jenisTagihan.is_nominal_per_kelas;
            document.getElementById('edit_rutin_buku_kas_id').value = data.jenisTagihan.buku_kas_id || '';
            
            // Show modal
            document.getElementById('editRutinModal').classList.remove('hidden');
        } else {
            console.error('Failed response:', data);
            closeEditRutinModal();
            alert('Gagal memuat data untuk diedit: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        closeEditRutinModal();
        alert('Terjadi kesalahan saat memuat data: ' + error.message);
    });
}

function closeEditRutinModal() {
    document.getElementById('editRutinModal').classList.add('hidden');
    clearEditRutinForm();
}

function clearEditRutinForm() {
    const form = document.getElementById('editRutinForm');
    if (form) {
        form.reset();
        // Clear error messages
        const errorElements = form.querySelectorAll('.text-red-500');
        errorElements.forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });
    }
}

function submitEditRutinForm() {
    const submitBtn = document.querySelector('#editRutinModal button[onclick="submitEditRutinForm()"]');
    const loadingSpinner = document.getElementById('edit-rutin-loading-spinner');
    const submitText = document.getElementById('edit-rutin-submit-text');
    
    // Show loading state
    if (loadingSpinner) loadingSpinner.classList.remove('hidden');
    if (submitText) submitText.textContent = 'Menyimpan...';
    if (submitBtn) submitBtn.disabled = true;
    
    const form = document.getElementById('editRutinForm');
    const formData = new FormData(form);
    const id = document.getElementById('edit_rutin_id').value;
    
    // Convert FormData to regular object for JSON
    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
    });
    
    fetch(`/keuangan/jenis-tagihan/${id}`, {
        method: 'PUT',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeEditRutinModal();
            alert('Tagihan rutin berhasil diperbarui!');
            location.reload(); // Refresh to show updated data
        } else {
            if (data.errors) {
                // Display validation errors
                Object.keys(data.errors).forEach(field => {
                    const errorElement = document.getElementById(`edit-rutin-error-${field}`);
                    if (errorElement) {
                        errorElement.textContent = data.errors[field][0];
                        errorElement.classList.remove('hidden');
                    }
                });
            } else {
                alert('Gagal memperbarui tagihan: ' + (data.message || 'Unknown error'));
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan data: ' + error.message);
    })
    .finally(() => {
        // Hide loading state
        if (loadingSpinner) loadingSpinner.classList.add('hidden');
        if (submitText) submitText.textContent = 'Simpan Perubahan';
        if (submitBtn) submitBtn.disabled = false;
    });
}

// Modal functions for Tagihan Insidental
function openInsidentalModal() {
    document.getElementById('insidentalModal').classList.remove('hidden');
    clearInsidentalForm();
    
    // Initialize santri search system
    initializeSantriSearch();
}

function closeInsidentalModal() {
    document.getElementById('insidentalModal').classList.add('hidden');
    clearInsidentalForm();
}

function clearInsidentalForm() {
    const form = document.getElementById('insidentalForm');
    if (form) {
        form.reset();
        // Clear error messages
        const errorElements = form.querySelectorAll('.text-red-500');
        errorElements.forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });
        // Uncheck all bulan pembayaran checkboxes
        const checkboxes = document.querySelectorAll('input[name="bulan_pembayaran[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        // Clear target selection
        const targetSelection = document.getElementById('target-selection');
        const allTargets = document.getElementById('target-semua');
        const kelasTargets = document.getElementById('target-kelas');
        const santriTargets = document.getElementById('target-santri');
        
        if (targetSelection) targetSelection.classList.add('hidden');
        if (allTargets) allTargets.classList.add('hidden');
        if (kelasTargets) kelasTargets.classList.add('hidden');
        if (santriTargets) santriTargets.classList.add('hidden');
        
        // Clear selected santri tags and hidden inputs
        const tagsContainer = document.getElementById('selected-santri-tags');
        const hiddenInputsContainer = document.getElementById('santri-hidden-inputs');
        if (tagsContainer) tagsContainer.innerHTML = '';
        if (hiddenInputsContainer) hiddenInputsContainer.innerHTML = '';
        
        // Clear santri search
        const santriSearch = document.getElementById('santri_search');
        const santriDropdown = document.getElementById('santri-dropdown');
        if (santriSearch) santriSearch.value = '';
        if (santriDropdown) santriDropdown.classList.add('hidden');
    }
}

function initializeSantriSearch() {
    const searchInput = document.getElementById('santri_search');
    const dropdown = document.getElementById('santri_dropdown');
    
    if (!searchInput || !dropdown) return;
    
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            dropdown.classList.add('hidden');
            return;
        }
        
        searchTimeout = setTimeout(() => {
            searchSantri(query);
        }, 300);
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
}

function searchSantri(query) {
    const dropdown = document.getElementById('santri_dropdown');
    if (!dropdown) return;
    
    dropdown.innerHTML = '<div class="p-2 text-gray-500">Mencari...</div>';
    dropdown.classList.remove('hidden');
    
    fetch(`/keuangan/jenis-tagihan/search-santri?q=${encodeURIComponent(query)}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.santri) {
            dropdown.innerHTML = '';
            
            if (data.santri.length === 0) {
                dropdown.innerHTML = '<div class="p-2 text-gray-500">Tidak ada santri ditemukan</div>';
            } else {
                data.santri.forEach(santri => {
                    // Check if santri is already selected
                    const existingTag = document.querySelector(`#selected_santri_tags [data-santri-id="${santri.id}"]`);
                    if (existingTag) return; // Skip if already selected
                    
                    const item = document.createElement('div');
                    item.className = 'p-2 hover:bg-gray-100 cursor-pointer border-b border-gray-200 last:border-b-0';
                    item.innerHTML = `
                        <div class="text-sm font-medium text-gray-900">${santri.nama}</div>
                        <div class="text-xs text-gray-500">${santri.kelas ? santri.kelas.nama : 'Tidak ada kelas'}</div>
                    `;
                    
                    item.addEventListener('click', () => {
                        addSelectedSantri(santri);
                        dropdown.classList.add('hidden');
                        document.getElementById('santri_search').value = '';
                    });
                    
                    dropdown.appendChild(item);
                });
            }
        } else {
            dropdown.innerHTML = '<div class="p-2 text-red-500">Gagal memuat data santri</div>';
        }
    })
    .catch(error => {
        console.error('Error searching santri:', error);
        dropdown.innerHTML = '<div class="p-2 text-red-500">Terjadi kesalahan</div>';
    });
}

function addSelectedSantri(santri) {
    const tagsContainer = document.getElementById('selected_santri_tags');
    
    if (!tagsContainer) return;
    
    // Check if santri is already selected
    const existingTag = tagsContainer.querySelector(`[data-santri-id="${santri.id}"]`);
    if (existingTag) return;
    
    // Create tag element
    const tag = document.createElement('div');
    tag.className = 'bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-sm flex items-center gap-2';
    tag.setAttribute('data-santri-id', santri.id);
    tag.innerHTML = `
        <span>${santri.nama}</span>
        <input type="hidden" name="target_santri[]" value="${santri.id}">
        <button type="button" onclick="removeSelectedSantri(${santri.id})" class="text-orange-600 hover:text-orange-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    
    // Add to container
    tagsContainer.appendChild(tag);
}

function removeSelectedSantri(santriId) {
    const tag = document.querySelector(`#selected_santri_tags [data-santri-id="${santriId}"]`);
    if (tag) tag.remove();
}

// Edit Insidental Modal Functions
function openEditInsidentalModal(id) {
    console.log('Opening edit insidental modal for ID:', id);
    
    // Show loading state
    const editModal = document.getElementById('editInsidentalModal');
    if (editModal) {
        editModal.classList.remove('hidden');
    }
    
    // Fetch data for the selected jenis tagihan
    fetch(`/keuangan/jenis-tagihan/${id}/edit`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        console.log('Edit Insidental Response status:', response.status);
        console.log('Edit Insidental Response URL:', response.url);
        if (!response.ok) {
            return response.text().then(text => {
                console.error('Edit Insidental Response text:', text);
                throw new Error(`HTTP error! status: ${response.status} - ${text}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Edit Insidental Response data:', data);
        if (data.success) {
            console.log('Insidental tagihan data:', data.jenisTagihan);
            
            // Populate form fields for insidental
            document.getElementById('edit_insidental_id').value = data.jenisTagihan.id;
            document.getElementById('edit_insidental_nama').value = data.jenisTagihan.nama;
            document.getElementById('edit_insidental_deskripsi').value = data.jenisTagihan.deskripsi || '';
            document.getElementById('edit_insidental_nominal').value = data.jenisTagihan.nominal;
            document.getElementById('edit_insidental_tanggal_jatuh_tempo').value = data.jenisTagihan.tanggal_jatuh_tempo;
            document.getElementById('edit_insidental_bulan_jatuh_tempo').value = data.jenisTagihan.bulan_jatuh_tempo || 0;
            
            // Set target type
            const targetRadios = document.querySelectorAll('#editInsidentalModal input[name="target_type"]');
            targetRadios.forEach(radio => {
                radio.checked = radio.value === data.jenisTagihan.target_type;
            });
            
            // Handle target-specific selections
            if (data.jenisTagihan.target_type === 'kelas') {
                // Populate kelas selection
                const kelasSelect = document.getElementById('edit_kelas_ids');
                if (kelasSelect && data.jenisTagihan.kelas_ids) {
                    Array.from(kelasSelect.options).forEach(option => {
                        option.selected = data.jenisTagihan.kelas_ids.includes(parseInt(option.value));
                    });
                }
            } else if (data.jenisTagihan.target_type === 'santri') {
                // Populate santri tags
                const tagsContainer = document.getElementById('edit-selected-santri-tags');
                if (tagsContainer && data.jenisTagihan.santri_data) {
                    tagsContainer.innerHTML = '';
                    data.jenisTagihan.santri_data.forEach(santri => {
                        const tag = document.createElement('div');
                        tag.className = 'bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-sm flex items-center gap-2';
                        tag.setAttribute('data-santri-id', santri.id);
                        tag.innerHTML = `
                            <span>${santri.nama_lengkap}</span>
                            <input type="hidden" name="target_santri[]" value="${santri.id}">
                            <button type="button" onclick="removeEditSelectedSantri(${santri.id})" class="text-orange-600 hover:text-orange-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        `;
                        tagsContainer.appendChild(tag);
                    });
                }
            }
            
            // Set bulan pembayaran
            if (data.jenisTagihan.bulan_pembayaran) {
                const bulanCheckboxes = document.querySelectorAll('#editInsidentalModal input[name="bulan_pembayaran[]"]');
                bulanCheckboxes.forEach(checkbox => {
                    checkbox.checked = data.jenisTagihan.bulan_pembayaran.includes(parseInt(checkbox.value));
                });
            }
            
            // Populate buku kas
            if (data.bukuKasList) {
                const bukuKasSelect = document.getElementById('edit_insidental_buku_kas_id');
                if (bukuKasSelect) {
                    bukuKasSelect.innerHTML = '<option value="">Pilih Buku Kas</option>';
                    data.bukuKasList.forEach(kas => {
                        const option = document.createElement('option');
                        option.value = kas.id;
                        option.textContent = `${kas.nama_kas} (${kas.kode_kas})`;
                        if (kas.id === data.jenisTagihan.buku_kas_id) {
                            option.selected = true;
                        }
                        bukuKasSelect.appendChild(option);
                    });
                }
            }
            
            // Trigger target type change to show/hide sections
            const checkedRadio = document.querySelector('#editInsidentalModal input[name="target_type"]:checked');
            if (checkedRadio) {
                checkedRadio.dispatchEvent(new Event('change'));
            }
            
            // Set up event listeners for target type changes in edit modal
            const editTargetRadios = document.querySelectorAll('#editInsidentalModal input[name="target_type"]');
            editTargetRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    const targetSelection = document.getElementById('edit-target-selection');
                    const kelasSelection = document.getElementById('edit-kelas-selection');
                    const santriSelection = document.getElementById('edit-santri-selection');
                    
                    // Hide all sections first
                    if (targetSelection) targetSelection.classList.add('hidden');
                    if (kelasSelection) kelasSelection.classList.add('hidden');
                    if (santriSelection) santriSelection.classList.add('hidden');
                    
                    // Show appropriate section based on selection
                    if (this.value === 'kelas') {
                        if (targetSelection) targetSelection.classList.remove('hidden');
                        if (kelasSelection) kelasSelection.classList.remove('hidden');
                    } else if (this.value === 'santri') {
                        if (targetSelection) targetSelection.classList.remove('hidden');
                        if (santriSelection) santriSelection.classList.remove('hidden');
                        // Initialize santri search for edit modal
                        initializeEditSantriSearch();
                    }
                });
            });
            
        } else {
            console.error('Failed to load tagihan data:', data.message);
            alert('Gagal memuat data tagihan: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error loading tagihan data:', error);
        alert('Terjadi kesalahan saat memuat data tagihan');
    });
}

function closeEditInsidentalModal() {
    document.getElementById('editInsidentalModal').classList.add('hidden');
    clearEditInsidentalForm();
}

function clearEditInsidentalForm() {
    const form = document.getElementById('editInsidentalForm');
    if (form) {
        form.reset();
        // Clear error messages
        const errorElements = form.querySelectorAll('.text-red-500');
        errorElements.forEach(el => {
            el.classList.add('hidden');
        });
        
        // Clear santri tags
        const tagsContainer = document.getElementById('edit-selected-santri-tags');
        if (tagsContainer) {
            tagsContainer.innerHTML = '';
        }
    }
}

function submitEditInsidentalForm() {
    const form = document.getElementById('editInsidentalForm');
    if (!form) return;
    
    const formData = new FormData(form);
    const id = formData.get('id');
    
    // Clear previous errors
    const errorElements = form.querySelectorAll('.text-red-500');
    errorElements.forEach(el => {
        el.classList.add('hidden');
    });
    
    fetch(`/keuangan/jenis-tagihan/${id}`, {
        method: 'PUT',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeEditInsidentalModal();
            location.reload(); // Refresh page to show updated data
        } else {
            // Display validation errors
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const errorElement = document.getElementById(`edit-insidental-error-${field}`);
                    if (errorElement) {
                        errorElement.textContent = data.errors[field][0];
                        errorElement.classList.remove('hidden');
                    }
                });
            } else {
                alert('Gagal memperbarui tagihan: ' + (data.message || 'Unknown error'));
            }
        }
    })
    .catch(error => {
        console.error('Error updating tagihan:', error);
        alert('Terjadi kesalahan saat memperbarui tagihan');
    });
}

function removeEditSelectedSantri(santriId) {
    const tag = document.querySelector(`#edit-selected-santri-tags [data-santri-id="${santriId}"]`);
    if (tag) tag.remove();
}

function initializeEditSantriSearch() {
    const searchInput = document.getElementById('edit_santri_search');
    const dropdown = document.getElementById('edit-santri-dropdown');
    
    if (!searchInput || !dropdown) return;
    
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            dropdown.classList.add('hidden');
            return;
        }
        
        searchTimeout = setTimeout(() => {
            searchEditSantri(query);
        }, 300);
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
}

function searchEditSantri(query) {
    const dropdown = document.getElementById('edit-santri-dropdown');
    if (!dropdown) return;
    
    dropdown.classList.remove('hidden');
    dropdown.innerHTML = '<div class="p-2 text-gray-500">Mencari...</div>';
    
    fetch(`/keuangan/jenis-tagihan/search-santri?q=${encodeURIComponent(query)}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            dropdown.innerHTML = '';
            
            if (data.santri.length === 0) {
                dropdown.innerHTML = '<div class="p-2 text-gray-500">Tidak ada santri ditemukan</div>';
            } else {
                data.santri.forEach(santri => {
                    // Check if santri is already selected
                    const existingTag = document.querySelector(`#edit-selected-santri-tags [data-santri-id="${santri.id}"]`);
                    if (existingTag) return; // Skip if already selected
                    
                    const item = document.createElement('div');
                    item.className = 'p-2 hover:bg-gray-100 cursor-pointer border-b border-gray-200 last:border-b-0';
                    item.innerHTML = `
                        <div class="text-sm font-medium text-gray-900">${santri.nama}</div>
                        <div class="text-xs text-gray-500">${santri.kelas ? santri.kelas.nama : 'Tidak ada kelas'}</div>
                    `;
                    
                    item.addEventListener('click', () => {
                        addEditSelectedSantri(santri);
                        dropdown.classList.add('hidden');
                        document.getElementById('edit_santri_search').value = '';
                    });
                    
                    dropdown.appendChild(item);
                });
            }
        } else {
            dropdown.innerHTML = '<div class="p-2 text-red-500">Gagal memuat data santri</div>';
        }
    })
    .catch(error => {
        console.error('Error searching santri:', error);
        dropdown.innerHTML = '<div class="p-2 text-red-500">Terjadi kesalahan</div>';
    });
}

function addEditSelectedSantri(santri) {
    const tagsContainer = document.getElementById('edit-selected-santri-tags');
    
    if (!tagsContainer) return;
    
    // Check if santri is already selected
    const existingTag = tagsContainer.querySelector(`[data-santri-id="${santri.id}"]`);
    if (existingTag) return;
    
    // Create tag element
    const tag = document.createElement('div');
    tag.className = 'bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-sm flex items-center gap-2';
    tag.setAttribute('data-santri-id', santri.id);
    tag.innerHTML = `
        <span>${santri.nama}</span>
        <input type="hidden" name="target_santri[]" value="${santri.id}">
        <button type="button" onclick="removeEditSelectedSantri(${santri.id})" class="text-orange-600 hover:text-orange-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    
    // Add to container
    tagsContainer.appendChild(tag);
}

function submitInsidentalForm() {
    const submitBtn = document.querySelector('#insidentalModal button[onclick="submitInsidentalForm()"]');
    const loadingSpinner = document.getElementById('insidental-loading-spinner');
    const submitText = document.getElementById('insidental-submit-text');
    
    // Show loading state
    if (loadingSpinner) loadingSpinner.classList.remove('hidden');
    if (submitText) submitText.textContent = 'Menyimpan...';
    if (submitBtn) submitBtn.disabled = true;
    
    const form = document.getElementById('insidentalForm');
    const formData = new FormData(form);
    
    // Clear previous errors
    const errorElements = form.querySelectorAll('.text-red-500');
    errorElements.forEach(el => {
        el.classList.add('hidden');
        el.textContent = '';
    });
    
    fetch('/keuangan/jenis-tagihan', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeInsidentalModal();
            alert('Tagihan insidental berhasil ditambahkan!');
            location.reload(); // Refresh to show new data
        } else {
            if (data.errors) {
                // Display validation errors
                Object.keys(data.errors).forEach(field => {
                    const errorElement = document.getElementById(`insidental-error-${field}`);
                    if (errorElement) {
                        errorElement.textContent = data.errors[field][0];
                        errorElement.classList.remove('hidden');
                    }
                });
            } else {
                alert('Gagal menambahkan tagihan: ' + (data.message || 'Unknown error'));
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan data: ' + error.message);
    })
    .finally(() => {
        // Hide loading state
        if (loadingSpinner) loadingSpinner.classList.add('hidden');
        if (submitText) submitText.textContent = 'Simpan';
        if (submitBtn) submitBtn.disabled = false;
    });
}

function submitRutinForm() {
    const submitBtn = document.querySelector('#createModal button[onclick="submitRutinForm()"]');
    const loadingSpinner = document.getElementById('rutin-loading-spinner');
    const submitText = document.getElementById('rutin-submit-text');
    
    // Show loading state
    if (loadingSpinner) loadingSpinner.classList.remove('hidden');
    if (submitText) submitText.textContent = 'Menyimpan...';
    if (submitBtn) submitBtn.disabled = true;
    
    const form = document.getElementById('createForm');
    const formData = new FormData(form);
    
    // Clear previous errors
    const errorElements = form.querySelectorAll('.text-red-500');
    errorElements.forEach(el => {
        el.classList.add('hidden');
        el.textContent = '';
    });
    
    fetch('/keuangan/jenis-tagihan', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeCreateModal();
            alert('Tagihan rutin berhasil ditambahkan!');
            location.reload(); // Refresh to show new data
        } else {
            if (data.errors) {
                // Display validation errors
                Object.keys(data.errors).forEach(field => {
                    const errorElement = document.getElementById(`rutin-error-${field}`);
                    if (errorElement) {
                        errorElement.textContent = data.errors[field][0];
                        errorElement.classList.remove('hidden');
                    }
                });
            } else {
                alert('Gagal menambahkan tagihan: ' + (data.message || 'Unknown error'));
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan data: ' + error.message);
    })
    .finally(() => {
        // Hide loading state
        if (loadingSpinner) loadingSpinner.classList.add('hidden');
        if (submitText) submitText.textContent = 'Simpan';
        if (submitBtn) submitBtn.disabled = false;
    });
}

// ...existing code...
</script>
@endsection
