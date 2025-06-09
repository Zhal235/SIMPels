@extends('layouts.admin')

@section('content')
<script>
    window.dompetSantriDataUrl = "{{ route('keuangan.dompet.santri.index') }}";
    window.dompetTopupUrl = "{{ route('keuangan.dompet.santri.topup') }}";
    window.dompetWithdrawUrl = "{{ route('keuangan.dompet.santri.withdraw') }}";
    window.dompetAktivasiUrl = "{{ route('keuangan.dompet.santri.aktivasi') }}";
</script>
<div x-data="dompetSantri()">
    
    {{-- Header + Actions --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 pb-4 border-b border-gray-200">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zM14 6a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h8zM6 10a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2H8a2 2 0 01-2-2v-2z" />
                </svg>
                Dompet Digital Santri
            </h1>
            <p class="text-sm text-gray-500 mt-1">Kelola dompet digital dan transaksi santri pesantren.</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button @click="exportData()" 
                    class="inline-flex items-center px-4 py-2 bg-gray-500 text-white text-sm font-medium rounded-md opacity-50 cursor-not-allowed"
                    disabled>
                <span class="material-icons-outlined mr-2">file_download</span>
                Export (Belum Tersedia)
            </button>
        </div>
    </div>    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar - Daftar Santri (Right Side) -->
        <div class="lg:col-span-1 lg:order-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 flex flex-col h-full">
                <!-- Sidebar Header -->
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Daftar Santri</h2>
                    <!-- Search -->
                    <div class="mt-3">
                        <div class="relative">
                            <input type="text" 
                                   x-model="searchTerm"
                                   placeholder="Cari santri..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Santri List -->
                <div class="h-[600px] overflow-y-auto">
                    <div class="p-2 space-y-1">
                        <!-- Show loading or no data message if santriList is empty -->
                        <div x-show="!santriList || santriList.length === 0" class="text-center py-8">
                            <div class="text-gray-400 mb-2">
                                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Tidak Ada Data Santri</h3>
                            <p class="text-gray-500">Data santri tidak ditemukan atau belum dimuat</p>
                        </div>

                        <!-- Santri items -->
                        <template x-for="(santri, index) in filteredSantri" :key="santri.id">
                            <div @click="selectSantri(santri)"
                                 class="p-3 rounded-lg cursor-pointer transition-colors hover:bg-gray-50"
                                 :class="selectedSantri?.id === santri.id ? 'bg-blue-50 border border-blue-200' : 'border border-transparent'">
                                <div class="flex items-center space-x-3">
                                    <img :src="santri.foto || '{{ asset('img/default-avatar.png') }}'" 
                                         :alt="santri.nama"
                                         class="w-10 h-10 rounded-full object-cover border border-gray-200"
                                         onerror="this.src='{{ asset('img/default-avatar.png') }}'">
                                    <div class="flex-1 min-w-0">
                                        <div class="font-medium text-gray-900 text-sm truncate" x-text="santri.nama"></div>
                                        <div class="text-xs text-gray-500" x-text="'NIS: ' + santri.nis"></div>
                                        <div class="text-xs text-gray-400" x-text="santri.kelas"></div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                
                <!-- Total count -->
                <div class="p-3 border-t border-gray-200 bg-gray-50 text-center">
                    <span class="text-xs text-gray-500" x-text="santriList ? 'Total: ' + filteredSantri.length + ' santri' : 'Memuat...'"></span>
                </div>
            </div>
        </div>
        
        <!-- Main Content Area (Left Side) -->
        <div class="lg:col-span-3 lg:order-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">

                <!-- Content -->
                <div class="p-6">
                    <div x-show="!selectedSantri" class="text-center py-16">
                        <div class="text-gray-400 mb-4">
                            <svg class="mx-auto h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-medium text-gray-900 mb-2">Pilih Santri</h3>
                        <p class="text-gray-500">Pilih santri dari daftar di samping kanan untuk melihat detail dompet digital dan transaksi</p>
                    </div>

                    <!-- Santri Details -->
                    <div x-show="selectedSantri" class="space-y-6">
                        <!-- Santri Info Card -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-100">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                                Data Santri
                            </h2>
                            <div class="flex items-start space-x-6">
                                <!-- Foto Santri -->
                                <div class="flex-shrink-0">
                                    <img :src="selectedSantri?.foto" 
                                         :alt="selectedSantri?.nama"
                                         class="w-20 h-20 rounded-lg object-cover border-2 border-white shadow-md">
                                </div>
                                <!-- Data Santri -->
                                <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nama</label>
                                        <p class="mt-1 text-sm text-gray-900 font-medium" x-text="selectedSantri?.nama"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">NIS</label>
                                        <p class="mt-1 text-sm text-gray-900" x-text="selectedSantri?.nis"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tempat, Tanggal Lahir</label>
                                        <p class="mt-1 text-sm text-gray-900" x-text="selectedSantri?.tempat_lahir + ', ' + selectedSantri?.tanggal_lahir"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Kelas</label>
                                        <p class="mt-1 text-sm text-gray-900" x-text="selectedSantri?.kelas"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Asrama</label>
                                        <p class="mt-1 text-sm text-gray-900" x-text="selectedSantri?.asrama"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nama Orang Tua</label>
                                        <p class="mt-1 text-sm text-gray-900" x-text="selectedSantri?.nama_ortu"></p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Nomor HP</label>
                                        <p class="mt-1 text-sm text-gray-900" x-text="selectedSantri?.no_hp"></p>
                                    </div>
                                </div>
                            </div>
                        </div>                        <!-- Dompet Information -->
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                            <!-- Show if santri has dompet -->
                            <div x-show="selectedSantri?.dompet" class="divide-y divide-gray-200">
                                <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zM14 6a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h8zM6 10a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2H8a2 2 0 01-2-2v-2z" />
                                            </svg>
                                            Informasi Dompet Digital
                                        </h3>
                                        <p class="text-sm text-gray-500 mt-1">Kelola saldo dan transaksi dompet santri</p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button @click="openTopupModal()" 
                                                x-show="walletStatus === 'active'"
                                                class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors shadow-sm flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                            </svg>
                                            Top Up
                                        </button>
                                        <button @click="openWithdrawModal()" 
                                                x-show="walletStatus === 'active'"
                                                class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors shadow-sm flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            Tarik
                                        </button>
                                        <button @click="toggleWalletStatus()" 
                                                class="text-gray-600 hover:text-gray-800 font-medium py-2 px-4 rounded-lg border border-gray-300 hover:border-gray-400 transition-colors shadow-sm flex items-center gap-2">
                                            <svg x-show="walletStatus === 'active'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd" />
                                            </svg>
                                            <svg x-show="walletStatus === 'inactive'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span x-text="walletStatus === 'active' ? 'Nonaktifkan' : 'Aktifkan'"></span>
                                        </button>
                                    </div>
                                </div>                                </div>
                                <div class="p-6">
                                    <!-- Dompet Balance Card -->
                                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-6 border border-green-100 mb-6">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900 mb-2">Saldo Dompet</h4>
                                                <div class="flex items-baseline space-x-2">
                                                    <span class="text-3xl font-bold text-green-600" x-text="formatRupiah(walletBalance)"></span>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                                          :class="walletStatus === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                                          x-text="walletStatus === 'active' ? 'Aktif' : 'Tidak Aktif'">
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm text-gray-600">Nomor Dompet</div>
                                                <div class="text-lg font-mono font-medium text-gray-900" x-text="walletNumber"></div>
                                                <div class="text-xs text-gray-500 mt-1">Limit: <span x-text="formatRupiah(transactionLimit)"></span></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Transaction History -->
                                    <div class="mb-6">
                                        <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 16a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            Riwayat Transaksi
                                        </h4>
                                        
                                        <div class="space-y-3">
                                            <template x-for="transaction in transactions" :key="transaction.id">
                                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center space-x-3">
                                                            <div class="flex-shrink-0">
                                                                <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                                                     :class="transaction.type === 'credit' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                                        <path x-show="transaction.type === 'credit'" fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                                                        <path x-show="transaction.type === 'debit'" fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <div class="font-medium text-gray-900" x-text="transaction.description"></div>
                                                                <div class="text-sm text-gray-500" x-text="formatDate(transaction.date)"></div>
                                                            </div>
                                                        </div>
                                                        <div class="text-right">
                                                            <div class="font-medium" 
                                                                 :class="transaction.type === 'credit' ? 'text-green-600' : 'text-red-600'"
                                                                 x-text="(transaction.type === 'credit' ? '+' : '-') + formatRupiah(transaction.amount)">
                                                            </div>
                                                            <div class="text-xs text-gray-500">Saldo: <span x-text="formatRupiah(transaction.balance)"></span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                            
                                            <!-- Empty state -->
                                            <div x-show="transactions.length === 0" class="text-center py-8">
                                                <div class="text-gray-400 mb-2">
                                                    <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                    </svg>
                                                </div>
                                                <h3 class="text-lg font-medium text-gray-900 mb-1">Belum Ada Transaksi</h3>
                                                <p class="text-gray-500">Transaksi dompet akan muncul di sini</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Show if santri doesn't have dompet -->
                            <div x-show="!selectedSantri?.dompet" class="p-6">
                                <div class="text-center py-16">
                                    <div class="text-gray-400 mb-4">
                                        <svg class="mx-auto h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 0h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2M7 7h10" />
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-medium text-gray-900 mb-2">Belum Memiliki Dompet Digital</h3>
                                    <p class="text-gray-500 mb-6">Santri ini belum memiliki dompet digital. Silakan buat dompet terlebih dahulu.</p>
                                    <a :href="'{{ route('keuangan.dompet.santri.create') }}?santri_id=' + selectedSantri?.id" 
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                        </svg>
                                        Buat Dompet Digital
                                    </a>
                                </div>
                            </div>                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Up Modal -->
    <div x-show="showTopupModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showTopupModal = false"></div>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m8-8H4" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                Top Up Dompet
                            </h3>
                            
                            <div class="mb-4">
                                <div class="bg-green-50 p-3 rounded-md mb-4">
                                    <p class="text-sm text-green-800">Saldo Saat Ini: <span class="font-bold" x-text="formatRupiah(walletBalance)"></span></p>
                                </div>
                                
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Top Up</label>
                                <input type="number" 
                                       x-model="topupAmount"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                       placeholder="Masukkan jumlah top up">
                                       
                                <div x-show="topupAmount > 0" class="mt-3 p-3 bg-gray-50 rounded-md">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-700">Saldo Setelah Top Up:</span>
                                        <span class="text-lg font-bold text-green-600" x-text="formatRupiah(walletBalance + (topupAmount || 0))"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="processTopup()" 
                            :disabled="!topupAmount || topupAmount <= 0"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Proses Top Up
                    </button>
                    <button @click="showTopupModal = false; topupAmount = 0;" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Withdraw Modal -->
    <div x-show="showWithdrawModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showWithdrawModal = false"></div>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                Tarik Saldo Dompet
                            </h3>
                            
                            <div class="mb-4">
                                <div class="bg-red-50 p-3 rounded-md mb-4">
                                    <p class="text-sm text-red-800">Saldo Tersedia: <span class="font-bold" x-text="formatRupiah(walletBalance)"></span></p>
                                </div>
                                
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Penarikan</label>
                                <input type="number" 
                                       x-model="withdrawAmount"
                                       :max="walletBalance"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                       placeholder="Masukkan jumlah penarikan">
                                       
                                <div x-show="withdrawAmount > 0" class="mt-3 p-3 bg-gray-50 rounded-md">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-700">Saldo Setelah Penarikan:</span>
                                        <span class="text-lg font-bold text-red-600" x-text="formatRupiah(Math.max(0, walletBalance - (withdrawAmount || 0)))"></span>
                                    </div>
                                </div>
                                
                                <div x-show="withdrawAmount > walletBalance" class="mt-3 p-3 bg-red-50 rounded-md">
                                    <div class="flex items-center text-red-800">
                                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-sm">Jumlah penarikan melebihi saldo yang tersedia</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="processWithdraw()" 
                            :disabled="!withdrawAmount || withdrawAmount <= 0 || withdrawAmount > walletBalance"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Proses Penarikan
                    </button>
                    <button @click="showWithdrawModal = false; withdrawAmount = 0;" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function dompetSantri() {
    return {
        selectedSantri: null,
        searchTerm: '',
        santriList: @json($santriList ?? []),
        walletBalance: 0,
        walletNumber: '',
        walletStatus: 'active',
        transactionLimit: 500000,
        transactions: [],
        showTopupModal: false,
        showWithdrawModal: false,
        topupAmount: 0,
        withdrawAmount: 0,

        init() {
            console.log('Dompet Santri initialized');
            console.log('Santri list:', this.santriList);
            console.log('Total santri:', this.santriList.length);
        },

        formatRupiah(number) {
            const safeNumber = isNaN(number) ? 0 : parseFloat(number);
            return new Intl.NumberFormat('id-ID', { 
                style: 'currency', 
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0 
            }).format(safeNumber);
        },

        formatDate(dateString) {
            if (!dateString) return '';
            
            try {
                const date = new Date(dateString);
                const options = { 
                    year: 'numeric', 
                    month: 'short', 
                    day: 'numeric',
                    timeZone: 'Asia/Jakarta'
                };
                return new Intl.DateTimeFormat('id-ID', options).format(date);
            } catch (error) {
                return dateString;
            }
        },        get filteredSantri() {
            if (!this.santriList || !Array.isArray(this.santriList)) {
                return [];
            }
            
            let filtered = this.santriList;
            if (this.searchTerm && this.searchTerm.trim() !== '') {
                const searchLower = this.searchTerm.toLowerCase().trim();
                filtered = this.santriList.filter(santri => 
                    (santri.nama && santri.nama.toLowerCase().includes(searchLower)) ||
                    (santri.nis && santri.nis.toString().includes(searchLower))
                );
            }
            return filtered;
        },

        selectSantri(santri) {
            this.selectedSantri = santri;
            this.loadWalletData(santri.id);
        },        async loadWalletData(santriId) {
            try {
                // If the santri has a dompet, load it from the santri data
                if (this.selectedSantri.dompet) {
                    this.walletBalance = this.selectedSantri.dompet.saldo;
                    this.walletNumber = this.selectedSantri.dompet.nomor_dompet;
                    this.walletStatus = this.selectedSantri.dompet.is_active ? 'active' : 'inactive';
                    this.transactionLimit = this.selectedSantri.dompet.limit_transaksi;
                    
                    // Load transaction history via AJAX
                    await this.loadTransactionHistory(this.selectedSantri.dompet.id);
                } else {
                    // No dompet exists for this santri
                    this.walletBalance = 0;
                    this.walletNumber = '';
                    this.walletStatus = 'inactive';
                    this.transactionLimit = 500000;
                    this.transactions = [];
                }
            } catch (error) {
                console.error('Error loading wallet data:', error);
                this.transactions = [];
            }
        },

        async loadTransactionHistory(dompetId) {
            try {
                const response = await fetch(`${window.dompetSantriDataUrl}?dompet_id=${dompetId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    this.transactions = data.transaksi || [];
                } else {
                    this.transactions = [];
                }
            } catch (error) {
                console.error('Error loading transaction history:', error);
                this.transactions = [];
            }
        },

        openTopupModal() {
            this.showTopupModal = true;
            this.topupAmount = 0;
        },

        openWithdrawModal() {
            this.showWithdrawModal = true;
            this.withdrawAmount = 0;
        },        async processTopup() {
            if (!this.selectedSantri || !this.selectedSantri.dompet) {
                alert('Santri belum memiliki dompet digital');
                return;
            }

            try {
                const response = await fetch(window.dompetTopupUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        dompet_id: this.selectedSantri.dompet.id,
                        jumlah: parseFloat(this.topupAmount),
                        keterangan: 'Top up saldo dompet'
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Update balance
                    this.walletBalance = data.saldo_baru;
                    this.selectedSantri.dompet.saldo = data.saldo_baru;
                    
                    // Reload transaction history
                    await this.loadTransactionHistory(this.selectedSantri.dompet.id);

                    // Close modal and reset
                    this.showTopupModal = false;
                    this.topupAmount = 0;
                    
                    alert('Top up berhasil!');
                } else {
                    alert('Gagal memproses top up: ' + data.message);
                }
            } catch (error) {
                console.error('Error processing top up:', error);
                alert('Gagal memproses top up');
            }
        },        async processWithdraw() {
            if (!this.selectedSantri || !this.selectedSantri.dompet) {
                alert('Santri belum memiliki dompet digital');
                return;
            }

            try {
                const response = await fetch(window.dompetWithdrawUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        dompet_id: this.selectedSantri.dompet.id,
                        jumlah: parseFloat(this.withdrawAmount),
                        keterangan: 'Penarikan saldo dompet'
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Update balance
                    this.walletBalance = data.saldo_baru;
                    this.selectedSantri.dompet.saldo = data.saldo_baru;
                    
                    // Reload transaction history
                    await this.loadTransactionHistory(this.selectedSantri.dompet.id);

                    // Close modal and reset
                    this.showWithdrawModal = false;
                    this.withdrawAmount = 0;
                    
                    alert('Penarikan berhasil!');
                } else {
                    alert('Gagal memproses penarikan: ' + data.message);
                }
            } catch (error) {
                console.error('Error processing withdraw:', error);
                alert('Gagal memproses penarikan');
            }        },

        async toggleWalletStatus() {
            if (!this.selectedSantri || !this.selectedSantri.dompet) {
                alert('Santri belum memiliki dompet digital');
                return;
            }

            try {
                const newStatus = this.walletStatus === 'active' ? false : true;
                
                const response = await fetch(window.dompetAktivasiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        dompet_id: this.selectedSantri.dompet.id,
                        is_active: newStatus
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.walletStatus = data.is_active ? 'active' : 'inactive';
                    this.selectedSantri.dompet.is_active = data.is_active;
                    
                    alert(`Dompet berhasil ${data.is_active ? 'diaktifkan' : 'dinonaktifkan'}`);
                } else {
                    alert('Gagal mengubah status dompet: ' + data.message);
                }
            } catch (error) {
                console.error('Error toggling wallet status:', error);
                alert('Gagal mengubah status dompet');
            }
        },

        exportData() {
            alert('Fitur export belum tersedia');
        }
    }
}
</script>
@endsection
