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
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama/Pemohon</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Admin</th>
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                            @if($item->tagihanSantri && $item->tagihanSantri->santri)
                                {{ $item->tagihanSantri->santri->nama_santri }}
                            @elseif($item->nama_pemohon)
                                {{ $item->nama_pemohon }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                            @if($item->creator)
                                {{ $item->creator->name }}
                            @else
                                -
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
                                <a href="{{ route('keuangan.transaksi-kas.show', $item->id) }}" class="text-blue-600 hover:text-blue-900 p-1" title="Lihat Detail">
                                    <span class="material-icons-outlined text-lg">visibility</span>
                                </a>
                                
                                <!-- Tombol Edit -->
                                <button type="button" 
                                    onclick="editTransaksi('{{ $item->id }}')"
                                    class="text-amber-600 hover:text-amber-900 p-1" 
                                    title="Edit Transaksi">
                                    <span class="material-icons-outlined text-lg">edit</span>
                                </button>

                                <!-- Tombol Approve, Reject, dan Delete hanya untuk status pending -->
                                @if($item->status === 'pending')
                                <button type="button" 
                                    onclick="confirmApprove('{{ route('keuangan.transaksi-kas.approve', $item->id) }}')"
                                    class="text-green-600 hover:text-green-900 p-1"
                                    title="Setujui">
                                    <span class="material-icons-outlined text-lg">check_circle</span>
                                </button>
                                
                                <button type="button" 
                                    onclick="confirmReject('{{ route('keuangan.transaksi-kas.reject', $item->id) }}')"
                                    class="text-red-600 hover:text-red-900 p-1"
                                    title="Tolak">
                                    <span class="material-icons-outlined text-lg">cancel</span>
                                </button>
                                
                                <button type="button" 
                                    onclick="confirmDelete('{{ route('keuangan.transaksi-kas.destroy', $item->id) }}')"
                                    class="text-red-600 hover:text-red-900 p-1"
                                    title="Hapus">
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

<!-- Edit Transaksi Modal -->
<div id="editTransaksiModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Edit Transaksi Kas</h3>
            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-icons-outlined">close</span>
            </button>
        </div>
        
        <form id="editTransaksiForm" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Buku Kas -->
                <div>
                    <label for="edit_buku_kas_id" class="block text-sm font-medium text-gray-700 mb-1">Buku Kas <span class="text-red-500">*</span></label>
                    <select id="edit_buku_kas_id" name="buku_kas_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">Pilih Buku Kas</option>
                        @forelse($bukuKasList as $buku)
                        <option value="{{ $buku->id }}">{{ $buku->nama_kas }}</option>
                        @empty
                        <option value="" disabled>Tidak ada buku kas tersedia</option>
                        @endforelse
                    </select>
                </div>
                
                <!-- Kategori -->
                <div>
                    <label for="edit_kategori" class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                    <input type="text" id="edit_kategori" name="kategori" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>
                
                <!-- Jumlah -->
                <div>
                    <label for="edit_jumlah" class="block text-sm font-medium text-gray-700 mb-1">Jumlah (Rp) <span class="text-red-500">*</span></label>
                    <input type="text" id="edit_jumlah" name="jumlah" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>
                
                <!-- Tanggal Transaksi -->
                <div>
                    <label for="edit_tanggal_transaksi" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Transaksi <span class="text-red-500">*</span></label>
                    <input type="date" id="edit_tanggal_transaksi" name="tanggal_transaksi" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>
                
                <!-- Metode Pembayaran -->
                <div>
                    <label for="edit_metode_pembayaran" class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran <span class="text-red-500">*</span></label>
                    <select id="edit_metode_pembayaran" name="metode_pembayaran" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">Pilih Metode</option>
                        <option value="Tunai">Tunai</option>
                        <option value="Transfer">Transfer</option>
                        <option value="Kartu Debit">Kartu Debit</option>
                        <option value="Kartu Kredit">Kartu Kredit</option>
                        <option value="E-Wallet">E-Wallet</option>
                    </select>
                </div>
                
                <!-- No. Referensi -->
                <div>
                    <label for="edit_no_referensi" class="block text-sm font-medium text-gray-700 mb-1">No. Referensi (Opsional)</label>
                    <input type="text" id="edit_no_referensi" name="no_referensi" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Nomor cek, transfer, dll">
                </div>
                
                <!-- Nama Pemohon -->
                <div>
                    <label for="edit_nama_pemohon" class="block text-sm font-medium text-gray-700 mb-1">Nama Pemohon (Opsional)</label>
                    <input type="text" id="edit_nama_pemohon" name="nama_pemohon" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Nama orang yang mengajukan">
                </div>
            </div>
            
            <!-- Keterangan -->
            <div class="mt-4">
                <label for="edit_keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan (Opsional)</label>
                <textarea id="edit_keterangan" name="keterangan" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Rincian atau catatan tambahan..."></textarea>
            </div>
            
            <!-- Hidden field for raw amount value -->
            <input type="hidden" id="edit_jumlah_raw" name="jumlah_raw">
            
            <!-- Buttons -->
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
                    Update Transaksi
                </button>
            </div>
        </form>
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
    
    function editTransaksi(id) {
        // Show modal
        document.getElementById('editTransaksiModal').classList.remove('hidden');
        
        // Show loading state
        Swal.fire({
            title: 'Memuat data...',
            didOpen: () => {
                Swal.showLoading();
            },
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            showConfirmButton: false
        });
        
        // Fetch transaksi data
        fetch(`/api/keuangan/transaksi-kas/${id}`)
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.success && data.data) {
                    const transaksi = data.data;
                    
                    if (!transaksi.id) {
                        Swal.fire('Error', 'Data transaksi tidak valid', 'error');
                        closeEditModal();
                        return;
                    }
                    
                    try {
                        // Set form action
                        document.getElementById('editTransaksiForm').action = `/keuangan/transaksi-kas/${id}`;
                        
                        // Fill form fields
                        document.getElementById('edit_buku_kas_id').value = transaksi.buku_kas_id || '';
                        document.getElementById('edit_kategori').value = transaksi.kategori || '';
                        document.getElementById('edit_jumlah').value = transaksi.jumlah ? formatRupiah(transaksi.jumlah) : '';
                        document.getElementById('edit_tanggal_transaksi').value = transaksi.tanggal_transaksi ? transaksi.tanggal_transaksi.split('T')[0] : '';
                        document.getElementById('edit_metode_pembayaran').value = transaksi.metode_pembayaran || '';
                        document.getElementById('edit_no_referensi').value = transaksi.no_referensi || '';
                        document.getElementById('edit_nama_pemohon').value = transaksi.nama_pemohon || '';
                        document.getElementById('edit_keterangan').value = transaksi.keterangan || '';
                    } catch (err) {
                        console.error('Error setting form values:', err);
                        Swal.fire('Error', 'Gagal mengisi form dengan data transaksi', 'error');
                        closeEditModal();
                    }
                } else {
                    Swal.fire('Error', 'Gagal memuat data transaksi', 'error');
                    closeEditModal();
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error:', error);
                Swal.fire('Error', 'Terjadi kesalahan saat memuat data', 'error');
                closeEditModal();
            });
    }
    
    function closeEditModal() {
        // Hide modal
        document.getElementById('editTransaksiModal').classList.add('hidden');
        
        // Reset form
        document.getElementById('editTransaksiForm').reset();
    }
    
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }
    
    // Initialize event listeners after DOM is fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Format currency input in edit modal
        const editJumlahInput = document.getElementById('edit_jumlah');
        if (editJumlahInput) {
            editJumlahInput.addEventListener('input', function() {
                let value = this.value.replace(/[^\d]/g, '');
                this.value = formatRupiah(value);
            });
        }
        
        // Handle form submission
        const editForm = document.getElementById('editTransaksiForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Process amount value - remove formatting
                const jumlahInput = document.getElementById('edit_jumlah');
                const jumlahRawInput = document.getElementById('edit_jumlah_raw');
                const rawValue = jumlahInput.value.replace(/[^\d]/g, '');
                
                jumlahRawInput.value = rawValue;
                
                // Show loading
                Swal.fire({
                    title: 'Menyimpan perubahan...',
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    showConfirmButton: false
                });
                
                // Submit form with AJAX to prevent page reload
                fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Data transaksi berhasil diperbarui',
                            showConfirmButton: true
                        }).then(() => {
                            // Reload page to show updated data
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Terjadi kesalahan saat menyimpan data'
                        });
                    }
                })
                .catch(error => {
                    Swal.close();
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat memproses permintaan'
                    });
                });
            });
        }
    });
</script>
@endpush
