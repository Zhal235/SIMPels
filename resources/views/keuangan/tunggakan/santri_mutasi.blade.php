@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
                <span class="material-icons-outlined text-orange-600">swap_horiz</span>
                Tunggakan Santri Mutasi
            </h1>
            <p class="text-gray-600 mt-2">Daftar tunggakan tagihan santri yang telah dimutasi tahun ajaran {{ $tahunAjaran->nama_tahun_ajaran }}.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 mt-4 sm:mt-0">
            <a href="{{ route('keuangan.tunggakan.export-excel') }}?tipe=mutasi" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                <span class="material-icons-outlined text-sm mr-2">file_download</span>
                Export Excel
            </a>
            <a href="{{ route('keuangan.tunggakan.print') }}?tipe=mutasi" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                <span class="material-icons-outlined text-sm mr-2">print</span>
                Cetak Laporan
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

    <!-- Filter & Search Section -->
    <div class="mb-6 bg-white rounded-lg shadow p-4">
        <div class="flex flex-col md:flex-row md:items-end gap-4">
            <div class="flex-1">
                <label for="search-filter" class="block text-sm font-medium text-gray-700 mb-1">Cari Santri</label>
                <div class="relative">
                    <input type="text" id="search-filter" class="w-full border-gray-300 rounded-lg pl-10 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari berdasarkan NIS atau nama...">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 material-icons-outlined text-gray-500">search</span>
                </div>
            </div>
            <div>
                <label for="jenis-tagihan-filter" class="block text-sm font-medium text-gray-700 mb-1">Jenis Tagihan</label>
                <select id="jenis-tagihan-filter" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Jenis</option>
                    @foreach($jenisTagihan as $jenis)
                    <option value="{{ $jenis->id }}">{{ $jenis->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button id="apply-filter" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                    <span class="material-icons-outlined mr-1 text-sm">filter_list</span>
                    Terapkan Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Sub Menu Navigation -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="flex flex-wrap">
            <a href="{{ route('keuangan.tunggakan.santri-aktif') }}" class="px-4 py-3 border-b-2 border-transparent hover:border-gray-300 text-gray-700">
                Santri Aktif
            </a>
            <a href="{{ route('keuangan.tunggakan.santri-mutasi') }}" class="px-4 py-3 border-b-2 border-orange-600 text-orange-600 font-medium">
                Mutasi
            </a>
            <a href="{{ route('keuangan.tunggakan.santri-alumni') }}" class="px-4 py-3 border-b-2 border-transparent hover:border-gray-300 text-gray-700">
                Alumni
            </a>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($tunggakan->count() > 0)
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        NIS
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nama Santri
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Total Tunggakan
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Jumlah Tagihan
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tanggal Mutasi
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Sekolah Tujuan
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($tunggakan as $santri_id => $tagihanList)
                @php
                    $santri = $tagihanList->first()->santri;
                    $totalTunggakan = $tagihanList->sum('sisa_tagihan');
                    $mutasi = $santri->mutasi ? $santri->mutasi : null;
                    $tanggalMutasi = $mutasi ? \Carbon\Carbon::parse($mutasi->tanggal_mutasi)->format('d-m-Y') : '-';
                    $sekolahTujuan = $mutasi ? $mutasi->sekolah_tujuan : '-';
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $santri->nis }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $santri->nama_santri }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                        Rp {{ number_format($totalTunggakan, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $tagihanList->count() }} tagihan
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $tanggalMutasi }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $sekolahTujuan }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('keuangan.tunggakan.detail', $santri_id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                            <span class="material-icons-outlined text-base align-middle">visibility</span>
                            Lihat
                        </a>                        <a href="{{ route('keuangan.pembayaran-santri.index', ['santri_id' => $santri_id]) }}" class="text-emerald-600 hover:text-emerald-900">
                            <span class="material-icons-outlined text-base align-middle">payments</span>
                            Bayar
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="p-8 text-center">
            <div class="text-gray-500 text-lg mb-4">Tidak ada data tunggakan untuk santri mutasi.</div>
            <div class="text-gray-400">Semua santri mutasi telah melunasi tagihan mereka.</div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchFilter = document.getElementById('search-filter');
        const jenisTagihanFilter = document.getElementById('jenis-tagihan-filter');
        const applyFilterButton = document.getElementById('apply-filter');
        const tableRows = document.querySelectorAll('tbody tr');

        // Filter function
        function filterTable() {
            const searchValue = searchFilter.value.toLowerCase();
            const jenisValue = jenisTagihanFilter.value;

            tableRows.forEach(row => {
                const nis = row.cells[0].textContent.toLowerCase();
                const nama = row.cells[1].textContent.toLowerCase();
                const matchSearch = nis.includes(searchValue) || nama.includes(searchValue);

                // For now we just filter by search, jenis tagihan would require backend filtering
                row.style.display = matchSearch ? '' : 'none';
            });
        }

        // Event listeners
        searchFilter.addEventListener('input', filterTable);
        applyFilterButton.addEventListener('click', filterTable);

        // Apply filters on page load if values exist
        if (searchFilter.value || jenisTagihanFilter.value) {
            filterTable();
        }
    });
</script>
@endpush
@endsection
