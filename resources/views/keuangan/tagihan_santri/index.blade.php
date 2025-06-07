@extends('layouts.admin')

@section('content')
<div>
    <div class="flex flex-col xl:flex-row items-start xl:items-center justify-between mb-4 pb-3 border-b border-gray-200 gap-3">
        <div class="flex-1 min-w-0">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-3 text-blue-600 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zM14 6a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h8zM6 10a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2H8a2 2 0 01-2-2v-2z" />
                </svg>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        Tagihan Santri
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">
                        <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded-md text-xs font-medium">
                            Juli {{ $activeTahunAjaran->tahun_mulai }} - Juni {{ $activeTahunAjaran->tahun_selesai }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-4">
        <div class="p-4">
            <form method="GET" action="{{ route('keuangan.tagihan-santri.index') }}">
                <div class="grid grid-cols-12 gap-4 items-end">
                    <!-- Filter Label -->
                    <div class="col-span-12 mb-2">
                        <h3 class="text-sm font-medium text-gray-700">Filter & Pencarian</h3>
                    </div>
                    
                    <!-- Kelas Filter -->
                    <div class="col-span-12 md:col-span-3">
                        <label for="kelas" class="block text-xs font-medium text-gray-600 mb-1">Kelas</label>
                        <select name="kelas" id="kelas" class="w-full py-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">Semua Kelas</option>
                            @foreach($allKelas as $kelas)
                                <option value="{{ $kelas }}" {{ $kelasFilter == $kelas ? 'selected' : '' }}>{{ $kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Search Input -->
                    <div class="col-span-12 md:col-span-4">
                        <label for="searchSantri" class="block text-xs font-medium text-gray-600 mb-1">Cari Santri</label>
                        <input type="text" id="searchSantri" placeholder="Nama santri atau NIS..." 
                               class="w-full py-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    
                    <!-- Filter Button -->
                    <div class="col-span-12 md:col-span-2">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707v4.586a1 1 0 01-.293.707L11 21.414a1 1 0 01-.707.293H9a1 1 0 01-1-1v-7a1 1 0 00-.293-.707L1.293 7.293A1 1 0 011 6.586V4z" />
                            </svg>
                            Filter
                        </button>
                    </div>
                    
                    <!-- Reset Button -->
                    <div class="col-span-12 md:col-span-2">
                        <a href="{{ route('keuangan.tagihan-santri.index') }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reset
                        </a>
                    </div>
                    
                    <!-- Export Button -->
                    <div class="col-span-12 md:col-span-1">
                        <a href="{{ route('keuangan.tagihan-santri.export', request()->all()) }}" 
                           class="w-full inline-flex justify-center items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200"
                           title="Export Excel">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Data Tagihan Santri</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        Total data: <span id="totalRecords" class="font-medium">{{ count($santris) }}</span> santri
                    </p>
                </div>
            </div>
            
            <!-- Tabel Data Santri -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Santri</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Tagihan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Dibayar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Tagihan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="santriTableBody" class="bg-white divide-y divide-gray-200">
                        @foreach($santris as $index => $santri)
                            <tr class="santri-row hover:bg-gray-50" data-search="{{ strtolower($santri['nama_santri'] . ' ' . $santri['nis']) }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $santri['nama_santri'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $santri['nis'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $santri['kelas'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @php
                                        $totalTagihan = ($santri['summary_rutin']['total_tagihan'] ?? 0) + ($santri['summary_insidentil']['total_tagihan'] ?? 0);
                                    @endphp
                                    Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @php
                                        $totalDibayar = ($santri['summary_rutin']['total_pembayaran'] ?? 0) + ($santri['summary_insidentil']['total_pembayaran'] ?? 0);
                                    @endphp
                                    Rp {{ number_format($totalDibayar, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @php
                                        $sisaTagihan = ($santri['summary_rutin']['sisa_tagihan'] ?? 0) + ($santri['summary_insidentil']['sisa_tagihan'] ?? 0);
                                    @endphp
                                    <span class="{{ $sisaTagihan > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $status = $sisaTagihan == 0 ? 'Lunas' : ($totalDibayar > 0 ? 'Sebagian' : 'Belum Bayar');
                                        $statusColor = $sisaTagihan == 0 ? 'green' : ($totalDibayar > 0 ? 'yellow' : 'red');
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $statusColor == 'green' ? 'bg-green-100 text-green-800' : 
                                           ($statusColor == 'yellow' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="showDetailModal({{ $santri['id'] }})" class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>        </div>
    </div>

    <!-- Modal Detail Tagihan -->
    <div id="detailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Detail Tagihan Santri</h3>
                    <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div id="modalContent">
                    <!-- Content akan diisi via JavaScript -->
                </div>
            </div>
        </div>
    </div>

<script>
const santriData = @json($santris);
const activeTahunAjaran = @json($activeTahunAjaran);

// Handle search functionality with record counting
function handleSearch() {
    const searchInput = document.getElementById('searchSantri');
    const totalRecords = document.getElementById('totalRecords');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.santri-row');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const searchData = row.getAttribute('data-search');
                if (searchData && searchData.includes(searchTerm)) {
                    row.style.display = 'table-row';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Update record counter
            if (totalRecords) {
                totalRecords.textContent = visibleCount;
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    handleSearch();
    // Close modal when clicking outside
    const detailModal = document.getElementById('detailModal');
    if (detailModal) {
        detailModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeDetailModal();
            }
        });
    }
});

function showDetailModal(santriId) {
    const santri = santriData.find(s => s.id == santriId);
    if (!santri) {
        console.error('Santri not found with ID:', santriId);
        return;
    }
    
    document.getElementById('modalTitle').textContent = `Detail Tagihan - ${santri.nama_santri || 'Unknown'}`;
    let html = `
        <div class="mb-4 p-4 bg-gray-50 rounded-lg">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Nama Santri</p>
                    <p class="font-medium">${santri.nama_santri || 'N/A'}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">NIS</p>
                    <p class="font-medium">${santri.nis || 'N/A'}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Kelas</p>
                    <p class="font-medium">${santri.kelas || 'N/A'}</p>
                </div>
            </div>
        </div>
    `;
    // Tabs
    html += `
        <ul class="flex border-b mb-4">
            <li class="mr-2">
                <a href="#" class="tab-link inline-block px-4 py-2 text-sm font-medium border-b-2 border-blue-600 text-blue-600" onclick="showModalTab(this, 'rutin-detail'); return false;">
                    Tagihan Rutin
                </a>
            </li>
            <li class="mr-2">
                <a href="#" class="tab-link inline-block px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:text-gray-800" onclick="showModalTab(this, 'insidentil-detail'); return false;">
                    Tagihan Insidentil
                </a>
            </li>
        </ul>
    `;
    // Tagihan Rutin Tab
    html += `<div id="rutin-detail" class="tab-content">
        <h4 class="font-medium mb-3">Tagihan Rutin (Juli ${activeTahunAjaran.tahun_mulai} - Juni ${activeTahunAjaran.tahun_selesai})</h4>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Bulan</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jenis Tagihan</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nominal</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dibayar</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Sisa</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">`;
    
    if (!santri.tagihan_rutin || santri.tagihan_rutin.length === 0) {
        html += `<tr><td colspan="6" class="px-3 py-4 text-center text-gray-400">Tidak ada tagihan rutin</td></tr>`;
    } else {
        santri.tagihan_rutin.forEach((bulan, index) => {
            if (bulan.tagihan_items && bulan.tagihan_items.length > 0) {
                bulan.tagihan_items.forEach(item => {
                    const status = (item.total_dibayar || 0) == 0 ? 'Belum Bayar' : ((item.sisa_bayar || 0) == 0 ? 'Lunas' : 'Sebagian');
                    const statusColor = (item.total_dibayar || 0) == 0 ? 'text-red-600' : ((item.sisa_bayar || 0) == 0 ? 'text-green-600' : 'text-yellow-600');
                    html += `<tr>
                        <td class="px-3 py-2 text-sm">${bulan.nama_bulan_tahun || 'N/A'}</td>
                        <td class="px-3 py-2 text-sm">${item.nama_tagihan || 'N/A'}</td>
                        <td class="px-3 py-2 text-sm">Rp ${Number(item.nominal || 0).toLocaleString('id-ID')}</td>
                        <td class="px-3 py-2 text-sm">Rp ${Number(item.total_dibayar || 0).toLocaleString('id-ID')}</td>
                        <td class="px-3 py-2 text-sm">Rp ${Number(item.sisa_bayar || 0).toLocaleString('id-ID')}</td>
                        <td class="px-3 py-2 text-sm ${statusColor}">${status}</td>
                    </tr>`;
                });
            }
        });
    }
    html += `</tbody>
            <tfoot class="bg-gray-50">
                <tr class="font-medium">
                    <td colspan="2" class="px-3 py-2 text-right">Total</td>
                    <td class="px-3 py-2">Rp ${Number((santri.summary_rutin && santri.summary_rutin.total_tagihan) || 0).toLocaleString('id-ID')}</td>
                    <td class="px-3 py-2">Rp ${Number((santri.summary_rutin && santri.summary_rutin.total_pembayaran) || 0).toLocaleString('id-ID')}</td>
                    <td class="px-3 py-2">Rp ${Number((santri.summary_rutin && santri.summary_rutin.sisa_tagihan) || 0).toLocaleString('id-ID')}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        </div>
    </div>`;
    // Tagihan Insidentil Tab
    html += `<div id="insidentil-detail" class="tab-content hidden">
        <h4 class="font-medium mb-3">Tagihan Insidentil</h4>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jenis Tagihan</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nominal</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dibayar</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Sisa</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">`;
    if (!santri.tagihan_insidentil || santri.tagihan_insidentil.length === 0) {
        html += `<tr><td colspan="5" class="px-3 py-4 text-center text-gray-400">Tidak ada tagihan insidentil</td></tr>`;
    } else {
        santri.tagihan_insidentil.forEach(item => {
            const status = (item.total_dibayar || 0) == 0 ? 'Belum Bayar' : ((item.sisa_bayar || 0) == 0 ? 'Lunas' : 'Sebagian');
            const statusColor = (item.total_dibayar || 0) == 0 ? 'text-red-600' : ((item.sisa_bayar || 0) == 0 ? 'text-green-600' : 'text-yellow-600');
            html += `<tr>
                <td class="px-3 py-2 text-sm">${item.nama_tagihan || 'N/A'}</td>
                <td class="px-3 py-2 text-sm">Rp ${Number(item.nominal || 0).toLocaleString('id-ID')}</td>
                <td class="px-3 py-2 text-sm">Rp ${Number(item.total_dibayar || 0).toLocaleString('id-ID')}</td>
                <td class="px-3 py-2 text-sm">Rp ${Number(item.sisa_bayar || 0).toLocaleString('id-ID')}</td>
                <td class="px-3 py-2 text-sm ${statusColor}">${status}</td>
            </tr>`;
        });
    }
    html += `</tbody>
            <tfoot class="bg-gray-50">
                <tr class="font-medium">
                    <td class="px-3 py-2 text-right">Total</td>
                    <td class="px-3 py-2">Rp ${Number((santri.summary_insidentil && santri.summary_insidentil.total_tagihan) || 0).toLocaleString('id-ID')}</td>
                    <td class="px-3 py-2">Rp ${Number((santri.summary_insidentil && santri.summary_insidentil.total_pembayaran) || 0).toLocaleString('id-ID')}</td>
                    <td class="px-3 py-2">Rp ${Number((santri.summary_insidentil && santri.summary_insidentil.sisa_tagihan) || 0).toLocaleString('id-ID')}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        </div>
    </div>`;
    try {
        document.getElementById('modalContent').innerHTML = html;
        document.getElementById('detailModal').classList.remove('hidden');
    } catch (error) {
        console.error('Error showing modal:', error);
    }
}

function closeDetailModal() {
    const modal = document.getElementById('detailModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

function showModalTab(link, tabId) {
    // Remove active state from all tabs
    document.querySelectorAll('.tab-link').forEach(el => {
        el.classList.remove('border-blue-600', 'text-blue-600');
        el.classList.add('border-transparent', 'text-gray-600');
    });
    // Add active state to clicked tab
    link.classList.add('border-blue-600', 'text-blue-600');
    link.classList.remove('border-transparent', 'text-gray-600');
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    // Show selected tab content
    const targetTab = document.getElementById(tabId);
    if (targetTab) {
        targetTab.classList.remove('hidden');
    }
}
</script>
@endsection
