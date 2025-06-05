@extends('layouts.admin')

@section('content')
<div x-data="tagihanSantriApp()">
    {{-- Header + Actions --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 pb-4 border-b border-gray-200">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zM14 6a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h8zM6 10a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2H8a2 2 0 01-2-2v-2z" />
                </svg>
                Monitoring Implementasi Tagihan Santri
            </h1>
            <p class="text-sm text-gray-500 mt-1">Monitoring implementasi tagihan ke seluruh santri untuk tahun ajaran {{ $activeTahunAjaran->nama_tahun_ajaran }}.</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button @click="exportData()" 
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Excel
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Filter Data</h3>
            <form method="GET" action="{{ route('keuangan.tagihan-santri.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    {{-- Jenis Tagihan Filter --}}
                    <div>
                        <label for="jenis_tagihan_id" class="block text-sm font-medium text-gray-700 mb-2">Jenis Tagihan</label>
                        <select name="jenis_tagihan_id" id="jenis_tagihan_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Jenis Tagihan</option>
                            @foreach($jenisTagihans as $jenis)
                                <option value="{{ $jenis->id }}" {{ request('jenis_tagihan_id') == $jenis->id ? 'selected' : '' }}>
                                    {{ $jenis->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status Filter --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status Implementasi</label>
                        <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>Semua Status</option>
                            @if($selectedJenisTagihan)
                                <option value="implemented" {{ $statusFilter == 'implemented' ? 'selected' : '' }}>Sudah Ditetapkan</option>
                                <option value="not_implemented" {{ $statusFilter == 'not_implemented' ? 'selected' : '' }}>Belum Ditetapkan</option>
                            @else
                                <option value="complete" {{ $statusFilter == 'complete' ? 'selected' : '' }}>Lengkap (100%)</option>
                                <option value="partial" {{ $statusFilter == 'partial' ? 'selected' : '' }}>Sebagian (1-99%)</option>
                                <option value="none" {{ $statusFilter == 'none' ? 'selected' : '' }}>Belum Ada (0%)</option>
                            @endif
                        </select>
                    </div>

                    {{-- Kelas Filter --}}
                    <div>
                        <label for="kelas" class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                        <select name="kelas" id="kelas" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Kelas</option>
                            @foreach($allKelas as $kelas)
                                <option value="{{ $kelas }}" {{ $kelasFilter == $kelas ? 'selected' : '' }}>
                                    {{ $kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Asrama Filter --}}
                    <div>
                        <label for="asrama" class="block text-sm font-medium text-gray-700 mb-2">Asrama</label>
                        <select name="asrama" id="asrama" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Asrama</option>
                            @foreach($allAsrama as $asrama)
                                <option value="{{ $asrama }}" {{ $asramaFilter == $asrama ? 'selected' : '' }}>
                                    {{ $asrama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Button --}}
                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707v4.586a1 1 0 01-.293.707L11 21.414a1 1 0 01-.707.293H9a1 1 0 01-1-1v-7a1 1 0 00-.293-.707L1.293 7.293A1 1 0 011 6.586V4z" />
                            </svg>
                            Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary Statistics --}}
    @if(!$selectedJenisTagihan)
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Santri</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $santris->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Lengkap (100%)</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $santris->filter(function($s) { return $s['tagihan_status']['percentage'] == 100; })->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Sebagian</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $santris->filter(function($s) { return $s['tagihan_status']['percentage'] > 0 && $s['tagihan_status']['percentage'] < 100; })->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Belum Ada (0%)</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $santris->filter(function($s) { return $s['tagihan_status']['percentage'] == 0; })->count() }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Data Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    @if($selectedJenisTagihan)
                        Implementasi Tagihan: {{ $selectedJenisTagihan->nama }}
                    @else
                        Status Implementasi Tagihan Santri
                    @endif
                </h3>
                <div class="text-sm text-gray-500">
                    Total: {{ $santris->count() }} santri
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Santri
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kelas
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Asrama
                            </th>
                            @if($selectedJenisTagihan)
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nominal
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal Penetapan
                                </th>
                            @else
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Progress
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                            @endif
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($santris as $santri)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700">{{ substr($santri['nama_santri'], 0, 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $santri['nama_santri'] }}</div>
                                            <div class="text-sm text-gray-500">NIS: {{ $santri['nis'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $santri['kelas'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $santri['asrama'] }}
                                </td>

                                @if($selectedJenisTagihan)
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($santri['tagihan_status']['implemented'])
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                    <circle cx="4" cy="4" r="3"/>
                                                </svg>
                                                Sudah Ditetapkan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                    <circle cx="4" cy="4" r="3"/>
                                                </svg>
                                                Belum Ditetapkan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($santri['tagihan_status']['implemented'])
                                            Rp {{ number_format($santri['tagihan_status']['nominal'], 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $santri['tagihan_status']['tanggal_penetapan'] ?? '-' }}
                                    </td>
                                @else
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-16 bg-gray-200 rounded-full h-2">
                                                <div class="h-2 rounded-full 
                                                    @if($santri['tagihan_status']['percentage'] == 100) bg-green-600
                                                    @elseif($santri['tagihan_status']['percentage'] > 50) bg-yellow-500
                                                    @elseif($santri['tagihan_status']['percentage'] > 0) bg-orange-500
                                                    @else bg-red-500
                                                    @endif" 
                                                    style="width: {{ $santri['tagihan_status']['percentage'] }}%">
                                                </div>
                                            </div>
                                            <span class="ml-2 text-sm text-gray-600">{{ $santri['tagihan_status']['percentage'] }}%</span>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $santri['tagihan_status']['implemented_count'] }}/{{ $santri['tagihan_status']['total_jenis_tagihan'] }} tagihan
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($santri['tagihan_status']['percentage'] == 100)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Lengkap
                                            </span>
                                        @elseif($santri['tagihan_status']['percentage'] > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Sebagian
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Belum Ada
                                            </span>
                                        @endif
                                    </td>
                                @endif

                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button @click="showDetailModal({{ $santri['id'] }})" 
                                            class="text-blue-600 hover:text-blue-900 transition">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    Tidak ada data santri yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Detail Modal --}}
    <div x-show="showModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal()"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Detail Tagihan Santri</h3>
                        <button @click="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div x-show="loading" class="text-center py-8">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        <p class="mt-2 text-gray-600">Memuat data...</p>
                    </div>
                    
                    <div x-show="!loading && modalData" class="space-y-6">
                        {{-- Santri Info --}}
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Informasi Santri</h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-gray-700">Nama:</span>
                                    <span x-text="modalData?.santri?.nama_santri"></span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">NIS:</span>
                                    <span x-text="modalData?.santri?.nis"></span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Kelas:</span>
                                    <span x-text="modalData?.santri?.kelas"></span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Asrama:</span>
                                    <span x-text="modalData?.santri?.asrama"></span>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Tagihan Detail --}}
                        <div>
                            <h4 class="font-medium text-gray-900 mb-4">Detail Tagihan</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Tagihan</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal Default</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal Santri</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Penetapan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <template x-for="tagihan in modalData?.tagihan || []" :key="tagihan.id">
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="tagihan.nama"></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="tagihan.jenis_pembayaran"></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatRupiah(tagihan.nominal_default)"></td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span :class="tagihan.implemented ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                                                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                                        <span x-text="tagihan.implemented ? 'Sudah Ditetapkan' : 'Belum Ditetapkan'"></span>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="tagihan.nominal_santri ? formatRupiah(tagihan.nominal_santri) : '-'"></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="tagihan.tanggal_penetapan || '-'"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="closeModal()" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-600 text-base font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function tagihanSantriApp() {
    return {
        showModal: false,
        loading: false,
        modalData: null,
          async showDetailModal(santriId) {
            this.showModal = true;
            this.loading = true;
            this.modalData = null;
            
            try {
                const response = await fetch(`/keuangan/tagihan-santri/${santriId}`);
                const data = await response.json();
                
                if (response.ok) {
                    this.modalData = data;
                } else {
                    console.error('Error:', data.error);
                }
            } catch (error) {
                console.error('Fetch error:', error);
            } finally {
                this.loading = false;
            }
        },
        
        closeModal() {
            this.showModal = false;
            this.modalData = null;
            this.loading = false;
        },
        
        formatRupiah(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        },
          exportData() {
            // Menggunakan parameter filter yang sedang aktif
            const params = new URLSearchParams(window.location.search);
            const exportUrl = '{{ route("keuangan.tagihan-santri.export") }}?' + params.toString();
            window.location.href = exportUrl;
        }
    }
}
</script>
@endsection
