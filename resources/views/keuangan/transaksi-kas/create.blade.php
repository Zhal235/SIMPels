@extends('layouts.admin')

@section('title', 'Tambah Transaksi Kas')

@section('content')
<div class="container-fluid px-6 py-4">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">
            @if($jenis === 'pemasukan')
                Tambah Pemasukan Kas
            @elseif($jenis === 'pengeluaran')
                Tambah Pengeluaran Kas
            @else
                Tambah Transfer Kas
            @endif
        </h2>
        <p class="text-slate-600 mt-1">
            @if($jenis === 'pemasukan')
                Catat transaksi pemasukan kas baru
            @elseif($jenis === 'pengeluaran')
                Catat transaksi pengeluaran kas baru
            @else
                Catat transfer antar buku kas
            @endif
        </p>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <form action="{{ route('keuangan.transaksi-kas.store') }}" method="POST" enctype="multipart/form-data" class="max-w-4xl">
            @csrf
            
            <input type="hidden" name="jenis_transaksi" value="{{ $jenis }}">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Buku Kas Sumber -->
                <div>
                    <label for="buku_kas_id" class="block mb-1 text-sm font-medium text-slate-700">
                        @if($jenis === 'transfer')
                            Buku Kas Sumber
                        @else
                            Buku Kas
                        @endif
                        <span class="text-red-500">*</span>
                    </label>
                    <select id="buku_kas_id" name="buku_kas_id" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        <option value="">Pilih Buku Kas</option>
                        @foreach($bukuKasList as $bukuKas)
                        <option value="{{ $bukuKas->id }}" data-saldo="{{ $bukuKas->saldo_saat_ini }}">
                            {{ $bukuKas->nama_kas }} - Rp {{ number_format($bukuKas->saldo_saat_ini, 0, ',', '.') }}
                        </option>
                        @endforeach
                    </select>
                    @error('buku_kas_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    
                    <div id="saldo_info" class="mt-1 text-xs text-slate-500"></div>
                </div>
                
                <!-- Buku Kas Tujuan (for transfer only) -->
                @if($jenis === 'transfer')
                <div>
                    <label for="buku_kas_tujuan_id" class="block mb-1 text-sm font-medium text-slate-700">
                        Buku Kas Tujuan
                        <span class="text-red-500">*</span>
                    </label>
                    <select id="buku_kas_tujuan_id" name="buku_kas_tujuan_id" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        <option value="">Pilih Buku Kas Tujuan</option>
                        @foreach($bukuKasList as $bukuKas)
                        <option value="{{ $bukuKas->id }}">
                            {{ $bukuKas->nama_kas }} - Rp {{ number_format($bukuKas->saldo_saat_ini, 0, ',', '.') }}
                        </option>
                        @endforeach
                    </select>
                    @error('buku_kas_tujuan_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                @endif
                
                <!-- Kategori -->
                <div class="{{ $jenis !== 'transfer' ? 'md:col-span-2' : '' }}">
                    <label for="kategori" class="block mb-1 text-sm font-medium text-slate-700">
                        Kategori
                        <span class="text-red-500">*</span>
                    </label>
                    <select id="kategori" name="kategori" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        <option value="">Pilih Kategori</option>
                        
                        @if($jenis === 'pemasukan')
                            <option value="Pembayaran Santri">Pembayaran Santri</option>
                            <option value="Sumbangan">Sumbangan</option>
                            <option value="Hibah">Hibah</option>
                            <option value="Dana Bantuan">Dana Bantuan</option>
                            <option value="Lainnya">Lainnya</option>
                        @elseif($jenis === 'pengeluaran')
                            <option value="Operasional">Operasional</option>
                            <option value="Gaji">Gaji</option>
                            <option value="Pemeliharaan">Pemeliharaan</option>
                            <option value="Pembangunan">Pembangunan</option>
                            <option value="ATK">ATK</option>
                            <option value="Konsumsi">Konsumsi</option>
                            <option value="Lainnya">Lainnya</option>
                        @else
                            <option value="Transfer Kas">Transfer Kas</option>
                            <option value="Penyesuaian Saldo">Penyesuaian Saldo</option>
                            <option value="Lainnya">Lainnya</option>
                        @endif
                    </select>
                    @error('kategori')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Jumlah -->
                <div class="md:col-span-2">
                    <label for="jumlah" class="block mb-1 text-sm font-medium text-slate-700">
                        Jumlah (Rp)
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <span class="text-slate-500">Rp</span>
                        </div>
                        <input type="text" id="jumlah" name="jumlah" 
                               class="block w-full pl-12 pr-12 rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               placeholder="0" 
                               value="{{ old('jumlah') }}" 
                               oninput="formatCurrency(this)" 
                               required>
                    </div>
                    @error('jumlah')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Tanggal Transaksi -->
                <div>
                    <label for="tanggal_transaksi" class="block mb-1 text-sm font-medium text-slate-700">
                        Tanggal Transaksi
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="tanggal_transaksi" name="tanggal_transaksi" 
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                           value="{{ old('tanggal_transaksi', now()->format('Y-m-d')) }}" 
                           required>
                    @error('tanggal_transaksi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Metode Pembayaran -->
                <div>
                    <label for="metode_pembayaran" class="block mb-1 text-sm font-medium text-slate-700">
                        Metode Transaksi
                        <span class="text-red-500">*</span>
                    </label>
                    <select id="metode_pembayaran" name="metode_pembayaran" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        <option value="">Pilih Metode</option>
                        <option value="Tunai">Tunai</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="QRIS">QRIS</option>
                        <option value="Lainnya">Lainnya</option>
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
                           value="{{ old('no_referensi') }}">
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
                    <input type="file" id="bukti_transaksi" name="bukti_transaksi" 
                           class="block w-full text-sm text-slate-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-md file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-blue-50 file:text-blue-700
                                  hover:file:bg-blue-100"
                           accept="image/jpeg,image/png,image/jpg,application/pdf">
                    <p class="mt-1 text-xs text-slate-500">Upload file JPG, PNG atau PDF (max 2MB)</p>
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
                              placeholder="Rincian atau catatan tambahan...">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('keuangan.transaksi-kas.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-md transition">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
                    Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Format currency on input
        const jumlahInput = document.getElementById('jumlah');
        if (jumlahInput) {
            jumlahInput.addEventListener('input', function() {
                formatCurrency(this);
            });
        }
        
        // Handle buku kas selection
        const bukuKasSelect = document.getElementById('buku_kas_id');
        const bukuKasTujuanSelect = document.getElementById('buku_kas_tujuan_id');
        const saldoInfo = document.getElementById('saldo_info');
        const jenisTransaksi = '{{ $jenis }}';
        
        if (bukuKasSelect) {
            bukuKasSelect.addEventListener('change', function() {
                updateSaldoInfo();
                
                if (bukuKasTujuanSelect) {
                    // Disable the same option in destination dropdown
                    const selectedValue = this.value;
                    
                    Array.from(bukuKasTujuanSelect.options).forEach(option => {
                        option.disabled = (option.value === selectedValue && option.value !== '');
                    });
                }
            });
        }
        
        if (bukuKasTujuanSelect) {
            bukuKasTujuanSelect.addEventListener('change', function() {
                const selectedValue = this.value;
                
                // Disable the same option in source dropdown
                if (bukuKasSelect) {
                    Array.from(bukuKasSelect.options).forEach(option => {
                        option.disabled = (option.value === selectedValue && option.value !== '');
                    });
                }
            });
        }
        
        function updateSaldoInfo() {
            if (!bukuKasSelect || !saldoInfo) return;
            
            const selectedOption = bukuKasSelect.options[bukuKasSelect.selectedIndex];
            if (selectedOption && selectedOption.value !== '') {
                const saldo = selectedOption.getAttribute('data-saldo');
                if (saldo) {
                    const formattedSaldo = new Intl.NumberFormat('id-ID').format(saldo);
                    
                    if (jenisTransaksi === 'pengeluaran' || jenisTransaksi === 'transfer') {
                        saldoInfo.textContent = `Saldo tersedia: Rp ${formattedSaldo}`;
                        saldoInfo.classList.add('text-blue-600');
                    } else {
                        saldoInfo.textContent = '';
                    }
                }
            } else {
                saldoInfo.textContent = '';
            }
        }
        
        // Initial update
        if (bukuKasSelect && bukuKasSelect.value) {
            updateSaldoInfo();
        }
    });
    
    function formatCurrency(input) {
        // Remove non-digits
        let value = input.value.replace(/\D/g, '');
        
        // Format with thousand separator
        if (value !== '') {
            value = parseInt(value).toLocaleString('id-ID');
        }
        
        input.value = value;
    }
</script>
@endpush
