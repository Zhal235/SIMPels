@extends('layouts.admin')

@section('title', 'Detail Transaksi Kas')

@section('content')
<div class="container-fluid px-6 py-4">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Detail Transaksi Kas</h2>
            <p class="text-slate-600 mt-1">Informasi lengkap transaksi kas</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('keuangan.transaksi-kas.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-4 py-2 rounded-lg flex items-center gap-1 transition">
                <span class="material-icons-outlined text-sm">arrow_back</span>
                Kembali
            </a>
            
            @if($transaksi->status === 'pending')
            <a href="{{ route('keuangan.transaksi-kas.edit', $transaksi->id) }}" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg flex items-center gap-1 transition">
                <span class="material-icons-outlined text-sm">edit</span>
                Edit
            </a>
            @endif
        </div>
    </div>
    
    <!-- Status Cards -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                <!-- Jenis Transaksi Badge -->
                <div class="px-4 py-2 rounded-lg
                    {{ $transaksi->jenis_transaksi === 'pemasukan' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $transaksi->jenis_transaksi === 'pengeluaran' ? 'bg-red-100 text-red-800' : '' }}
                    {{ $transaksi->jenis_transaksi === 'transfer' ? 'bg-blue-100 text-blue-800' : '' }}">
                    <div class="flex items-center gap-2">
                        @if($transaksi->jenis_transaksi === 'pemasukan')
                            <span class="material-icons-outlined">add_circle</span>
                            <span class="font-semibold">Pemasukan</span>
                        @elseif($transaksi->jenis_transaksi === 'pengeluaran')
                            <span class="material-icons-outlined">remove_circle</span>
                            <span class="font-semibold">Pengeluaran</span>
                        @else
                            <span class="material-icons-outlined">swap_horiz</span>
                            <span class="font-semibold">Transfer</span>
                        @endif
                    </div>
                </div>
                
                <!-- Kode Transaksi Badge -->
                <div class="px-4 py-2 bg-slate-100 text-slate-800 rounded-lg">
                    <div class="flex items-center gap-2">
                        <span class="material-icons-outlined">receipt</span>
                        <span class="font-semibold">{{ $transaksi->kode_transaksi }}</span>
                    </div>
                </div>
                
                <!-- Status Badge -->
                <div class="px-4 py-2 rounded-lg
                    {{ $transaksi->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $transaksi->status === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                    {{ $transaksi->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                    <div class="flex items-center gap-2">
                        @if($transaksi->status === 'approved')
                            <span class="material-icons-outlined">check_circle</span>
                            <span class="font-semibold">Disetujui</span>
                        @elseif($transaksi->status === 'pending')
                            <span class="material-icons-outlined">pending</span>
                            <span class="font-semibold">Pending</span>
                        @else
                            <span class="material-icons-outlined">cancel</span>
                            <span class="font-semibold">Ditolak</span>
                        @endif
                    </div>
                </div>
            </div>
            
            <div>
                <div class="text-3xl font-bold 
                    {{ $transaksi->jenis_transaksi === 'pemasukan' ? 'text-green-600' : '' }}
                    {{ $transaksi->jenis_transaksi === 'pengeluaran' ? 'text-red-600' : '' }}
                    {{ $transaksi->jenis_transaksi === 'transfer' ? 'text-blue-600' : '' }}">
                    {{ $transaksi->formatted_jumlah }}
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Transaction Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Transaction Details -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-semibold mb-4 pb-2 border-b border-slate-200 text-slate-800">Informasi Transaksi</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <!-- Buku Kas -->
                    <div>
                        <div class="text-sm text-slate-500 mb-1">
                            @if($transaksi->jenis_transaksi === 'transfer')
                                Buku Kas Sumber
                            @else
                                Buku Kas
                            @endif
                        </div>
                        <div class="font-medium text-slate-900">{{ $transaksi->bukuKas->nama_kas }}</div>
                    </div>
                    
                    <!-- Buku Kas Tujuan (for transfer only) -->
                    @if($transaksi->jenis_transaksi === 'transfer')
                    <div>
                        <div class="text-sm text-slate-500 mb-1">Buku Kas Tujuan</div>
                        <div class="font-medium text-slate-900">{{ $transaksi->bukuKasTujuan->nama_kas }}</div>
                    </div>
                    @endif
                    
                    <!-- Kategori -->
                    <div>
                        <div class="text-sm text-slate-500 mb-1">Kategori</div>
                        <div class="font-medium text-slate-900">{{ $transaksi->kategori }}</div>
                    </div>
                    
                    <!-- Tanggal -->
                    <div>
                        <div class="text-sm text-slate-500 mb-1">Tanggal Transaksi</div>
                        <div class="font-medium text-slate-900">{{ $transaksi->tanggal_transaksi->format('d F Y') }}</div>
                    </div>
                    
                    <!-- Metode Pembayaran -->
                    <div>
                        <div class="text-sm text-slate-500 mb-1">Metode Transaksi</div>
                        <div class="font-medium text-slate-900">{{ $transaksi->metode_pembayaran }}</div>
                    </div>
                    
                    <!-- No Referensi -->
                    <div>
                        <div class="text-sm text-slate-500 mb-1">No. Referensi</div>
                        <div class="font-medium text-slate-900">{{ $transaksi->no_referensi ?? '-' }}</div>
                    </div>
                    
                    <!-- Created At -->
                    <div>
                        <div class="text-sm text-slate-500 mb-1">Tanggal Input</div>
                        <div class="font-medium text-slate-900">{{ $transaksi->created_at->format('d F Y H:i') }}</div>
                    </div>
                    
                    <!-- Created By -->
                    <div>
                        <div class="text-sm text-slate-500 mb-1">Dibuat Oleh</div>
                        <div class="font-medium text-slate-900">
                            {{ $transaksi->creator->name ?? '-' }}
                        </div>
                    </div>
                    
                    @if($transaksi->status === 'approved' && $transaksi->approved_by)
                    <!-- Approved By -->
                    <div>
                        <div class="text-sm text-slate-500 mb-1">Disetujui Oleh</div>
                        <div class="font-medium text-slate-900">
                            {{ $transaksi->approver->name ?? '-' }}
                        </div>
                    </div>
                    @endif
                </div>
                
                <!-- Keterangan -->
                <div class="mt-6">
                    <div class="text-sm text-slate-500 mb-1">Keterangan</div>
                    <div class="bg-slate-50 rounded-md p-3 text-slate-800 whitespace-pre-wrap">{{ $transaksi->keterangan ?? '-' }}</div>
                </div>
            </div>
        </div>
        
        <!-- Right Column - Actions & Bukti -->
        <div class="space-y-6">
            <!-- Actions Card -->
            @if($transaksi->status === 'pending')
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-semibold mb-4 pb-2 border-b border-slate-200 text-slate-800">Tindakan</h3>
                
                <div class="space-y-3">
                    <button type="button" onclick="confirmApprove('{{ route('keuangan.transaksi-kas.approve', $transaksi->id) }}')" 
                        class="w-full py-2 px-4 bg-green-600 hover:bg-green-700 text-white rounded-md transition flex items-center justify-center gap-2">
                        <span class="material-icons-outlined">check_circle</span>
                        Setujui Transaksi
                    </button>
                    
                    <button type="button" onclick="confirmReject('{{ route('keuangan.transaksi-kas.reject', $transaksi->id) }}')" 
                        class="w-full py-2 px-4 bg-red-600 hover:bg-red-700 text-white rounded-md transition flex items-center justify-center gap-2">
                        <span class="material-icons-outlined">cancel</span>
                        Tolak Transaksi
                    </button>
                    
                    <button type="button" onclick="confirmDelete('{{ route('keuangan.transaksi-kas.destroy', $transaksi->id) }}')" 
                        class="w-full py-2 px-4 bg-slate-600 hover:bg-slate-700 text-white rounded-md transition flex items-center justify-center gap-2">
                        <span class="material-icons-outlined">delete</span>
                        Hapus Transaksi
                    </button>
                </div>
            </div>
            @endif
            
            <!-- Bukti Transaksi Card -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-semibold mb-4 pb-2 border-b border-slate-200 text-slate-800">Bukti Transaksi</h3>
                
                @if($transaksi->bukti_transaksi)
                    <div class="flex flex-col items-center">
                        @php
                            $extension = pathinfo(storage_path('app/public/' . $transaksi->bukti_transaksi), PATHINFO_EXTENSION);
                            $isPDF = strtolower($extension) === 'pdf';
                        @endphp
                        
                        @if($isPDF)
                            <div class="mb-3 bg-red-100 text-red-800 p-3 rounded-lg flex items-center gap-2">
                                <span class="material-icons-outlined">picture_as_pdf</span>
                                <span>File PDF</span>
                            </div>
                            <a href="{{ asset('storage/' . $transaksi->bukti_transaksi) }}" target="_blank" 
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition flex items-center gap-1">
                                <span class="material-icons-outlined">visibility</span>
                                Lihat PDF
                            </a>
                        @else
                            <div class="border border-slate-200 rounded-lg p-1 mb-3 max-w-full">
                                <img src="{{ asset('storage/' . $transaksi->bukti_transaksi) }}" alt="Bukti Transaksi" class="max-w-full h-auto rounded max-h-80">
                            </div>
                            <a href="{{ asset('storage/' . $transaksi->bukti_transaksi) }}" target="_blank" 
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition flex items-center gap-1">
                                <span class="material-icons-outlined">zoom_in</span>
                                Lihat Bukti
                            </a>
                        @endif
                    </div>
                @else
                    <div class="bg-slate-100 rounded-lg p-6 flex flex-col items-center justify-center text-slate-500">
                        <span class="material-icons-outlined text-3xl mb-2">no_photography</span>
                        <p>Tidak ada bukti transaksi</p>
                    </div>
                @endif
            </div>
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
