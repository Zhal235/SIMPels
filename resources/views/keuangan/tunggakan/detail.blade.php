@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
                <span class="material-icons-outlined text-blue-600">receipt_long</span>
                Detail Tunggakan Santri
            </h1>
            <p class="text-gray-600 mt-2">Rincian tagihan yang belum dibayar oleh santri.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 mt-4 sm:mt-0">
            <a href="{{ route('keuangan.tunggakan.export-excel') }}?santri_id={{ $santri->id }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                <span class="material-icons-outlined text-sm mr-2">file_download</span>
                Export Excel
            </a>
            <a href="{{ route('keuangan.tunggakan.print') }}?santri_id={{ $santri->id }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                <span class="material-icons-outlined text-sm mr-2">print</span>
                Cetak Laporan
            </a>
            
            @php
                // Determine the back button route based on santri status
                $backRoute = 'keuangan.tunggakan.santri-aktif';
                if ($santri->status === 'mutasi') {
                    $backRoute = 'keuangan.tunggakan.santri-mutasi';
                } elseif ($santri->status === 'alumni') {
                    $backRoute = 'keuangan.tunggakan.santri-alumni';
                }
            @endphp
            
            <a href="{{ route($backRoute) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                <span class="material-icons-outlined text-sm mr-2">arrow_back</span>
                Kembali
            </a>
        </div>
    </div>
    
    <!-- Flash Messages -->
    @if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <svg onclick="this.parentElement.parentElement.remove()" class="fill-current h-6 w-6 text-green-500 cursor-pointer" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <title>Close</title>
                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
            </svg>
        </span>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <svg onclick="this.parentElement.parentElement.remove()" class="fill-current h-6 w-6 text-red-500 cursor-pointer" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <title>Close</title>
                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
            </svg>
        </span>
    </div>
    @endif

    @if(session('info'))
    <div class="mb-6 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('info') }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <svg onclick="this.parentElement.parentElement.remove()" class="fill-current h-6 w-6 text-blue-500 cursor-pointer" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <title>Close</title>
                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
            </svg>
        </span>
    </div>
    @endif

    <!-- Santri Information Card -->
    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3 border-b pb-2">Data Santri</h3>
                <div class="space-y-2">
                    <div>
                        <span class="text-gray-600 text-sm">NIS:</span>
                        <p class="font-medium">{{ $santri->nis }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600 text-sm">Nama:</span>
                        <p class="font-medium">{{ $santri->nama_santri }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600 text-sm">Status:</span>
                        <p class="font-medium">
                            @if($santri->status === 'aktif')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            @elseif($santri->status === 'mutasi')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    Mutasi
                                </span>
                            @elseif($santri->status === 'alumni')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    Alumni
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $santri->status }}
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3 border-b pb-2">Data Akademik</h3>
                <div class="space-y-2">
                    <div>
                        <span class="text-gray-600 text-sm">Kelas:</span>
                        <p class="font-medium">{{ $santri->kelasRelasi->pluck('nama')->join(', ') ?: '-' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600 text-sm">Asrama:</span>
                        <p class="font-medium">{{ $santri->asrama_anggota_terakhir->asrama->nama ?? '-' }}</p>
                    </div>
                    @if($santri->status === 'mutasi' && $santri->mutasi)
                    <div>
                        <span class="text-gray-600 text-sm">Tanggal Mutasi:</span>
                        <p class="font-medium">{{ \Carbon\Carbon::parse($santri->mutasi->tanggal_mutasi)->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600 text-sm">Tujuan Mutasi:</span>
                        <p class="font-medium">{{ $santri->mutasi->sekolah_tujuan }}</p>
                    </div>
                    @endif
                </div>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3 border-b pb-2">Ringkasan Tunggakan</h3>
                <div class="space-y-2">
                    <div>
                        <span class="text-gray-600 text-sm">Total Tunggakan:</span>
                        <p class="text-xl font-bold text-red-600">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600 text-sm">Tunggakan Tahun Ajaran Saat Ini:</span>
                        <p class="font-medium text-red-600">Rp {{ number_format($totalTunggakanTahunIni, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600 text-sm">Tunggakan Tahun Ajaran Sebelumnya:</span>
                        <p class="font-medium text-red-600">Rp {{ number_format($totalTunggakanTahunSebelumnya, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600 text-sm">Jumlah Tagihan:</span>
                        <p class="font-medium">{{ $tagihanTahunIni->count() + $tagihanTahunSebelumnya->count() }} tagihan</p>
                    </div>                    <div class="pt-3">
                        <a href="{{ route('keuangan.pembayaran-santri.index', ['santri_id' => $santri->id]) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center w-full">
                            <span class="material-icons-outlined text-base mr-1">payments</span>
                            Bayar Tunggakan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tagihan Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden" x-data="{ showPrevious: false }">
        <div class="flex justify-between items-center border-b p-4">
            <h3 class="text-lg font-semibold text-gray-900">Detail Tagihan</h3>
            <div>
                <button @click="showPrevious = !showPrevious" class="px-3 py-1 text-sm rounded-md border hover:bg-gray-50 inline-flex items-center">
                    <span x-text="showPrevious ? 'Sembunyikan Tagihan Tahun Sebelumnya' : 'Tampilkan Tagihan Tahun Sebelumnya'"></span>
                    <span class="material-icons-outlined text-sm ml-1" x-text="showPrevious ? 'expand_less' : 'expand_more'"></span>
                </button>
            </div>
        </div>
        
        <div>
            <h4 class="px-4 py-2 bg-blue-50 text-blue-700 text-sm font-medium border-b border-blue-100">
                Tahun Ajaran {{ $tahunAjaran->nama }}
            </h4>
            
            @if($tagihanTahunIni->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal Tagihan
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jenis Tagihan
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Periode
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nominal
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sudah Dibayar
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sisa Tagihan
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($tagihanTahunIni as $item)
                        <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($item->tanggal_tagihan)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->jenisTagihan->nama }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $item->bulan_tahun }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Rp {{ number_format($item->nominal_tagihan, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Rp {{ number_format($item->nominal_dibayar, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">
                            Rp {{ number_format($item->sisa_tagihan, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($item->status_pembayaran == 'lunas')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Lunas
                                </span>
                            @elseif($item->status_pembayaran == 'sebagian')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Sebagian
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Belum Bayar
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-medium">
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-right text-gray-900">Total Tunggakan:</td>
                        <td class="px-6 py-4 text-lg font-bold text-red-600">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="p-8 text-center">
            <div class="text-gray-500 text-lg mb-4">Tidak ada data tunggakan tahun ini untuk santri ini.</div>
            <div class="text-gray-400">Semua tagihan tahun ini sudah dilunasi.</div>
        </div>
        @endif
        
        <!-- Previous Years Tagihan (Collapsed by default) -->
        <div x-show="showPrevious" x-transition class="border-t">
            <h4 class="px-4 py-2 bg-orange-50 text-orange-700 text-sm font-medium border-b border-orange-100">
                Tahun Ajaran Sebelumnya
            </h4>
            
            @if($tagihanTahunSebelumnya->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tahun Ajaran
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal Tagihan
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jenis Tagihan
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Periode
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nominal
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sudah Dibayar
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sisa Tagihan
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($tagihanTahunSebelumnya as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->tahunAjaran->nama }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($item->tanggal_tagihan)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->jenisTagihan->nama }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->bulan_tahun }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Rp {{ number_format($item->nominal_tagihan, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                Rp {{ number_format($item->nominal_dibayar, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">
                                Rp {{ number_format($item->sisa_tagihan, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($item->status_pembayaran == 'lunas')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Lunas
                                    </span>
                                @elseif($item->status_pembayaran == 'sebagian')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Sebagian
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Belum Bayar
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 font-medium">
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-right text-gray-900">Subtotal Tunggakan Tahun Sebelumnya:</td>
                            <td class="px-6 py-4 text-lg font-bold text-red-600">Rp {{ number_format($totalTunggakanTahunSebelumnya, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
            <div class="p-8 text-center">
                <div class="text-gray-500 text-lg mb-4">Tidak ada tunggakan tahun sebelumnya.</div>
                <div class="text-gray-400">Semua tagihan tahun sebelumnya sudah dilunasi.</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
