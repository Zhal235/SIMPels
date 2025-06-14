@extends('layouts.admin')

@section('content')
<script>
    window.pembayaranSantriProcessUrl = "{{ route('keuangan.pembayaran-santri.process') }}";
    window.pembayaranSantriDataUrl = "{{ url('keuangan/pembayaran-santri') }}";
</script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<div x-data="pembayaranSantri()">
    
    {{-- Header + Actions --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 pb-4 border-b border-gray-200">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zM14 6a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h8zM6 10a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2H8a2 2 0 01-2-2v-2z" />
                </svg>
                Pembayaran Santri
            </h1>
            <p class="text-sm text-gray-500 mt-1">Kelola tagihan dan pembayaran santri pesantren.</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button @click="cetakKwitansi()" 
                    class="inline-flex items-center px-4 py-2 bg-gray-500 text-white text-sm font-medium rounded-md opacity-50 cursor-not-allowed"
                    disabled>
                <span class="material-icons-outlined mr-2">receipt</span>
                Cetak Kwitansi (Belum Tersedia)
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Main Content Area -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">

                <!-- Content -->
                <div class="p-6">
                    <div x-show="!selectedSantri" class="text-center py-16">
                        <div class="text-gray-400 mb-4">
                            <svg class="mx-auto h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-medium text-gray-900 mb-2">Pilih Santri</h3>
                        <p class="text-gray-500">Pilih santri dari sidebar untuk melihat detail dan tagihan pembayaran</p>
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
                        </div>

                        <!-- Payment Status -->
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                            <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zM14 6a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h8zM6 10a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2H8a2 2 0 01-2-2v-2z" />
                                        </svg>
                                        Tagihan Pembayaran
                                    </h3>
                                    <p class="text-sm text-gray-500 mt-1">Status pembayaran bulanan santri</p>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" 
                                               x-model="selectAll" 
                                               @change="toggleSelectAll()"
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">Pilih Semua</span>
                                    </label>
                                </div>
                            </div>
                            <div class="p-6">
                                <!-- Bulk Payment Section - Moved to top -->
                                <div x-show="selectedPayments.length > 0" class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                        <div>
                                            <h4 class="text-lg font-medium text-gray-900 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                Pembayaran Terpilih
                                            </h4>
                                            <p class="text-sm text-gray-600" x-text="selectedPayments.length + ' item dipilih'"></p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-600">Total Tagihan:</p>
                                            <p class="text-xl font-bold text-blue-600" x-text="formatRupiah(totalSelectedAmount)"></p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <button @click="bayarTerpilih()" 
                                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors shadow-sm flex items-center justify-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zM14 6a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h8zM6 10a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2H8a2 2 0 01-2-2v-2z" clip-rule="evenodd" />
                                            </svg>
                                            Bayar Lunas
                                        </button>
                                        <button x-show="selectedPayments.length > 0" @click="openPartialPaymentModal()" 
                                                class="bg-orange-600 hover:bg-orange-700 text-white font-medium py-3 px-4 rounded-lg transition-colors shadow-sm flex items-center justify-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                            </svg>
                                            Bayar Sebagian
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Tab Navigation -->
                                <div class="mb-6 border-b border-gray-200">
                                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                                        <li class="mr-2">
                                            <button @click="activeTab = 'rutin'; selectedPayments = []; selectAll = false;" 
                                                    :class="{'text-blue-600 border-blue-600': activeTab === 'rutin', 'text-gray-500 hover:text-gray-600 border-transparent': activeTab !== 'rutin'}"
                                                    class="inline-flex items-center p-4 border-b-2 rounded-t-lg group">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" :class="{'text-blue-600': activeTab === 'rutin', 'text-gray-400': activeTab !== 'rutin'}" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                                                </svg>
                                                Tagihan Rutin
                                                <span class="ml-2 bg-blue-100 text-blue-700 py-0.5 px-2 rounded-full text-xs" x-text="payments.length"></span>
                                            </button>
                                        </li>
                                        <li class="mr-2">
                                            <button @click="activeTab = 'insidentil'; selectedPayments = []; selectAll = false;" 
                                                    :class="{'text-purple-600 border-purple-600': activeTab === 'insidentil', 'text-gray-500 hover:text-gray-600 border-transparent': activeTab !== 'insidentil'}"
                                                    class="inline-flex items-center p-4 border-b-2 rounded-t-lg group">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" :class="{'text-purple-600': activeTab === 'insidentil', 'text-gray-400': activeTab !== 'insidentil'}" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" clip-rule="evenodd" />
                                                </svg>
                                                Tagihan Insidentil
                                                <span class="ml-2 bg-purple-100 text-purple-700 py-0.5 px-2 rounded-full text-xs" x-text="insidentilPayments.length"></span>
                                            </button>
                                        </li>
                                        <li class="mr-2">
                                            <button @click="activeTab = 'tunggakan'; selectedPayments = []; selectAll = false; loadTunggakanData();" 
                                                    :class="{'text-red-600 border-red-600': activeTab === 'tunggakan', 'text-gray-500 hover:text-gray-600 border-transparent': activeTab !== 'tunggakan'}"
                                                    class="inline-flex items-center p-4 border-b-2 rounded-t-lg group">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" :class="{'text-red-600': activeTab === 'tunggakan', 'text-gray-400': activeTab !== 'tunggakan'}" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                                Tunggakan
                                                <span class="ml-2 bg-red-100 text-red-700 py-0.5 px-2 rounded-full text-xs" x-text="tunggakanPayments.length"></span>
                                            </button>
                                        </li>
                                        <li class="mr-2">
                                            <button @click="activeTab = 'lunas'; selectedPayments = []; selectAll = false; loadPaidPayments();" 
                                                    :class="{'text-green-600 border-green-600': activeTab === 'lunas', 'text-gray-500 hover:text-gray-600 border-transparent': activeTab !== 'lunas'}"
                                                    class="inline-flex items-center p-4 border-b-2 rounded-t-lg group">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" :class="{'text-green-600': activeTab === 'lunas', 'text-gray-400': activeTab !== 'lunas'}" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                Sudah Dibayar
                                                <span class="ml-2 bg-green-100 text-green-700 py-0.5 px-2 rounded-full text-xs" x-text="paidPayments.length"></span>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                
                                <div class="overflow-x-auto pb-4">
                                    <!-- Tab Content: Tagihan Rutin -->
                                    <div x-show="activeTab === 'rutin'">
                                        <!-- Tagihan Rutin menggunakan data dari payments (tagihan santri) -->
                                        <div x-show="payments.filter(p => p.status !== 'lunas' && (p.kategori_tagihan === 'Rutin' || p.is_bulanan)).length > 0">
                                            <!-- Group by Tahun Ajaran (mirip dengan tunggakan) -->
                                            <div class="mb-8">
                                                <!-- Tahun Ajaran Header untuk Rutin -->
                                                <div class="bg-blue-100 px-6 py-4 rounded-t-lg border border-blue-200">
                                                    <div class="flex justify-between items-center">
                                                        <div>
                                                            <h3 class="text-lg font-semibold text-blue-900">Tagihan Rutin Tahun Ajaran Aktif</h3>
                                                            <p class="text-sm text-blue-700" x-text="payments.filter(p => p.status !== 'lunas' && (p.kategori_tagihan === 'Rutin' || p.is_bulanan)).length + ' tagihan belum lunas'"></p>
                                                        </div>
                                                        <div class="text-right">
                                                            <p class="text-sm text-blue-700">Total Tagihan:</p>
                                                            <p class="text-xl font-bold text-blue-900" x-text="formatRupiah(payments.filter(p => p.status !== 'lunas' && (p.kategori_tagihan === 'Rutin' || p.is_bulanan)).reduce((total, p) => total + (p.sisa || 0), 0))"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Monthly Payment Boxes for Rutin -->
                                                <div class="border-l border-r border-b border-blue-200 rounded-b-lg bg-blue-50/30 p-6">
                                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                                        <template x-for="month in [...new Set(payments.filter(p => p.status !== 'lunas' && (p.kategori_tagihan === 'Rutin' || p.is_bulanan) && p.bulan).map(p => p.bulan))].sort()" :key="month">
                                                            <div class="border border-blue-200 rounded-lg overflow-hidden shadow-sm bg-white">
                                                                <!-- Month Header with Checkbox -->
                                                                <div class="bg-blue-50 px-4 py-3 border-b border-blue-200 flex justify-between items-center">
                                                                    <h3 class="font-medium text-blue-900" x-text="formatMonthDisplay(month)"></h3>
                                                                    <label class="flex items-center">
                                                                        <input type="checkbox" 
                                                                               @change="toggleMonthSelection(month, 'rutin')"
                                                                               :checked="isMonthSelected(month, 'rutin')"
                                                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                                        <span class="ml-2 text-xs text-blue-700">Pilih Semua</span>
                                                                    </label>
                                                                </div>
                                                                
                                                                <!-- Month Payments -->
                                                                <div class="divide-y divide-blue-100">
                                                                    <template x-for="payment in payments.filter(p => p.bulan === month && p.status !== 'lunas' && (p.kategori_tagihan === 'Rutin' || p.is_bulanan))" :key="payment.id">
                                                                        <div class="p-4">
                                                                            <div class="flex justify-between items-start mb-2">
                                                                                <div>
                                                                                    <div class="font-medium text-gray-900" x-text="payment.jenis_tagihan"></div>
                                                                                    <div class="text-xs text-blue-600 font-medium" x-text="payment.kategori_tagihan || (payment.is_bulanan ? 'Rutin' : 'Lainnya')"></div>
                                                                                </div>
                                                                                <div>
                                                                                    <input type="checkbox"
                                                                                        :value="payment.id" 
                                                                                        :checked="selectedPayments.includes(Number(payment.id))"
                                                                                        @change="togglePaymentSelection(Number(payment.id))"
                                                                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                                                </div>
                                                                            </div>
                                                                            
                                                                            <!-- Payment Details -->
                                                                            <div class="mt-2 space-y-1 text-sm">
                                                                                <div class="flex justify-between">
                                                                                    <span class="text-gray-600">Tagihan:</span>
                                                                                    <span class="font-medium" x-text="formatRupiah(payment.tagihan)"></span>
                                                                                </div>
                                                                                <div class="flex justify-between" x-show="payment.keringanan > 0">
                                                                                    <span class="text-gray-600">Keringanan:</span>
                                                                                    <span class="text-blue-600" x-text="formatRupiah(payment.keringanan)"></span>
                                                                                </div>
                                                                                <div class="flex justify-between" x-show="payment.dibayar > 0">
                                                                                    <span class="text-gray-600">Dibayar:</span>
                                                                                    <span class="text-green-600" x-text="formatRupiah(payment.dibayar)"></span>
                                                                                </div>
                                                                                <div class="flex justify-between border-t border-gray-100 pt-1">
                                                                                    <span class="text-gray-600">Sisa:</span>
                                                                                    <span class="font-medium text-blue-600" x-text="formatRupiah(payment.sisa)"></span>
                                                                                </div>
                                                                            </div>
                                                                            
                                                                            <!-- Due Date & Status -->
                                                                            <div class="mt-3 space-y-2">
                                                                                <div x-show="payment.tanggal_jatuh_tempo" class="text-xs text-blue-500">
                                                                                    Jatuh Tempo: <span x-text="formatDate(payment.tanggal_jatuh_tempo)"></span>
                                                                                </div>
                                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium w-full justify-center"
                                                                                    :class="{
                                                                                        'bg-red-100 text-red-800': payment.status === 'belum_bayar',
                                                                                        'bg-yellow-100 text-yellow-800': payment.status === 'sebagian'
                                                                                    }"
                                                                                    x-text="payment.status === 'sebagian' ? 'Sebagian' : 'Belum Bayar'">
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </template>
                                                                    
                                                                    <!-- Empty state if no payments for this month -->
                                                                    <div x-show="!payments.some(p => p.bulan === month && p.status !== 'lunas' && (p.kategori_tagihan === 'Rutin' || p.is_bulanan))" class="p-4 text-center text-gray-500 text-sm">
                                                                        Tidak ada tagihan rutin
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Empty state if no rutin payments -->
                                        <div x-show="payments.filter(p => p.status !== 'lunas' && (p.kategori_tagihan === 'Rutin' || p.is_bulanan)).length === 0" class="text-center py-12">
                                            <div class="text-gray-400 mb-4">
                                                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Tagihan Rutin</h3>
                                            <p class="text-gray-500">Semua tagihan rutin telah dibayar lunas</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Tab Content: Tagihan Insidentil -->
                                    <div x-show="activeTab === 'insidentil'">
                                        <!-- Check if we have insidentil payments data -->
                                        <div x-show="insidentilPayments.length === 0" class="text-center py-8">
                                            <div class="text-gray-400 mb-4">
                                                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Tagihan Insidentil</h3>
                                            <p class="text-gray-500">Santri ini tidak memiliki tagihan insidentil</p>
                                        </div>

                                        <!-- Insidentil Payment Boxes -->
                                        <div x-show="insidentilPayments.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                            <template x-for="payment in insidentilPayments" :key="payment.id">
                                                <div class="border rounded-lg overflow-hidden shadow-sm">
                                                <div class="border rounded-lg overflow-hidden shadow-sm">
                                                    <!-- Payment Header -->
                                                    <div class="bg-purple-50 px-4 py-3 border-b flex justify-between items-center">
                                                        <div>
                                                            <h3 class="font-medium text-gray-900" x-text="payment.jenis_tagihan"></h3>
                                                            <p class="text-xs text-purple-600 font-medium">Insidentil</p>
                                                        </div>
                                                        <input type="checkbox"
                                                            :value="payment.id" 
                                                            :checked="selectedPayments.includes(Number(payment.id))"
                                                            @change="togglePaymentSelection(Number(payment.id))"
                                                            class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                                    </div>
                                                    
                                                    <!-- Payment Content -->
                                                    <div class="p-4">
                                                        <!-- Payment Details -->
                                                        <div class="space-y-1 text-sm">
                                                            <div class="flex justify-between">
                                                                <span class="text-gray-600">Tagihan:</span>
                                                                <span class="font-medium" x-text="formatRupiah(payment.nominal_tagihan)"></span>
                                                            </div>
                                                            <div class="flex justify-between">
                                                                <span class="text-gray-600">Dibayar:</span>
                                                                <span class="font-medium" x-text="formatRupiah(payment.nominal_dibayar)"></span>
                                                            </div>
                                                            <div class="flex justify-between">
                                                                <span class="text-gray-600">Sisa:</span>
                                                                <span class="font-medium text-red-600" x-text="formatRupiah(payment.sisa_tagihan)"></span>
                                                            </div>
                                                            <div x-show="payment.bulan" class="flex justify-between pt-2 border-t border-gray-200">
                                                                <span class="text-gray-600">Bulan:</span>
                                                                <span class="font-medium" x-text="formatMonthDisplay(payment.bulan)"></span>
                                                            </div>
                                                            <!-- Tanggal Jatuh Tempo -->
                                                            <div x-show="payment.tanggal_jatuh_tempo" class="flex justify-between pt-1">
                                                                <span class="text-gray-600">Jatuh Tempo:</span>
                                                                <span class="font-medium" 
                                                                      :class="payment.is_jatuh_tempo ? 'text-red-600' : 'text-gray-900'"
                                                                      x-text="formatDate(payment.tanggal_jatuh_tempo)"></span>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Status Badge dengan Indikator Jatuh Tempo -->
                                                        <div class="mt-3 space-y-2">
                                                            <!-- Badge Jatuh Tempo (jika sudah jatuh tempo) -->
                                                            <div x-show="payment.is_jatuh_tempo" class="text-center">
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 w-full justify-center">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                                    </svg>
                                                                    Sudah Jatuh Tempo
                                                                </span>
                                                            </div>
                                                            <!-- Badge Status Pembayaran -->
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium w-full justify-center"
                                                                :class="{
                                                                    'bg-red-100 text-red-800': payment.status_pembayaran === 'belum_bayar',
                                                                    'bg-yellow-100 text-yellow-800': payment.status_pembayaran === 'sebagian'
                                                                }"
                                                                x-text="payment.status_pembayaran === 'sebagian' ? 'Sebagian' : 'Belum Bayar'">
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    
                                    <!-- Tab Content: Tunggakan -->
                                    <div x-show="activeTab === 'tunggakan'">
                                        <!-- Tunggakan grouped by bulan -->
                                        <div x-show="tunggakanPayments.length > 0">
                                            <!-- Group by Tahun Ajaran first -->
                                            <template x-for="(tahunGroup, tahunId) in groupedTunggakan" :key="tahunId">
                                                <div class="mb-8">
                                                    <!-- Tahun Ajaran Header -->
                                                    <div class="bg-red-100 px-6 py-4 rounded-t-lg border border-red-200">
                                                        <div class="flex justify-between items-center">
                                                            <div>
                                                                <h3 class="text-lg font-semibold text-red-900" x-text="tahunGroup.nama"></h3>
                                                                <p class="text-sm text-red-700" x-text="tahunGroup.payments.length + ' tagihan belum lunas'"></p>
                                                            </div>
                                                            <div class="text-right">
                                                                <p class="text-sm text-red-700">Total Tunggakan:</p>
                                                                <p class="text-xl font-bold text-red-900" x-text="formatRupiah(tahunGroup.total)"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Monthly Payment Boxes for this Tahun Ajaran -->
                                                    <div class="border-l border-r border-b border-red-200 rounded-b-lg bg-red-50/30 p-6">
                                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                                            <template x-for="month in getUniqueMonthsForTahun(tahunGroup.payments)" :key="month">
                                                                <div class="border border-red-200 rounded-lg overflow-hidden shadow-sm bg-white">
                                                                    <!-- Month Header with Checkbox -->
                                                                    <div class="bg-red-50 px-4 py-3 border-b border-red-200 flex justify-between items-center">
                                                                        <h3 class="font-medium text-red-900" x-text="formatMonthDisplay(month)"></h3>
                                                                        <label class="flex items-center">
                                                                            <input type="checkbox" 
                                                                                   @change="toggleMonthSelectionTunggakan(month, tahunId)"
                                                                                   :checked="isMonthSelectedTunggakan(month, tahunId)"
                                                                                   class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                                                            <span class="ml-2 text-xs text-red-700">Pilih Semua</span>
                                                                        </label>
                                                                    </div>
                                                                    
                                                                    <!-- Month Payments -->
                                                                    <div class="divide-y divide-red-100">
                                                                        <template x-for="payment in tahunGroup.payments.filter(p => p.bulan === month)" :key="payment.id">
                                                                            <div class="p-4">
                                                                                <div class="flex justify-between items-start mb-2">
                                                                                    <div>
                                                                                        <div class="font-medium text-gray-900" x-text="payment.jenis_tagihan"></div>
                                                                                        <div class="text-xs text-red-600 font-medium" x-text="payment.kategori_tagihan || (payment.is_bulanan ? 'Rutin' : 'Lainnya')"></div>
                                                                                    </div>
                                                                                    <div>
                                                                                        <input type="checkbox"
                                                                                            :value="payment.id" 
                                                                                            :checked="selectedPayments.includes(Number(payment.id))"
                                                                                            @change="togglePaymentSelection(Number(payment.id))"
                                                                                            class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                                                                    </div>
                                                                                </div>
                                                                                
                                                                                <!-- Payment Details -->
                                                                                <div class="mt-2 space-y-1 text-sm">
                                                                                    <div class="flex justify-between">
                                                                                        <span class="text-gray-600">Tagihan:</span>
                                                                                        <span class="font-medium" x-text="formatRupiah(payment.nominal_tagihan)"></span>
                                                                                    </div>
                                                                                    <div class="flex justify-between" x-show="payment.nominal_dibayar > 0">
                                                                                        <span class="text-gray-600">Dibayar:</span>
                                                                                        <span class="text-green-600" x-text="formatRupiah(payment.nominal_dibayar)"></span>
                                                                                    </div>
                                                                                    <div class="flex justify-between" x-show="payment.nominal_keringanan > 0">
                                                                                        <span class="text-gray-600">Keringanan:</span>
                                                                                        <span class="text-blue-600" x-text="formatRupiah(payment.nominal_keringanan)"></span>
                                                                                    </div>
                                                                                    <div class="flex justify-between border-t border-gray-100 pt-1">
                                                                                        <span class="text-gray-600">Sisa:</span>
                                                                                        <span class="font-medium text-red-600" x-text="formatRupiah(payment.sisa_tagihan)"></span>
                                                                                    </div>
                                                                                </div>
                                                                                
                                                                                <!-- Due Date & Status -->
                                                                                <div class="mt-3 space-y-2">
                                                                                    <div x-show="payment.tanggal_jatuh_tempo" class="text-xs text-red-500">
                                                                                        Jatuh Tempo: <span x-text="formatDate(payment.tanggal_jatuh_tempo)"></span>
                                                                                    </div>
                                                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium w-full justify-center bg-red-100 text-red-800">
                                                                                        Tunggakan
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </template>
                                                                        
                                                                        <!-- Empty state if no payments for this month -->
                                                                        <div x-show="!tahunGroup.payments.some(p => p.bulan === month)" class="p-4 text-center text-gray-500 text-sm">
                                                                            Tidak ada tunggakan
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                        
                                        <!-- Empty state if no tunggakan -->
                                        <div x-show="tunggakanPayments.length === 0" class="text-center py-12">
                                            <div class="text-gray-400 mb-4">
                                                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Tunggakan</h3>
                                            <p class="text-gray-500">Santri tidak memiliki tunggakan dari tahun ajaran manapun</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Tab Content: Sudah Dibayar -->
                                    <div x-show="activeTab === 'lunas'">
                                        <!-- Check if we have paid payments data -->
                                        <div x-show="paidPayments.length === 0" class="text-center py-8">
                                            <div class="text-gray-400 mb-4">
                                                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Pembayaran</h3>
                                            <p class="text-gray-500">Santri ini belum memiliki riwayat pembayaran yang tercatat</p>
                                        </div>

                                        <!-- Paid Payments List -->
                                        <div x-show="paidPayments.length > 0" class="space-y-4">
                                            <template x-for="payment in paidPayments" :key="payment.id">
                                                <div class="border rounded-lg p-4 bg-green-50 border-green-200">
                                                    <div class="flex justify-between items-start">
                                                        <div class="flex-1">
                                                            <h4 class="font-medium text-gray-900" x-text="payment.jenis_tagihan"></h4>
                                                            <p class="text-sm text-gray-600" x-text="payment.bulan ? formatMonthDisplay(payment.bulan) : 'Tanpa bulan'"></p>
                                                            <p class="text-sm text-gray-600" x-text="payment.tahun_ajaran"></p>
                                                        </div>
                                                        <div class="text-right">
                                                            <p class="font-semibold text-green-600" x-text="formatRupiah(payment.nominal_dibayar)"></p>
                                                            <p class="text-xs text-gray-500" x-text="formatDate(payment.tanggal_pembayaran)"></p>
                                                            <p class="text-xs text-gray-500" x-text="payment.metode_pembayaran || 'Tunai'"></p>
                                                            <button @click="deletePaidPayment(payment.id)" class="mt-2 px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition-colors" title="Hapus pembayaran">
                                                                Hapus
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div x-show="payment.keterangan" class="mt-2 pt-2 border-t border-green-300">
                                                        <p class="text-sm text-gray-600" x-text="payment.keterangan"></p>
                                                    </div>
                                                    <div class="mt-2 pt-2 border-t border-green-300">
                                                        <p class="text-xs text-gray-500">Admin: <span x-text="payment.admin_name"></span></p>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
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
                        <template x-for="(santri, index) in filteredSantri" :key="santri.id">
                            <div @click="selectSantri(santri)"
                                 class="p-3 rounded-lg cursor-pointer transition-colors hover:bg-gray-50"
                                 :class="selectedSantri?.id === santri.id ? 'bg-blue-50 border border-blue-200' : 'border border-transparent'">
                                <div class="flex items-center space-x-3">
                                    <img :src="santri.foto" 
                                         :alt="santri.nama"
                                         class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                    <div class="flex-1 min-w-0">
                                        <div class="font-medium text-gray-900 text-sm truncate" x-text="santri.nama"></div>
                                        <div class="text-xs text-gray-500" x-text="'NIS: ' + santri.nis"></div>
                                        <div class="text-xs text-gray-400" x-text="santri.kelas"></div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <!-- Semua santri ditampilkan -->
                    </div>
                </div>
                
                <!-- Total count -->
                <div class="p-3 border-t border-gray-200 bg-gray-50 text-center">
                    <span class="text-xs text-gray-500" x-text="'Total: ' + filteredSantri.length + ' santri'"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Partial Payment Modal -->
    <div x-show="showPartialPaymentModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showPartialPaymentModal = false"></div>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                Pembayaran Sebagian
                            </h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Pembayaran</label>
                                <input type="number" 
                                       x-model="partialAmount"
                                       @input="calculatePaymentPriority()"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                       placeholder="Masukkan jumlah pembayaran">
                                <p class="text-xs text-gray-500 mt-1">Total tagihan: <span x-text="formatRupiah(totalSelectedAmount)"></span></p>
                            </div>
                            

                            <div x-show="partialAmount > 0 && selectedPayments.length > 0 && partialAmount < totalSelectedAmount" class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Prioritas Pembayaran</label>
                                <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-md">
                                    <template x-for="(item, index) in paymentPriorityList" :key="item.id">
                                        <div class="p-3 border-b border-gray-100 last:border-b-0">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1">
                                                    <div class="font-medium text-sm" x-text="item.jenis_tagihan"></div>
                                                    <div class="text-xs text-gray-500" x-text="item.bulan + ' - ' + item.kategori"></div>
                                                    <div class="text-xs text-gray-600">Sisa: <span x-text="formatRupiah(item.sisa)"></span></div>
                                                </div>
                                                <div class="ml-3">
                                                    <label class="flex items-center">
                                                        <input type="checkbox" 
                                                               x-model="item.willBePaidFull"
                                                               @change="updatePaymentCalculation()"
                                                               class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                                        <span class="ml-2 text-xs" x-text="item.willBePaidFull ? 'Prioritas' : 'Pilih'"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <div class="mt-2 p-2 bg-gray-50 rounded text-sm">
                                    <div class="flex justify-between">
                                        <span>Akan dibayar lunas:</span>
                                        <span x-text="formatRupiah(calculatedFullPayment)"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Sisa uang:</span>
                                        <span x-text="formatRupiah(remainingAmount)"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="processPartialPayment()" 
                            :disabled="!partialAmount || partialAmount <= 0"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Proses Pembayaran
                    </button>
                    <button @click="showPartialPaymentModal = false; partialAmount = 0; calculatedFullPayment = 0; remainingAmount = 0; paymentPriorityList = [];" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Full Payment Modal -->
    <div x-show="showFullPaymentModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showFullPaymentModal = false"></div>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                Pembayaran Lunas
                            </h3>
                            
                            <div class="mb-4">
                                <div class="bg-blue-50 p-3 rounded-md mb-4">
                                    <p class="text-sm text-blue-800">Total Tagihan: <span class="font-bold" x-text="formatRupiah(totalSelectedAmount)"></span></p>
                                    <p class="text-xs text-blue-600 mt-1" x-text="selectedPayments.length + ' item pembayaran terpilih'"></p>
                                </div>
                                

                                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Uang Diterima</label>
                                <input type="number" 
                                       x-model="fullPaymentAmount"
                                       @input="calculateChange()"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Masukkan jumlah uang">
                            </div>
                            
                            <div x-show="fullPaymentAmount > 0 && fullPaymentAmount >= totalSelectedAmount" class="mb-4">
                                <div class="p-3 bg-green-50 rounded-md">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-green-800">Kembalian:</span>
                                        <span class="text-lg font-bold text-green-800" x-text="formatRupiah(fullPaymentChange)"></span>
                                    </div>
                                </div>
                                
                                <div class="mt-4" x-show="fullPaymentChange > 0">
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" 
                                               x-model="saveToWallet"
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm text-gray-700">Simpan kembalian ke dompet santri</span>
                                    </label>
                                    <p class="text-xs text-gray-500 mt-1 ml-6">Kembalian akan disimpan ke dompet santri untuk pembayaran berikutnya</p>
                                </div>
                            </div>
                            
                            <div x-show="fullPaymentAmount > 0 && fullPaymentAmount < totalSelectedAmount" class="p-3 bg-red-50 rounded-md mb-4">
                                <div class="flex items-center text-red-800">
                                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm">Jumlah uang kurang dari total tagihan</span>
                                </div>
                                <p class="text-xs text-red-700 mt-1 ml-7">Gunakan opsi "Bayar Sebagian" untuk pembayaran parsial</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="processFullPayment()" 
                            :disabled="!fullPaymentAmount || fullPaymentAmount < totalSelectedAmount"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Proses Pembayaran
                    </button>
                    <button @click="showFullPaymentModal = false; fullPaymentAmount = 0; fullPaymentChange = 0; saveToWallet = false;" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function pembayaranSantri() {
    return {
        selectedSantri: null,
        searchTerm: '',
        selectedPayments: [],
        selectAll: false,
togglePaymentSelection(paymentId) {
    const numericPaymentId = Number(paymentId);
    const index = this.selectedPayments.indexOf(numericPaymentId);
    if (index > -1) {
        this.selectedPayments.splice(index, 1);
    } else {
        this.selectedPayments.push(numericPaymentId);
    }
    // Ensure selectedPayments contains unique, numeric IDs
    this.selectedPayments = [...new Set(this.selectedPayments.map(id => Number(id)))];
    console.log('[togglePaymentSelection] selectedPayments after toggle:', JSON.parse(JSON.stringify(this.selectedPayments)));
    
    // Trigger reactive update for total calculation
    this.$nextTick(() => {
        console.log('[togglePaymentSelection] Total after toggle:', this.totalSelectedAmount);
    });
},

        updateSelectAll() {
            // Function to update selectAll state based on current tab and selections
            console.log('[updateSelectAll] Called');
            
            // Determine which data source to use based on active tab
            let dataSource = [];
            if (this.activeTab === 'tunggakan') {
                dataSource = this.tunggakanPayments;
            } else if (this.activeTab === 'insidentil') {
                dataSource = this.insidentilPayments;
            } else if (this.activeTab === 'lunas') {
                dataSource = this.paidPayments;
            } else {
                dataSource = this.payments; // default untuk tab rutin
            }
            
            // Get selectable payment IDs
            const selectablePaymentIds = dataSource
                .filter(p => p.status !== 'lunas' && p.status_pembayaran !== 'lunas')
                .map(p => Number(p.id));
                
            if (selectablePaymentIds.length === 0) {
                this.selectAll = false;
                return;
            }
            
            // Check if all selectable items are selected
            const allSelected = selectablePaymentIds.every(id => 
                this.selectedPayments.includes(Number(id))
            );
            
            // Update selectAll state
            this.selectAll = allSelected && selectablePaymentIds.length > 0;
            console.log('[updateSelectAll] New state:', this.selectAll);
        },
        
        init() {
    console.log('[Alpine init] Component initializing and setting up watchers.');
    
    // Check if we have a pre-selected santri from the backend
    const preSelectedSantriId = @json($selectedSantriId ?? null);
    if (preSelectedSantriId) {
        const foundSantri = this.santriList.find(s => s.id == preSelectedSantriId);
        if (foundSantri) {
            this.selectedSantri = foundSantri;
            this.loadPaymentData(foundSantri.id);
        }
    }
    
    this.$watch('selectedPayments', (currentSelectedIdsOriginal) => {
        // Ensure all IDs in currentSelectedIds are numbers for consistent comparison
        const currentSelectedIds = currentSelectedIdsOriginal.map(id => Number(id));

        const selectablePaymentIds = (this.payments || [])
            .filter(p => p.status !== 'lunas')
            .map(p => Number(p.id)); // Ensure these are also numbers

        let newSelectAllState = false;
        if (selectablePaymentIds.length > 0 && currentSelectedIds.length > 0) {
            // Check if all selectable IDs are present in the current selection
            const allSelectableAreSelected = selectablePaymentIds.every(id => currentSelectedIds.includes(id));
            // Also check if the number of selected items matches the number of selectable items
            if (allSelectableAreSelected && currentSelectedIds.length === selectablePaymentIds.length) {
                newSelectAllState = true;
            }
        } 
        // If no items are selectable, or no items are selected, selectAll should be false.
        // This is implicitly handled as newSelectAllState defaults to false.

        if (this.selectAll !== newSelectAllState) {
            this.selectAll = newSelectAllState;
        }
        console.log('[Watcher selectedPayments] Original currentSelectedIds:', JSON.parse(JSON.stringify(currentSelectedIdsOriginal)));
        console.log('[Watcher selectedPayments] Processed currentSelectedIds (all numbers):', JSON.parse(JSON.stringify(currentSelectedIds)));
        console.log('[Watcher selectedPayments] selectablePaymentIds (all numbers):', JSON.parse(JSON.stringify(selectablePaymentIds)));
        console.log('[Watcher selectedPayments] Calculated newSelectAllState:', newSelectAllState, '; Current this.selectAll:', this.selectAll);
    });
    
    // Watch for changes in selectedPayments to trigger total update
    this.$watch('selectedPayments', () => {
        console.log('[Watcher selectedPayments] Total updated:', this.totalSelectedAmount);
    });
    
    // Watch for changes in activeTab to load appropriate data
    this.$watch('activeTab', (newTab) => {
        console.log('[Watcher activeTab] Tab changed to:', newTab);
        // Reset selections
        this.selectedPayments = [];
        this.selectAll = false;
        
        // Load data based on active tab
        if (newTab === 'lunas') {
            this.loadPaidPayments();
        } else if (newTab === 'tunggakan') {
            this.loadTunggakanData();
        } else if (newTab === 'insidentil') {
            this.loadInsidentilPayments();
        }
    });
    
    console.log('[Alpine init] Initial selectAll (likely false as payments might not be loaded):', this.selectAll);
},
        payments: [],
        tunggakanPayments: [],
        insidentilPayments: [],
        paidPayments: [],
        santriList: @json($santris),
        jenisTagihans: @json($jenisTagihans),
        activeTab: 'rutin',
        showPartialPaymentModal: false,
        showFullPaymentModal: false,
        partialAmount: 0,
        fullPaymentAmount: 0,
        fullPaymentChange: 0,
        saveToWallet: false,
        paymentPriorityList: [],
        calculatedFullPayment: 0,
        remainingAmount: 0,

        // Add helper function for safe number formatting
        formatNumberSafe(value) {
            const num = parseFloat(value);
            return isNaN(num) ? 0 : num;
        },

        formatRupiah(number) {
            // Ensure we have a valid number
            const safeNumber = this.formatNumberSafe(number);
            // Format the number to rupiah
            return new Intl.NumberFormat('id-ID', { 
                style: 'currency', 
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0 
            }).format(safeNumber);
        },

        formatMonthDisplay(monthCode) {
            const monthMap = {
                '2024-07': 'Juli 2024',
                '2024-08': 'Agustus 2024',
                '2024-09': 'September 2024',
                '2024-10': 'Oktober 2024',
                '2024-11': 'November 2024',
                '2024-12': 'Desember 2024',
                '2025-01': 'Januari 2025',
                '2025-02': 'Februari 2025',
                '2025-03': 'Maret 2025',
                '2025-04': 'April 2025',
                '2025-05': 'Mei 2025',
                '2025-06': 'Juni 2025'
            };
            
            return monthMap[monthCode] || monthCode;
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
        },

        get filteredSantri() {
            let filtered = this.santriList;
            if (this.searchTerm) {
                filtered = this.santriList.filter(santri => 
                    santri.nama.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
                    santri.nis.includes(this.searchTerm)
                );
            }
            return filtered;
        },

        get totalSelectedAmount() {
            // Pastikan selectedPayments tidak kosong
            if (!this.selectedPayments || this.selectedPayments.length === 0) {
                return 0;
            }
            
            // Hitung total dari semua pembayaran yang dipilih
            let total = 0;
            
            // Tentukan sumber data berdasarkan tab aktif
            let dataSource = [];
            if (this.activeTab === 'tunggakan') {
                dataSource = this.tunggakanPayments;
            } else if (this.activeTab === 'insidentil') {
                dataSource = this.insidentilPayments;
            } else if (this.activeTab === 'lunas') {
                dataSource = this.paidPayments;
            } else {
                dataSource = this.payments; // default untuk tab rutin
            }
            
            console.log('[totalSelectedAmount] Active tab:', this.activeTab);
            console.log('[totalSelectedAmount] Data source length:', dataSource.length);
            console.log('[totalSelectedAmount] Selected payments:', this.selectedPayments);
            
            // Iterasi melalui setiap ID pembayaran yang dipilih
            for (const paymentId of this.selectedPayments) {
                // Cari pembayaran dengan ID yang sesuai
                const id = Number(paymentId);
                const payment = dataSource.find(p => Number(p.id) === id);
                
                console.log('[totalSelectedAmount] Looking for payment ID:', id, 'Found:', !!payment);
                
                // Jika pembayaran ditemukan, tambahkan sisa ke total
                if (payment) {
                    const sisaTagihan = payment.sisa_tagihan || payment.sisa || 0;
                    total += Number(sisaTagihan);
                    console.log('[totalSelectedAmount] Added:', sisaTagihan, 'Running total:', total);
                }
            }
            
            console.log('[totalSelectedAmount] Final total:', total);
            return total;
        },

        selectSantri(santri) {
            this.selectedSantri = santri;
            this.selectedPayments = [];
            this.selectAll = false;
            // Load all payment data for this santri
            this.refreshAllPaymentData();
        },

        // Method untuk refresh semua data pembayaran
        async refreshAllPaymentData() {
            if (!this.selectedSantri) {
                console.warn('No santri selected for data refresh');
                return;
            }

            console.log('[refreshAllPaymentData] Refreshing all payment data for santri:', this.selectedSantri.id);

            try {
                // Load all types of payment data in parallel
                await Promise.all([
                    this.loadPaymentData(this.selectedSantri.id),
                    this.loadTunggakanData(),
                    this.loadPaidPayments(),
                    this.loadInsidentilPayments()
                ]);

               

                console.log('[refreshAllPaymentData] All payment data refreshed successfully');
            } catch (error) {
                console.error('[refreshAllPaymentData] Error refreshing payment data:', error);
                await Swal.fire({
                    title: 'Error!',
                    text: 'Gagal memuat data pembayaran: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
            }
        },

        async loadPaymentData(santriId) {
            try {
                // Reset payment data
                this.payments = [];
                this.selectedPayments = [];
                this.selectAll = false;

                console.log('[loadPaymentData] Loading data for santri:', santriId);
                console.log('[loadPaymentData] Using URL:', `${window.pembayaranSantriDataUrl}/data/${santriId}`);

                // Fetch payment data from API (TagihanSantri)
                const response = await fetch(`${window.pembayaranSantriDataUrl}/data/${santriId}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('[loadPaymentData] Received data:', data);
                
                if (data && data.length > 0) {
                    this.payments = data.map((item, index) => {
                        const payment = {
                            id: item.id, // tagihan_santri_id
                            bulan: item.bulan,
                            jenis_tagihan: item.jenis_tagihan,
                            jenis_tagihan_id: item.jenis_tagihan_id,
                            kategori: 'Tagihan Santri',
                            kategori_tagihan: item.kategori_tagihan || 'Rutin',
                            is_bulanan: item.is_bulanan || false,
                            tagihan: this.formatNumberSafe(item.nominal_tagihan),
                            keringanan: this.formatNumberSafe(item.nominal_keringanan || 0),
                            dibayar: this.formatNumberSafe(item.nominal_dibayar),
                            sisa: this.formatNumberSafe(item.sisa_tagihan),
                            status: item.status_pembayaran,
                            tanggal_jatuh_tempo: item.tanggal_jatuh_tempo,
                            is_jatuh_tempo: item.is_jatuh_tempo,
                            keterangan: item.keterangan,
                            transaksis: item.transaksis || []
                        };
                        
                        return payment;
                    });
                    
                    console.log('Loaded payment data:', this.payments.length, 'items');
                } else {
                    // No payment data found
                    console.log('No payment data found for santri:', santriId);
                    this.payments = [];
                }
            } catch (error) {
                console.error('Error loading payment data:', error);
                console.error('Error details:', {
                    message: error.message,
                    santriId: santriId,
                    url: `${window.pembayaranSantriDataUrl}/data/${santriId}`
                });
                // Reset to empty array if error occurs
                this.payments = [];
                
                // Show user-friendly error message
                await Swal.fire({
                    title: 'Error!',
                    text: 'Gagal memuat data pembayaran: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
            }
        },

        async loadTunggakanData() {
            if (!this.selectedSantri) {
                console.warn('No santri selected for tunggakan data');
                return;
            }

            try {
                // Reset tunggakan data
                this.tunggakanPayments = [];
                this.selectedPayments = [];
                this.selectAll = false;

                console.log('Loading tunggakan data for santri:', this.selectedSantri.id);

                // Fetch tunggakan data from API
                const url = `{{ url('keuangan/pembayaran-santri/tunggakan') }}/${this.selectedSantri.id}`;
                console.log('Fetching from URL:', url);
                
                const response = await fetch(url);
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Tunggakan API response:', data);
                
                if (data && data.length > 0) {
                    this.tunggakanPayments = data.map((item) => {
                        const payment = {
                            id: Number(item.id), // Ensure ID is always a number
                            bulan: item.bulan,
                            jenis_tagihan: item.jenis_tagihan,
                            jenis_tagihan_id: Number(item.jenis_tagihan_id),
                            tahun_ajaran: item.tahun_ajaran,
                            tahun_ajaran_id: Number(item.tahun_ajaran_id),
                            kategori_tagihan: item.kategori_tagihan || 'Rutin',
                            is_bulanan: item.is_bulanan || false,
                            nominal_tagihan: this.formatNumberSafe(item.nominal_tagihan),
                            nominal_dibayar: this.formatNumberSafe(item.nominal_dibayar),
                            nominal_keringanan: this.formatNumberSafe(item.nominal_keringanan),
                            sisa_tagihan: this.formatNumberSafe(item.sisa_tagihan),
                            status_pembayaran: item.status_pembayaran,
                            tanggal_jatuh_tempo: item.tanggal_jatuh_tempo,
                            is_jatuh_tempo: item.is_jatuh_tempo,
                            keterangan: item.keterangan,
                            transaksis: item.transaksis || []
                        };
                        console.log('[Tunggakan] Mapped payment ID:', payment.id, 'Type:', typeof payment.id);
                        return payment;
                    });
                    console.log('Mapped tunggakan payments:', this.tunggakanPayments.length);
                    console.log('[Tunggakan] Sample payment IDs:', this.tunggakanPayments.slice(0, 3).map(p => p.id));
                } else {
                    this.tunggakanPayments = [];
                    console.log('No tunggakan data found');
                }
            } catch (error) {
                console.error('Error loading tunggakan data:', error);
                this.tunggakanPayments = [];
            }
        },

        async loadPaidPayments() {
            if (!this.selectedSantri) {
                console.warn('No santri selected for paid payments data');
                return;
            }

            try {
                // Reset paid payments data
                this.paidPayments = [];

                console.log('Loading paid payments data for santri:', this.selectedSantri.id);

                // Fetch paid payments data from API
                const url = `{{ url('keuangan/pembayaran-santri/paid') }}/${this.selectedSantri.id}`;
                console.log('Fetching from URL:', url);
                
                const response = await fetch(url);
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Paid payments API response:', data);
                
                if (data && data.length > 0) {
                    this.paidPayments = data.map((item) => {
                        const payment = {
                            id: Number(item.id),
                            tagihan_santri_id: Number(item.tagihan_santri_id),
                            bulan: item.bulan,
                            jenis_tagihan: item.jenis_tagihan,
                            jenis_tagihan_id: Number(item.jenis_tagihan_id),
                            tahun_ajaran: item.tahun_ajaran,
                            tahun_ajaran_id: Number(item.tahun_ajaran_id),
                            kategori_tagihan: item.kategori_tagihan || 'Rutin',
                            is_bulanan: item.is_bulanan || false,
                            nominal_tagihan: this.formatNumberSafe(item.nominal_tagihan),
                            nominal_dibayar: this.formatNumberSafe(item.nominal_dibayar),
                            tanggal_pembayaran: item.tanggal_pembayaran,
                            metode_pembayaran: item.metode_pembayaran,
                            keterangan: item.keterangan,
                            admin_name: item.admin_name
                        };
                        console.log('[Paid] Mapped payment ID:', payment.id, 'Type:', typeof payment.id);
                        return payment;
                    });
                    console.log('Mapped paid payments:', this.paidPayments.length);
                } else {
                    this.paidPayments = [];
                    console.log('No paid payments data found');
                }
            } catch (error) {
                console.error('Error loading paid payments data:', error);
                this.paidPayments = [];
            }
        },

        async loadInsidentilPayments() {
            if (!this.selectedSantri) {
                console.warn('No santri selected for insidentil payments data');
                return;
            }

            try {
                // Reset insidentil payments data
                this.insidentilPayments = [];

                console.log('Loading insidentil payments data for santri:', this.selectedSantri.id);

                // Fetch insidentil payments data from API
                const url = `{{ url('keuangan/pembayaran-santri/insidentil') }}/${this.selectedSantri.id}`;
                console.log('Fetching from URL:', url);
                
                const response = await fetch(url);
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Insidentil payments API response:', data);
                
                if (data && data.length > 0) {
                    this.insidentilPayments = data.map((item) => {
                        const payment = {
                            id: Number(item.id),
                            bulan: item.bulan,
                            jenis_tagihan: item.jenis_tagihan,
                            jenis_tagihan_id: Number(item.jenis_tagihan_id),
                            tahun_ajaran: item.tahun_ajaran,
                            tahun_ajaran_id: Number(item.tahun_ajaran_id),
                            kategori_tagihan: item.kategori_tagihan || 'Insidentil',
                            is_bulanan: item.is_bulanan || false,
                            nominal_tagihan: this.formatNumberSafe(item.nominal_tagihan),
                            nominal_dibayar: this.formatNumberSafe(item.nominal_dibayar),
                            nominal_keringanan: this.formatNumberSafe(item.nominal_keringanan),
                            sisa_tagihan: this.formatNumberSafe(item.sisa_tagihan),
                            status_pembayaran: item.status_pembayaran,
                            tanggal_jatuh_tempo: item.tanggal_jatuh_tempo,
                            is_jatuh_tempo: item.is_jatuh_tempo,
                            keterangan: item.keterangan,
                            transaksis: item.transaksis || []
                        };
                        console.log('[Insidentil] Mapped payment ID:', payment.id, 'Type:', typeof payment.id);
                        return payment;
                    });
                    console.log('Mapped insidentil payments:', this.insidentilPayments.length);
                } else {
                    this.insidentilPayments = [];
                    console.log('No insidentil payments data found');
                }
            } catch (error) {
                console.error('Error loading insidentil payments data:', error);
                this.insidentilPayments = [];
            }
        },

        get groupedTunggakan() {
            // Group tunggakan by tahun ajaran
            const grouped = {};
            
            this.tunggakanPayments.forEach(payment => {
                const tahunId = payment.tahun_ajaran_id;
                
                if (!grouped[tahunId]) {
                    grouped[tahunId] = {
                        nama: payment.tahun_ajaran,
                        payments: [],
                        total: 0
                    };
                }
                
                grouped[tahunId].payments.push(payment);
                grouped[tahunId].total += payment.sisa_tagihan;
            });
            
            return grouped;
        },

        // Get unique months for a specific tahun ajaran's payments
        getUniqueMonthsForTahun(payments) {
            const months = [...new Set(payments.filter(p => p.bulan).map(p => p.bulan))];
            return months.sort();
        },

        // Toggle month selection for tunggakan
        toggleMonthSelectionTunggakan(month, tahunId) {
            console.log('[toggleMonthSelectionTunggakan] Called for month:', month, 'tahunId:', tahunId);
            
            const monthPayments = this.tunggakanPayments.filter(p => 
                p.bulan === month && Number(p.tahun_ajaran_id) === Number(tahunId)
            );
            
            console.log('[toggleMonthSelectionTunggakan] Found payments:', monthPayments.length);
            console.log('[toggleMonthSelectionTunggakan] Payment IDs:', monthPayments.map(p => p.id));
            
            const allSelected = monthPayments.every(payment => 
                this.selectedPayments.includes(Number(payment.id))
            );
            
            console.log('[toggleMonthSelectionTunggakan] All selected:', allSelected);
            
            if (allSelected) {
                // Unselect all payments for this month
                monthPayments.forEach(payment => {
                    const paymentId = Number(payment.id);
                    const index = this.selectedPayments.indexOf(paymentId);
                    if (index > -1) {
                        this.selectedPayments.splice(index, 1);
                    }
                });
                console.log('[toggleMonthSelectionTunggakan] Unselected all');
            } else {
                // Select all payments for this month
                monthPayments.forEach(payment => {
                    const paymentId = Number(payment.id);
                    if (!this.selectedPayments.includes(paymentId)) {
                        this.selectedPayments.push(paymentId);
                    }
                });
                console.log('[toggleMonthSelectionTunggakan] Selected all');
            }
            
            console.log('[toggleMonthSelectionTunggakan] Final selectedPayments:', this.selectedPayments);
            this.updateSelectAll();
        },

        // Check if all payments in a month for specific tahun are selected
        isMonthSelectedTunggakan(month, tahunId) {
            const monthPayments = this.tunggakanPayments.filter(p => 
                p.bulan === month && p.tahun_ajaran_id === tahunId
            );
            
            if (monthPayments.length === 0) return false;
            
            return monthPayments.every(payment => 
                this.selectedPayments.includes(Number(payment.id))
            );
        },

        cetakKwitansi() {
            alert('Fitur cetak kwitansi belum tersedia');
            return;
        },

        toggleSelectAll() {
            console.log('[toggleSelectAll] Called. Current selectAll state:', this.selectAll, 'Active tab:', this.activeTab);
            console.log('[toggleSelectAll] selectedPayments BEFORE:', JSON.parse(JSON.stringify(this.selectedPayments)));
            
            if (this.selectAll) { // If the "select all" checkbox is NOW checked
                let paymentsToSelect;
                
                // Filter berdasarkan tab aktif
                if (this.activeTab === 'rutin') {
                    paymentsToSelect = this.payments
                        .filter(payment => payment.status !== 'lunas' && (payment.kategori_tagihan === 'Rutin' || payment.is_bulanan))
                        .map(payment => Number(payment.id));
                } else if (this.activeTab === 'insidentil') {
                    paymentsToSelect = this.payments
                        .filter(payment => payment.status !== 'lunas' && payment.kategori_tagihan === 'Insidental')
                        .map(payment => Number(payment.id));
                } else if (this.activeTab === 'tunggakan') {
                    paymentsToSelect = this.tunggakanPayments
                        .filter(payment => payment.status_pembayaran !== 'lunas')
                        .map(payment => Number(payment.id));
                } else {
                    // Default: select all unpaid payments
                    paymentsToSelect = this.payments
                        .filter(payment => payment.status !== 'lunas')
                        .map(payment => Number(payment.id));
                }
                
                this.selectedPayments = paymentsToSelect;
            } else { // If the "select all" checkbox is NOW unchecked
                this.selectedPayments.splice(0, this.selectedPayments.length); // Clears array in place
            }
            console.log('[toggleSelectAll] selectedPayments AFTER:', JSON.parse(JSON.stringify(this.selectedPayments)));
        },

        toggleMonthSelection(month, kategori = null) {
            // Filter pembayaran berdasarkan bulan dan kategori
            let monthPayments;
            
            if (kategori === 'rutin') {
                monthPayments = this.payments
                    .filter(payment => payment.bulan === month && payment.status !== 'lunas' && (payment.kategori_tagihan === 'Rutin' || payment.is_bulanan))
                    .map(payment => Number(payment.id));
            } else if (kategori === 'insidentil') {
                monthPayments = this.insidentilPayments
                    .filter(payment => payment.bulan === month && payment.status_pembayaran !== 'lunas')
                    .map(payment => Number(payment.id));
            } else {
                // Untuk backward compatibility
                monthPayments = this.payments
                    .filter(payment => payment.bulan === month && payment.status !== 'lunas')
                    .map(payment => payment.id);
            }
            
            // Periksa apakah semua pembayaran bulan ini sudah dipilih
            const allSelected = monthPayments.every(id => this.selectedPayments.includes(id));
            
            if (allSelected) {
                // Jika semua sudah dipilih, hapus semua dari seleksi
                this.selectedPayments = this.selectedPayments.filter(id => 
                    !monthPayments.includes(id)
                );
            } else {
                // Jika belum semua dipilih, tambahkan semua ke seleksi
                // Hapus dulu yang sudah ada untuk menghindari duplikasi
                const currentSelected = this.selectedPayments.filter(id => 
                    !monthPayments.includes(id)

                );
                this.selectedPayments = [...currentSelected, ...monthPayments];
            }
        },

        isMonthSelected(month, kategori = null) {
            // Filter pembayaran berdasarkan bulan dan kategori
            let monthPayments;
            
            if (kategori === 'rutin') {
                monthPayments = this.payments
                    .filter(payment => payment.bulan === month && payment.status !== 'lunas' && (payment.kategori_tagihan === 'Rutin' || payment.is_bulanan))
                    .map(payment => Number(payment.id));
            } else if (kategori === 'insidentil') {
                monthPayments = this.insidentilPayments
                    .filter(payment => payment.bulan === month && payment.status_pembayaran !== 'lunas')
                    .map(payment => Number(payment.id));
            } else {
                // Untuk backward compatibility
                monthPayments = this.payments
                    .filter(payment => payment.bulan === month && payment.status !== 'lunas')
                    .map(payment => Number(payment.id));
            }
            
            // Return true jika ada pembayaran dan semua dipilih
            return monthPayments.length > 0 && monthPayments.every(id => this.selectedPayments.includes(id));
        },

        hasMonthFullySelected() {
            // Daftar bulan yang tersedia
            const availableMonths = ['2024-07', '2024-08', '2024-09', '2024-10', '2024-11', '2024-12', '2025-01', '2025-02', '2025-03', '2025-04', '2025-05', '2025-06'];
            
            // Cek apakah ada bulan yang dipilih semua
            return availableMonths.some(month => {
                const monthPayments = this.payments
                    .filter(payment => payment.bulan === month && payment.status !== 'lunas')
                    .map(payment => Number(payment.id));
                
                // Jika tidak ada pembayaran yang belum lunas di bulan ini, skip
                if (monthPayments.length === 0) return false;
                
                // Periksa apakah semua pembayaran bulan ini sudah dipilih
                return monthPayments.every(id => this.selectedPayments.includes(id));
            });
        },

        bayarTerpilih() {
            if (this.selectedPayments.length === 0) {
                alert('Pilih tagihan yang akan dibayar');
                return;
            }
            
            this.showFullPaymentModal = true;
        },
        
        calculateChange() {
            if (!this.fullPaymentAmount || this.fullPaymentAmount <= 0) {
                this.fullPaymentChange = 0;
                return;
            }
            
            // Calculate change amount
            this.fullPaymentChange = this.fullPaymentAmount - this.totalSelectedAmount;
            
            // Reset save to wallet option if there's no change
            if (this.fullPaymentChange <= 0) {
                this.saveToWallet = false;
            }
        },
        
        async processFullPayment() {
            try {
                // Validate basic requirements
                if (!this.selectedSantri) {
                    await Swal.fire({
                        title: 'Error!',
                        text: 'Pilih santri terlebih dahulu',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                    return;
                }

                if (!this.selectedPayments || this.selectedPayments.length === 0) {
                    await Swal.fire({
                        title: 'Peringatan!',
                        text: 'Pilih tagihan yang akan dibayar terlebih dahulu',
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    });
                    return;
                }

                if (!this.fullPaymentAmount || this.fullPaymentAmount < this.totalSelectedAmount) {
                    await Swal.fire({
                        title: 'Peringatan!',
                        text: 'Jumlah pembayaran kurang dari total tagihan',
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    });
                    return;
                }
                
                // Show loading state
                const loadingSwal = Swal.fire({
                    title: 'Memproses Pembayaran...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                let message = '';
                const payments = [];
                
                // Determine data source based on active tab
                let dataSource = [];
                if (this.activeTab === 'tunggakan') {
                    dataSource = this.tunggakanPayments;
                } else if (this.activeTab === 'insidentil') {
                    dataSource = this.insidentilPayments;
                } else if (this.activeTab === 'lunas') {
                    dataSource = this.paidPayments;
                } else {
                    dataSource = this.payments; // default untuk tab rutin
                }
                
                console.log('[processFullPayment] Active tab:', this.activeTab);
                console.log('[processFullPayment] Data source length:', dataSource.length);
                console.log('[processFullPayment] Selected payments:', this.selectedPayments);
                
                // Process all selected payments
                for (const paymentId of this.selectedPayments) {
                    const id = Number(paymentId);
                    const payment = dataSource.find(p => Number(p.id) === id);
                    
                    console.log('[processFullPayment] Processing payment ID:', id, 'Found:', !!payment);
                    
                    if (payment) {
                        // Use the correct field name based on data source
                        const amountToPay = payment.sisa_tagihan || payment.sisa || 0;
                        
                        payments.push({
                            tagihan_santri_id: payment.id, // ini adalah ID dari TagihanSantri
                            jenis_tagihan_id: payment.jenis_tagihan_id,
                            jenis_tagihan: payment.jenis_tagihan,
                            nominal: amountToPay,
                            bulan: payment.bulan,
                            keterangan: `Pembayaran ${payment.jenis_tagihan} - ${payment.bulan}`
                        });
                        
                        message += `- ${payment.jenis_tagihan} (${payment.bulan}): ${this.formatRupiah(amountToPay)} (Lunas)\n`;
                    }
                }
                
                console.log('[processFullPayment] Prepared payments:', payments);
                
                // Validate that we have payments to process
                if (payments.length === 0) {
                    if (loadingSwal) {
                        loadingSwal.close();
                    }
                    await Swal.fire({
                        title: 'Error!',
                        text: 'Tidak ada pembayaran yang ditemukan untuk diproses',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                    return;
                }
                
                // Validate payment data structure
                for (const payment of payments) {
                    if (!payment.tagihan_santri_id || !payment.nominal || payment.nominal <= 0) {
                        await Swal.fire({
                            title: 'Error!',
                            text: 'Data pembayaran tidak valid: ' + JSON.stringify(payment),
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                        return;
                    }
                }

                const requestData = {
                    santri_id: this.selectedSantri.id,
                    payments: payments,
                    total_amount: this.totalSelectedAmount,
                    received_amount: this.fullPaymentAmount,
                    save_to_wallet: this.saveToWallet
                };
                
                console.log('[processFullPayment] Sending request data:', requestData);

                // Validate required URLs and CSRF token
                if (!window.pembayaranSantriProcessUrl) {
                    throw new Error('URL proses pembayaran tidak ditemukan');
                }
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                if (!csrfToken) {
                    throw new Error('CSRF token tidak ditemukan');
                }
                
                console.log('[processFullPayment] Using URL:', window.pembayaranSantriProcessUrl);
                console.log('[processFullPayment] Using CSRF token:', csrfToken.substring(0, 10) + '...');

                try {
                    // Send payment data to server
                    const response = await fetch(window.pembayaranSantriProcessUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(requestData)
                    });

                    const result = await response.json();
                    
                    console.log('[processFullPayment] Response status:', response.status);
                    console.log('[processFullPayment] Response data:', result);
                    
                    // Check for validation errors (422)
                    if (response.status === 422) {
                        console.error('[processFullPayment] Validation errors:', result.errors);
                        const errorMessage = result.errors ? 
                            Object.values(result.errors).flat().join(', ') : 
                            result.message || 'Validasi gagal';
                        throw new Error(errorMessage);
                    }
                    
                    // Check for other errors
                    if (!response.ok) {
                        console.error('[processFullPayment] HTTP error:', response.status, result);
                        throw new Error(result.message || `Server error: ${response.status}`);
                    }

                    // Close loading state
                    if (loadingSwal) {
                        loadingSwal.close();
                    }

                    if (result.success) {
                        // Refresh all payment data from server
                        await this.refreshAllPaymentData();

                        // Show success message with SweetAlert2
                        await Swal.fire({
                            title: 'Pembayaran Berhasil!',
                            html: `
                                <div class="text-left">
                                    <p class="mb-2"><strong>Detail Pembayaran:</strong></p>
                                    <div class="bg-gray-50 p-3 rounded text-sm">
                                        ${message.split('\n').filter(line => line.trim()).map(line => line + '<br>').join('')}
                                    </div>
                                    <p class="mt-2">
                                        <strong>Total Pembayaran:</strong> ${this.formatRupiah(this.totalSelectedAmount)}<br>
                                        <strong>Diterima:</strong> ${this.formatRupiah(this.fullPaymentAmount)}<br>
                                        <strong>Kembalian:</strong> ${this.formatRupiah(this.fullPaymentChange)}
                                        ${this.saveToWallet ? '<br><em class="text-green-600">(Disimpan ke dompet santri)</em>' : ''}
                                    </p>
                                </div>
                            `,
                            icon: 'success',
                            confirmButtonText: 'Tutup',
                            showCancelButton: true,
                            cancelButtonText: 'Cetak Kwitansi',
                            cancelButtonColor: '#6B7280',
                            reverseButtons: true
                        });

                        // Reset form state and switch to lunas tab
                        this.showFullPaymentModal = false;
                        this.fullPaymentAmount = 0;
                        this.fullPaymentChange =  0;
                        this.saveToWallet = false;
                        this.selectedPayments = [];
                        this.selectAll = false;
                        
                        // Switch to lunas tab to show paid items
                        this.activeTab = 'lunas';
                        
                        console.log('[processFullPayment] Payment successful, switched to lunas tab');
                    } else {
                        throw new Error(result.message || 'Gagal memproses pembayaran');
                    }
                } catch (error) {
                    console.error('Payment processing error:', error);
                    await Swal.fire({
                        title: 'Gagal!',
                        text: error.message || 'Terjadi kesalahan saat memproses pembayaran',
                        icon: 'error',
                        confirmButtonText: 'Tutup'
                    });
                }
            } catch (error) {
                console.error('Payment form error:', error);
                await Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan pada form pembayaran',
                    icon: 'error',
                    confirmButtonText: 'Tutup'
                });
            }
        },
        
        // Method untuk membuka modal pembayaran sebagian dan mengisi prioritas list
        openPartialPaymentModal() {
            // Determine data source based on active tab
            let dataSource = [];
            if (this.activeTab === 'tunggakan') {
                dataSource = this.tunggakanPayments;
            } else if (this.activeTab === 'insidentil') {
                dataSource = this.insidentilPayments;
            } else if (this.activeTab === 'lunas') {
                dataSource = this.paidPayments;
            } else {
                dataSource = this.payments; // default untuk tab rutin
            }
            
            // Populate paymentPriorityList dari selectedPayments
            this.paymentPriorityList = this.selectedPayments.map(paymentId => {
                const id = Number(paymentId);
                const payment = dataSource.find(p => Number(p.id) === id);
                if (payment) {
                    return {
                        id: payment.id,
                        jenis_tagihan: payment.jenis_tagihan,
                        jenis_tagihan_id: payment.jenis_tagihan_id,
                        bulan: payment.bulan,
                        kategori: payment.kategori || 'Tagihan Santri',
                        sisa: payment.sisa_tagihan || payment.sisa || 0,
                        willBePaidFull: false // Default tidak diprioritaskan
                    };
                }
                return null;
            }).filter(item => item !== null);
            
            // Reset calculated values
            this.calculatedFullPayment = 0;
            this.remainingAmount = 0;
            
            // Buka modal
            this.showPartialPaymentModal = true;
        },
        
        // Method untuk update kalkulasi pembayaran prioritas
        updatePaymentCalculation() {
            // Hitung total pembayaran yang akan dibayar lunas (prioritas)
            this.calculatedFullPayment = this.paymentPriorityList
                .filter(item => item.willBePaidFull)
                .reduce((total, item) => total + parseFloat(item.sisa || 0), 0);
            
            // Hitung sisa uang setelah bayar prioritas
            this.remainingAmount = Math.max(0, this.partialAmount - this.calculatedFullPayment);
        },
        
        // Method untuk kalkulasi saat input jumlah pembayaran berubah
        calculatePaymentPriority() {
            this.updatePaymentCalculation();
        },
        
        async processPartialPayment() {
            try {
                // Validate basic requirements
                if (!this.selectedSantri) {
                    await Swal.fire({
                        title: 'Error!',
                        text: 'Pilih santri terlebih dahulu',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                    return;
                }

                if (!this.selectedPayments || this.selectedPayments.length === 0) {
                    await Swal.fire({
                        title: 'Peringatan!',
                        text: 'Pilih tagihan yang akan dibayar terlebih dahulu',
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    });
                    return;
                }

                if (!this.partialAmount || this.partialAmount <= 0) {
                    await Swal.fire({
                        title: 'Peringatan!',
                        text: 'Masukkan jumlah pembayaran yang valid',
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    });
                    return;
                }

                if (this.partialAmount > this.totalSelectedAmount) {
                    await Swal.fire({
                        title: 'Peringatan!',
                        text: 'Jumlah pembayaran melebihi total tagihan',
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    });
                    return;
                }

                // Show loading state
                const loadingSwal = Swal.fire({
                    title: 'Memproses Pembayaran...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Determine data source based on active tab
                let dataSource = [];
                if (this.activeTab === 'tunggakan') {
                    dataSource = this.tunggakanPayments;
                } else if (this.activeTab === 'insidentil') {
                    dataSource = this.insidentilPayments;
                } else if (this.activeTab === 'lunas') {
                    dataSource = this.paidPayments;
                } else {
                    dataSource = this.payments; // default untuk tab rutin
                }

                let message = '';
                const payments = [];
                let remainingAmount = this.partialAmount;

                // Get prioritized payments (those marked by user)
                const prioritizedPayments = this.paymentPriorityList
                    .filter(item => item.willBePaidFull)
                    .map(item => item.id);
                
                // Get non-prioritized payments
                const nonPrioritizedPayments = this.paymentPriorityList
                    .filter(item => !item.willBePaidFull)
                    .map(item => item.id);

                // Process prioritized payments first
                for (const paymentId of prioritizedPayments) {
                    const id = Number(paymentId);
                    const payment = dataSource.find(p => Number(p.id) === id);
                    if (payment && remainingAmount > 0) {
                        const sisaTagihan = payment.sisa_tagihan || payment.sisa || 0;
                        const amountToPay = Math.min(sisaTagihan, remainingAmount);
                        
                        payments.push({
                            tagihan_santri_id: payment.id, // ini adalah ID dari TagihanSantri
                            jenis_tagihan_id: payment.jenis_tagihan_id,
                            jenis_tagihan: payment.jenis_tagihan,
                            nominal: amountToPay,
                            bulan: payment.bulan,
                            keterangan: `Pembayaran sebagian ${payment.jenis_tagihan} - ${payment.bulan}`
                        });
                        
                        message += `- ${payment.jenis_tagihan} (${payment.bulan}): ${this.formatRupiah(amountToPay)}\n`;
                        remainingAmount -= amountToPay;
                        
                        if (remainingAmount <= 0) break;
                    }
                }

                // If there's still money left, apply it to non-prioritized items
                if (remainingAmount > 0 && nonPrioritizedPayments.length > 0) {
                    // Sort non-prioritized payments (SPP first, then by amount)
                    const sortedNonPrioritized = [...nonPrioritizedPayments].sort((aId, bId) => {
                        const a = dataSource.find(p => Number(p.id) === Number(aId));
                        const b = dataSource.find(p => Number(p.id) === Number(bId));
                        
                        if (!a || !b) return 0;
                        
                        // SPP first
                        if (a.jenis_tagihan === 'SPP' && b.jenis_tagihan !== 'SPP') return -1;
                        if (a.jenis_tagihan !== 'SPP' && b.jenis_tagihan === 'SPP') return 1;
                        
                        // Then by amount (smaller first)
                        const aSisa = a.sisa_tagihan || a.sisa || 0;
                        const bSisa = b.sisa_tagihan || b.sisa || 0;
                        return aSisa - bSisa;
                    });

                    for (const paymentId of sortedNonPrioritized) {
                        const id = Number(paymentId);
                        const payment = dataSource.find(p => Number(p.id) === id);
                        if (payment && remainingAmount > 0) {
                            const sisaTagihan = payment.sisa_tagihan || payment.sisa || 0;
                            const amountToPay = Math.min(sisaTagihan, remainingAmount);
                            
                            payments.push({
                                tagihan_santri_id: payment.id, // ini adalah ID dari TagihanSantri
                                jenis_tagihan_id: payment.jenis_tagihan_id,
                                jenis_tagihan: payment.jenis_tagihan,
                                nominal: amountToPay,
                                bulan: payment.bulan,
                                keterangan: `Pembayaran sebagian ${payment.jenis_tagihan} - ${payment.bulan}`
                            });
                            
                            message += `- ${payment.jenis_tagihan} (${payment.bulan}): ${this.formatRupiah(amountToPay)}\n`;
                            remainingAmount -= amountToPay;
                            
                            if (remainingAmount <= 0) break;
                        }
                    }
                }

                // Validate that we have payments to process
                if (payments.length === 0) {
                    if (loadingSwal) {
                        loadingSwal.close();
                    }
                    await Swal.fire({
                        title: 'Error!',
                        text: 'Tidak ada pembayaran yang ditemukan untuk diproses',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                    return;
                }

                const requestData = {
                    santri_id: this.selectedSantri.id,
                    payments: payments,
                    total_amount: this.partialAmount,
                    received_amount: this.partialAmount,
                    save_to_wallet: false
                };
                
                console.log('[processPartialPayment] Sending request data:', requestData);

                // Validate CSRF token exists
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                if (!csrfToken) {
                    throw new Error('CSRF token tidak ditemukan');
                }

                try {
                    // Send payment data to server
                    const response = await fetch(window.pembayaranSantriProcessUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(requestData)
                    });

                    const result = await response.json();
                    
                    console.log('[processPartialPayment] Response status:', response.status);
                    console.log('[processPartialPayment] Response data:', result);

                    // Close loading state
                    if (loadingSwal) {
                        loadingSwal.close();
                    }
                    
                    // Check for validation errors (422)
                    if (response.status === 422) {
                        console.error('[processPartialPayment] Validation errors:', result.errors);
                        const errorMessage = result.errors ? 
                            Object.values(result.errors).flat().join(', ') : 
                            result.message || 'Validasi gagal';
                        throw new Error(errorMessage);
                    }
                    
                    // Check for other errors
                    if (!response.ok) {
                        console.error('[processPartialPayment] HTTP error:', response.status, result);
                        throw new Error(result.message || `Server error: ${response.status}`);
                    }

                    if (result.success) {
                        // Refresh all payment data from server
                        await this.refreshAllPaymentData();

                        // Show success message with SweetAlert2
                        await Swal.fire({
                            title: 'Pembayaran Sebagian Berhasil!',
                            html: `
                                <div class="text-left">
                                    <p class="mb-2"><strong>Detail Pembayaran:</strong></p>
                                    <div class="bg-gray-50 p-3 rounded text-sm">
                                        ${message.split('\n').filter(line => line.trim()).map(line => line + '<br>').join('')}
                                    </div>
                                    <p class="mt-2">
                                        <strong>Total Dibayar:</strong> ${this.formatRupiah(this.partialAmount)}
                                    </p>
                                    <div class="mt-3 text-sm text-yellow-600">
                                        <em>Catatan: Beberapa tagihan masih memiliki sisa yang perlu dibayar.</em>
                                    </div>
                                </div>
                            `,
                            icon: 'success',
                            confirmButtonText: 'Tutup',
                            showCancelButton: true,
                            cancelButtonText: 'Cetak Kwitansi',
                            cancelButtonColor: '#6B7280',
                            reverseButtons: true
                        });

                        // Reset the form and stay on current tab (since partial payment might leave remaining balance)
                        this.showPartialPaymentModal = false;
                        this.partialAmount = 0;
                        this.calculatedFullPayment = 0;
                        this.remainingAmount = 0;
                        this.paymentPriorityList = [];
                        this.selectedPayments = [];
                        this.selectAll = false;

                        console.log('[processPartialPayment] Partial payment successful, staying on current tab');
                        
                        // Note: We don't switch tabs for partial payments since there might be remaining balances
                    } else {
                        throw new Error(result.message || 'Gagal memproses pembayaran');
                    }
                } catch (error) {
                    console.error('Payment processing error:', error);
                    await Swal.fire({
                        title: 'Gagal!',
                        text: error.message || 'Terjadi kesalahan saat memproses pembayaran',
                        icon: 'error',
                        confirmButtonText: 'Tutup'
                    });
                }
            } catch (error) {
                console.error('Payment form error:', error);
                await Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan pada form pembayaran',
                    icon: 'error',
                    confirmButtonText: 'Tutup'
                });
            }
        },

        closePartialPaymentModal() {
            this.showPartialPaymentModal = false;
            this.partialAmount = 0;
            this.paymentPriorityList = [];
            this.calculatedFullPayment = 0;
            this.remainingAmount = 0;
        },

        formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        },

        generatePaymentDate(month) {
            const monthMap = {
                '2024-07': '2024-07',
                '2024-08': '2024-08',
                '2024-09': '2024-09',
                '2024-10': '2024-10',
                '2024-11': '2024-11',
                '2024-12': '2024-12',
                '2025-01': '2025-01',
                '2025-02': '2025-02',
                '2025-03': '2025-03',
                '2025-04': '2025-04',
                '2025-05': '2025-05',
                '2025-06': '2025-06'
            };
            
            const yearMonth = monthMap[month] || month;
            if (!yearMonth) return null;
            
            // Generate random day between 1-28 to avoid month-end issues
            const day = Math.floor(Math.random() * 28) + 1;
            const date = new Date(`${yearMonth}-${day.toString().padStart(2, '0')}`);
            
            return date.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        },

        getRandomAdmin() {
            const admins = [
                'Ahmad Fauzi',
                'Siti Nurhaliza',
                'Muhammad Rizki',
                'Fatimah Zahra',
                'Abdul Rahman',
                'Khadijah Amalia',
                'Usman Hakim',
                'Aisyah Putri'
            ];
            
            return admins[Math.floor(Math.random() * admins.length)];
        },

        deletePaidPayment(transaksiId) {
            if (!confirm('Yakin ingin menghapus pembayaran ini?')) return;
            fetch(`/keuangan/pembayaran-santri/paid/${transaksiId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Pembayaran berhasil dihapus!');
                    this.loadPaidPayments();
                    this.refreshAllPaymentData(); // pastikan tab lain juga update
                } else {
                    alert('Gagal menghapus pembayaran: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(err => alert('Gagal menghapus pembayaran: ' + err));
        }
    }
}
</script>
@endsection
