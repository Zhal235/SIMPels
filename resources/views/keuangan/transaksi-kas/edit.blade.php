@extends('layouts.admin')

@section('title', 'Edit Transaksi Kas')

@section('content')
<div class="container-fluid px-6 py-4">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Edit Transaksi Kas</h2>
        <p class="text-slate-600 mt-1">Perbarui informasi transaksi kas</p>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <form action="{{ route('keuangan.transaksi-kas.update', $transaksi->id) }}" method="POST" enctype="multipart/form-data" class="max-w-4xl">
            @csrf
            @method('PUT')
            
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="px-3 py-1.5 rounded-lg 
                            {{ $transaksi->jenis_transaksi === 'pemasukan' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $transaksi->jenis_transaksi === 'pengeluaran' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $transaksi->jenis_transaksi === 'transfer' ? 'bg-blue-100 text-blue-800' : '' }}
                        ">
                            <span class="text-sm font-medium">
                                @if($transaksi->jenis_transaksi === 'pemasukan')
                                    Pemasukan
                                @elseif($transaksi->jenis_transaksi === 'pengeluaran')
                                    Pengeluaran
                                @else
                                    Transfer
                                @endif
                            </span>
                        </div>
                        
                        <div class="px-3 py-1.5 bg-slate-100 text-slate-800 rounded-lg">
                            <span class="text-sm font-medium">{{ $transaksi->kode_transaksi }}</span>
                        </div>
                        
                        <div class="px-3 py-1.5 rounded-lg
                            {{ $transaksi->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $transaksi->status === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                            {{ $transaksi->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                        ">
                            <span class="text-sm font-medium">
                                @if($transaksi->status === 'approved')
                                    Disetujui
                                @elseif($transaksi->status === 'pending')
                                    Pending
                                @else
                                    Ditolak
                                @endif
                            </span>
                        </div>
                    </div>
                    
                    <div class="text-sm text-slate-500">
                        <span class="inline-block mr-2">Dibuat: {{ $transaksi->created_at->format('d M Y H:i') }}</span>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Buku Kas Info -->
                <div>
                    <label class="block mb-1 text-sm font-medium text-slate-700">
                        @if($transaksi->jenis_transaksi === 'transfer')
                            Buku Kas Sumber
                        @else
                            Buku Kas
                        @endif
                    </label>
                    <div class="p-3 bg-slate-100 rounded-md text-slate-800 text-sm">
                        {{ $transaksi->bukuKas->nama_kas }}
                    </div>
                    <input type="hidden" name="buku_kas_id" value="{{ $transaksi->buku_kas_id }}">
                </div>
                
                <!-- Buku Kas Tujuan Info (for transfer only) -->
                @if($transaksi->jenis_transaksi === 'transfer')
                <div>
                    <label class="block mb-1 text-sm font-medium text-slate-700">
                        Buku Kas Tujuan
                    </label>
                    <div class="p-3 bg-slate-100 rounded-md text-slate-800 text-sm">
                        {{ $transaksi->bukuKasTujuan->nama_kas }}
                    </div>
                    <input type="hidden" name="buku_kas_tujuan_id" value="{{ $transaksi->buku_kas_tujuan_id }}">
                </div>
                @endif
                
                <!-- Kategori -->
                <div class="{{ $transaksi->jenis_transaksi !== 'transfer' ? 'md:col-span-2' : '' }}">
                    <label for="kategori" class="block mb-1 text-sm font-medium text-slate-700">
                        Kategori
                        <span class="text-red-500">*</span>
                    </label>
                    <select id="kategori" name="kategori" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        <option value="">Pilih Kategori</option>
                        
                        @if($transaksi->jenis_transaksi === 'pemasukan')
                            <option value="Pembayaran Santri" {{ $transaksi->kategori === 'Pembayaran Santri' ? 'selected' : '' }}>Pembayaran Santri</option>
                            <option value="Sumbangan" {{ $transaksi->kategori === 'Sumbangan' ? 'selected' : '' }}>Sumbangan</option>
                            <option value="Hibah" {{ $transaksi->kategori === 'Hibah' ? 'selected' : '' }}>Hibah</option>
                            <option value="Dana Bantuan" {{ $transaksi->kategori === 'Dana Bantuan' ? 'selected' : '' }}>Dana Bantuan</option>
                            <option value="Lainnya" {{ $transaksi->kategori === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        @elseif($transaksi->jenis_transaksi === 'pengeluaran')
                            <option value="Operasional" {{ $transaksi->kategori === 'Operasional' ? 'selected' : '' }}>Operasional</option>
                            <option value="Gaji" {{ $transaksi->kategori === 'Gaji' ? 'selected' : '' }}>Gaji</option>
                            <option value="Pemeliharaan" {{ $transaksi->kategori === 'Pemeliharaan' ? 'selected' : '' }}>Pemeliharaan</option>
                            <option value="Pembangunan" {{ $transaksi->kategori === 'Pembangunan' ? 'selected' : '' }}>Pembangunan</option>
                            <option value="ATK" {{ $transaksi->kategori === 'ATK' ? 'selected' : '' }}>ATK</option>
                            <option value="Konsumsi" {{ $transaksi->kategori === 'Konsumsi' ? 'selected' : '' }}>Konsumsi</option>
                            <option value="Lainnya" {{ $transaksi->kategori === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        @else
                            <option value="Transfer Kas" {{ $transaksi->kategori === 'Transfer Kas' ? 'selected' : '' }}>Transfer Kas</option>
                            <option value="Penyesuaian Saldo" {{ $transaksi->kategori === 'Penyesuaian Saldo' ? 'selected' : '' }}>Penyesuaian Saldo</option>
                            <option value="Lainnya" {{ $transaksi->kategori === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        @endif
                    </select>
                    @error('kategori')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Jumlah (readonly) -->
                <div class="md:col-span-2">
                    <label class="block mb-1 text-sm font-medium text-slate-700">
                        Jumlah
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <span class="text-slate-500">Rp</span>
                        </div>
                        <input type="text" 
                               class="block w-full pl-12 pr-12 rounded-md border-slate-300 bg-slate-100 text-slate-800 sm:text-sm"
                               value="{{ number_format($transaksi->jumlah, 0, ',', '.') }}" 
                               readonly>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">Jumlah tidak dapat diubah setelah transaksi dibuat</p>
                </div>
                
                <!-- Tanggal Transaksi (readonly) -->
                <div>
                    <label class="block mb-1 text-sm font-medium text-slate-700">
                        Tanggal Transaksi
                    </label>
                    <input type="text"
                           class="block w-full rounded-md border-slate-300 bg-slate-100 text-slate-800 sm:text-sm"
                           value="{{ $transaksi->tanggal_transaksi->format('d/m/Y') }}" 
                           readonly>
                </div>
                
                <!-- Metode Pembayaran -->
                <div>
                    <label for="metode_pembayaran" class="block mb-1 text-sm font-medium text-slate-700">
                        Metode Transaksi
                        <span class="text-red-500">*</span>
                    </label>
                    <select id="metode_pembayaran" name="metode_pembayaran" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        <option value="">Pilih Metode</option>
                        <option value="Tunai" {{ $transaksi->metode_pembayaran === 'Tunai' ? 'selected' : '' }}>Tunai</option>
                        <option value="Transfer Bank" {{ $transaksi->metode_pembayaran === 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="QRIS" {{ $transaksi->metode_pembayaran === 'QRIS' ? 'selected' : '' }}>QRIS</option>
                        <option value="Lainnya" {{ $transaksi->metode_pembayaran === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('metode_pembayaran')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- No Referensi -->
                <div>
                    <label for="no_referensi" class="block mb-1 text-sm font-medium text-slate-700">
                        No. Referensi 
                        <span class="text-slate-500 text-xs font-normal">(Opsional)</span>
                    </label>
                    <input type="text" id="no_referensi" name="no_referensi" 
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                           placeholder="Nomor cek, transfer, dll" 
                           value="{{ old('no_referensi', $transaksi->no_referensi) }}">
                    @error('no_referensi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Bukti Transaksi -->
                <div class="md:col-span-2">
                    <label for="bukti_transaksi" class="block mb-1 text-sm font-medium text-slate-700">
                        Bukti Transaksi 
                        <span class="text-slate-500 text-xs font-normal">(Opsional)</span>
                    </label>
                    
                    @if($transaksi->bukti_transaksi)
                    <div class="flex items-center gap-4 mb-2">
                        <div class="flex items-center gap-2 text-sm text-blue-600">
                            <span class="material-icons-outlined text-base">attachment</span>
                            <a href="{{ asset('storage/' . $transaksi->bukti_transaksi) }}" target="_blank" class="hover:underline">
                                Lihat bukti yang sudah diupload
                            </a>
                        </div>
                    </div>
                    @endif
                    
                    <input type="file" id="bukti_transaksi" name="bukti_transaksi" 
                           class="block w-full text-sm text-slate-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-md file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-blue-50 file:text-blue-700
                                  hover:file:bg-blue-100"
                           accept="image/jpeg,image/png,image/jpg,application/pdf">
                    <p class="mt-1 text-xs text-slate-500">Upload file JPG, PNG atau PDF baru (max 2MB) atau biarkan kosong untuk tetap menggunakan bukti yang sudah ada</p>
                    @error('bukti_transaksi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Keterangan -->
                <div class="md:col-span-2">
                    <label for="keterangan" class="block mb-1 text-sm font-medium text-slate-700">
                        Keterangan 
                        <span class="text-slate-500 text-xs font-normal">(Opsional)</span>
                    </label>
                    <textarea id="keterangan" name="keterangan" rows="3" 
                              class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                              placeholder="Rincian atau catatan tambahan...">{{ old('keterangan', $transaksi->keterangan) }}</textarea>
                    @error('keterangan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('keuangan.transaksi-kas.show', $transaksi->id) }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-md transition">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
