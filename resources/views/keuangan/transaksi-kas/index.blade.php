@extends('layouts.admin')

@section('title', 'Transaksi Kas')

@section('content')
<div class="container-fluid px-6 py-4">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-y-3">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Transaksi Kas</h2>
            <p class="text-slate-600 mt-1">Kelola pencatatan pemasukan, pengeluaran, dan transfer kas</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('keuangan.transaksi-kas.create', ['jenis' => 'pemasukan']) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                <span class="material-icons-outlined text-sm">add_circle</span>
                Pemasukan
            </a>
            <a href="{{ route('keuangan.transaksi-kas.create', ['jenis' => 'pengeluaran']) }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                <span class="material-icons-outlined text-sm">remove_circle</span>
                Pengeluaran
            </a>
            <a href="{{ route('keuangan.transaksi-kas.create', ['jenis' => 'transfer']) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                <span class="material-icons-outlined text-sm">swap_horiz</span>
                Transfer
            </a>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-md p-5 text-white relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-semibold mb-1 text-white/90">Total Pemasukan</h3>
                    <p class="text-2xl font-bold">Rp {{ number_format($stats['total_pemasukan'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <span class="material-icons-outlined text-3xl">trending_up</span>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 bg-white/10 rounded-full w-32 h-32"></div>
        </div>
        
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-md p-5 text-white relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-semibold mb-1 text-white/90">Total Pengeluaran</h3>
                    <p class="text-2xl font-bold">Rp {{ number_format($stats['total_pengeluaran'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <span class="material-icons-outlined text-3xl">trending_down</span>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 bg-white/10 rounded-full w-32 h-32"></div>
        </div>
        
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-md p-5 text-white relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-semibold mb-1 text-white/90">Transaksi Pending</h3>
                    <p class="text-2xl font-bold">{{ $stats['transaksi_pending'] }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <span class="material-icons-outlined text-3xl">pending_actions</span>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 bg-white/10 rounded-full w-32 h-32"></div>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-6">
        <h3 class="text-lg font-semibold mb-3 text-slate-800">Filter Transaksi</h3>
        <form method="GET" action="{{ route('keuangan.transaksi-kas.index') }}" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <div>
                <label for="jenis" class="block text-sm font-medium text-slate-700 mb-1">Jenis Transaksi</label>
                <select id="jenis" name="jenis" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">Semua Jenis</option>
                    <option value="pemasukan" {{ request('jenis') == 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                    <option value="pengeluaran" {{ request('jenis') == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                    <option value="transfer" {{ request('jenis') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                </select>
            </div>
            
            <div>
                <label for="buku_kas_id" class="block text-sm font-medium text-slate-700 mb-1">Buku Kas</label>
                <select id="buku_kas_id" name="buku_kas_id" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">Semua Buku Kas</option>
                    @foreach($bukuKasList as $bukuKas)
                    <option value="{{ $bukuKas->id }}" {{ request('buku_kas_id') == $bukuKas->id ? 'selected' : '' }}>{{ $bukuKas->nama_kas }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                <select id="status" name="status" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">Semua Status</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            
            <div>
                <label for="dari_tanggal" class="block text-sm font-medium text-slate-700 mb-1">Dari Tanggal</label>
                <input type="date" id="dari_tanggal" name="dari_tanggal" value="{{ request('dari_tanggal') }}" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
            
            <div>
                <label for="sampai_tanggal" class="block text-sm font-medium text-slate-700 mb-1">Sampai Tanggal</label>
                <input type="date" id="sampai_tanggal" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
            
            <div class="col-span-full md:col-span-2 lg:col-span-4">
                <label for="search" class="block text-sm font-medium text-slate-700 mb-1">Cari</label>
                <div class="relative">
                    <input type="text" id="search" name="search" placeholder="Cari kode, keterangan, atau referensi..." value="{{ request('search') }}" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm pl-10">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <span class="material-icons-outlined text-slate-400 text-lg">search</span>
                    </div>
                </div>
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center gap-1 transition">
                    <span class="material-icons-outlined text-sm">filter_alt</span> Filter
                </button>
                <a href="{{ route('keuangan.transaksi-kas.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-4 py-2 rounded flex items-center gap-1 transition">
                    <span class="material-icons-outlined text-sm">restart_alt</span> Reset
                </a>
            </div>
        </form>
    </div>
    
    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Kode</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Jenis</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Kategori</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Buku Kas</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Jumlah</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($transaksi as $item)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                            <a href="{{ route('keuangan.transaksi-kas.show', $item->id) }}" class="hover:text-blue-600">
                                {{ $item->kode_transaksi }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                            {{ $item->tanggal_transaksi->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $item->jenis_transaksi === 'pemasukan' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $item->jenis_transaksi === 'pengeluaran' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $item->jenis_transaksi === 'transfer' ? 'bg-blue-100 text-blue-800' : '' }}">
                                @if($item->jenis_transaksi === 'pemasukan')
                                <span class="material-icons-outlined text-xs">add_circle</span> Pemasukan
                                @elseif($item->jenis_transaksi === 'pengeluaran')
                                <span class="material-icons-outlined text-xs">remove_circle</span> Pengeluaran
                                @else
                                <span class="material-icons-outlined text-xs">swap_horiz</span> Transfer
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                            {{ $item->kategori }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                            {{ $item->bukuKas->nama_kas }}
                            @if($item->jenis_transaksi === 'transfer')
                            <span class="material-icons-outlined text-slate-400 text-xs">arrow_right_alt</span> 
                            {{ $item->bukuKasTujuan->nama_kas }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium
                            {{ $item->jenis_transaksi === 'pemasukan' ? 'text-green-600' : '' }}
                            {{ $item->jenis_transaksi === 'pengeluaran' ? 'text-red-600' : '' }}
                            {{ $item->jenis_transaksi === 'transfer' ? 'text-blue-600' : '' }}">
                            {{ $item->formatted_jumlah }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($item->status === 'approved')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <span class="material-icons-outlined text-xs">check_circle</span> Disetujui
                            </span>
                            @elseif($item->status === 'pending')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                <span class="material-icons-outlined text-xs">pending</span> Pending
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <span class="material-icons-outlined text-xs">cancel</span> Ditolak
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('keuangan.transaksi-kas.show', $item->id) }}" class="text-blue-600 hover:text-blue-900 p-1">
                                    <span class="material-icons-outlined text-lg">visibility</span>
                                </a>
                                
                                @if($item->status === 'pending')
                                <a href="{{ route('keuangan.transaksi-kas.edit', $item->id) }}" class="text-amber-600 hover:text-amber-900 p-1">
                                    <span class="material-icons-outlined text-lg">edit</span>
                                </a>
                                
                                <button type="button" 
                                    onclick="confirmApprove('{{ route('keuangan.transaksi-kas.approve', $item->id) }}')"
                                    class="text-green-600 hover:text-green-900 p-1">
                                    <span class="material-icons-outlined text-lg">check_circle</span>
                                </button>
                                
                                <button type="button" 
                                    onclick="confirmReject('{{ route('keuangan.transaksi-kas.reject', $item->id) }}')"
                                    class="text-red-600 hover:text-red-900 p-1">
                                    <span class="material-icons-outlined text-lg">cancel</span>
                                </button>
                                
                                <button type="button" 
                                    onclick="confirmDelete('{{ route('keuangan.transaksi-kas.destroy', $item->id) }}')"
                                    class="text-red-600 hover:text-red-900 p-1">
                                    <span class="material-icons-outlined text-lg">delete</span>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center gap-1">
                                <span class="material-icons-outlined text-4xl text-slate-300">receipt_long</span>
                                <p class="mt-2">Belum ada transaksi kas.</p>
                                <div class="mt-3 flex gap-2">
                                    <a href="{{ route('keuangan.transaksi-kas.create', ['jenis' => 'pemasukan']) }}" class="text-blue-600 hover:text-blue-800">Tambah Pemasukan</a>
                                    <span class="text-slate-300">|</span>
                                    <a href="{{ route('keuangan.transaksi-kas.create', ['jenis' => 'pengeluaran']) }}" class="text-blue-600 hover:text-blue-800">Tambah Pengeluaran</a>
                                    <span class="text-slate-300">|</span>
                                    <a href="{{ route('keuangan.transaksi-kas.create', ['jenis' => 'transfer']) }}" class="text-blue-600 hover:text-blue-800">Tambah Transfer</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $transaksi->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(url) {
        Swal.fire({
            title: 'Hapus Transaksi?',
            text: "Anda yakin ingin menghapus transaksi ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
    
    function confirmApprove(url) {
        Swal.fire({
            title: 'Setujui Transaksi?',
            text: "Transaksi yang disetujui akan mempengaruhi saldo kas",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Setujui!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.innerHTML = `@csrf`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
    
    function confirmReject(url) {
        Swal.fire({
            title: 'Tolak Transaksi?',
            text: "Masukkan alasan penolakan",
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off',
                placeholder: 'Alasan penolakan...'
            },
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Tolak Transaksi',
            cancelButtonText: 'Batal',
            showLoaderOnConfirm: true,
            preConfirm: (alasan) => {
                if (!alasan) {
                    Swal.showValidationMessage('Alasan penolakan wajib diisi');
                }
                return alasan;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.innerHTML = `
                    @csrf
                    <input type="hidden" name="alasan_penolakan" value="${result.value}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endpush
