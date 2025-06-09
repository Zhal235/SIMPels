@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Detail Dompet Santri</h1>
            <p class="text-gray-600 mt-2">{{ $dompet->nama_pemilik }}</p>
        </div>
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            <a href="{{ route('keuangan.dompet.santri.top-up.form', $dompet->id) }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                <span class="material-icons-outlined text-sm mr-2">add_circle</span>
                Top Up
            </a>
            <a href="{{ route('keuangan.dompet.santri.index') }}" 
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                <span class="material-icons-outlined text-sm mr-2">arrow_back</span>
                Kembali
            </a>
        </div>
    </div>

    <!-- Dompet Info -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Info Dompet -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Dompet</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Pemilik</label>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $dompet->nama_pemilik }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">NIS</label>
                        <p class="mt-1 text-lg text-gray-900">{{ $dompet->santri->nis ?? '-' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Nomor Dompet</label>
                        <p class="mt-1 text-lg font-mono text-gray-900">{{ $dompet->nomor_dompet }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Status</label>
                        <span class="mt-1 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $dompet->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $dompet->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Limit Transaksi</label>
                        <p class="mt-1 text-lg text-gray-900">
                            {{ $dompet->limit_transaksi ? 'Rp ' . number_format($dompet->limit_transaksi, 0, ',', '.') : 'Tidak terbatas' }}
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Dibuat</label>
                        <p class="mt-1 text-lg text-gray-900">{{ $dompet->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Saldo Card -->
        <div class="space-y-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-sm p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Saldo Dompet</p>
                        <p class="text-3xl font-bold">Rp {{ number_format($dompet->saldo, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-full">
                        <span class="material-icons-outlined text-2xl">account_balance_wallet</span>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistik Transaksi</h3>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Transaksi</span>
                        <span class="font-semibold">{{ $dompet->transaksiDompet->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Top Up</span>
                        <span class="font-semibold text-green-600">{{ $dompet->transaksiDompet->where('jenis_transaksi', 'top_up')->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Pembelian</span>
                        <span class="font-semibold text-red-600">{{ $dompet->transaksiDompet->where('jenis_transaksi', 'pembelian')->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Transaksi -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Riwayat Transaksi</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kode Transaksi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jenis
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kategori
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jumlah
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Saldo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transaksi as $t)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $t->tanggal_transaksi->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 font-mono">
                                    {{ $t->kode_transaksi }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="material-icons-outlined text-sm mr-2" style="color: {{ $t->color }}">{{ $t->icon }}</span>
                                    <span class="text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $t->jenis_transaksi) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $t->kategori }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm font-bold {{ in_array($t->jenis_transaksi, ['top_up', 'transfer_masuk']) ? 'text-green-600' : 'text-red-600' }}">
                                    {{ in_array($t->jenis_transaksi, ['top_up', 'transfer_masuk']) ? '+' : '-' }} Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                Rp {{ number_format($t->saldo_sesudah, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $t->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                       ($t->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($t->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="material-icons-outlined text-gray-400 text-5xl mb-3">receipt</span>
                                    <p class="text-gray-500 text-lg">Belum ada transaksi</p>
                                    <p class="text-gray-400">Lakukan top up untuk memulai</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($transaksi->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $transaksi->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
