@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Top Up Dompet Santri</h1>
            <p class="text-gray-600 mt-2">{{ $dompet->nama_pemilik }} - {{ $dompet->nomor_dompet }}</p>
        </div>
        <a href="{{ route('keuangan.dompet.santri.show', $dompet->id) }}" class="mt-4 sm:mt-0 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
            <span class="material-icons-outlined text-sm mr-2">arrow_back</span>
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Top Up -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <form action="{{ route('keuangan.dompet.santri.top-up', $dompet->id) }}" method="POST" class="p-6">
                    @csrf

                    <!-- Jumlah Top Up -->
                    <div class="mb-6">
                        <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-2">
                            Jumlah Top Up <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <span class="text-gray-500">Rp</span>
                            </div>
                            <input type="text" 
                                   id="jumlah_display" 
                                   class="w-full pl-10 rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-lg"
                                   placeholder="0" 
                                   value="{{ old('jumlah') }}" 
                                   oninput="formatCurrency(this)" 
                                   required>
                            <input type="hidden" id="jumlah" name="jumlah" value="{{ old('jumlah') }}">
                        </div>
                        @error('jumlah')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Minimal Rp 1.000</p>
                    </div>

                    <!-- Quick Amounts -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nominal Cepat
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" onclick="setAmount(10000)" class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors">
                                Rp 10.000
                            </button>
                            <button type="button" onclick="setAmount(25000)" class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors">
                                Rp 25.000
                            </button>
                            <button type="button" onclick="setAmount(50000)" class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors">
                                Rp 50.000
                            </button>
                            <button type="button" onclick="setAmount(100000)" class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors">
                                Rp 100.000
                            </button>
                            <button type="button" onclick="setAmount(200000)" class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors">
                                Rp 200.000
                            </button>
                            <button type="button" onclick="setAmount(500000)" class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors">
                                Rp 500.000
                            </button>
                        </div>
                    </div>

                    <!-- Sumber Dana -->
                    <div class="mb-6">
                        <label for="buku_kas_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Sumber Dana <span class="text-red-500">*</span>
                        </label>
                        <select name="buku_kas_id" id="buku_kas_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
                            <option value="">-- Pilih Buku Kas --</option>
                            @foreach($bukuKasList as $bukuKas)
                                <option value="{{ $bukuKas->id }}" {{ old('buku_kas_id') == $bukuKas->id ? 'selected' : '' }}>
                                    {{ $bukuKas->nama_kas }} (Saldo: Rp {{ number_format($bukuKas->saldo_saat_ini, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        @error('buku_kas_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="mb-6">
                        <label for="metode_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">
                            Metode Pembayaran <span class="text-red-500">*</span>
                        </label>
                        <select name="metode_pembayaran" id="metode_pembayaran" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
                            <option value="">-- Pilih Metode --</option>
                            <option value="Tunai" {{ old('metode_pembayaran') == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                            <option value="Transfer Bank" {{ old('metode_pembayaran') == 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
                            <option value="Kartu Debit" {{ old('metode_pembayaran') == 'Kartu Debit' ? 'selected' : '' }}>Kartu Debit</option>
                            <option value="E-Wallet" {{ old('metode_pembayaran') == 'E-Wallet' ? 'selected' : '' }}>E-Wallet</option>
                        </select>
                        @error('metode_pembayaran')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Keterangan -->
                    <div class="mb-6">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                            Keterangan (Opsional)
                        </label>
                        <textarea name="keterangan" 
                                  id="keterangan" 
                                  rows="3"
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                  placeholder="Tambahan keterangan untuk transaksi ini...">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('keuangan.dompet.santri.show', $dompet->id) }}" 
                           class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                            Batal
                        </a>
                        <button type="submit" 
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                            <span class="material-icons-outlined text-sm mr-2">add_circle</span>
                            Proses Top Up
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Dompet -->
        <div class="space-y-6">
            <!-- Current Balance -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-sm p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Saldo Saat Ini</p>
                        <p class="text-3xl font-bold">Rp {{ number_format($dompet->saldo, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-full">
                        <span class="material-icons-outlined text-2xl">account_balance_wallet</span>
                    </div>
                </div>
            </div>

            <!-- Dompet Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Info Dompet</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Pemilik</label>
                        <p class="text-sm text-gray-900">{{ $dompet->nama_pemilik }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Nomor Dompet</label>
                        <p class="text-sm font-mono text-gray-900">{{ $dompet->nomor_dompet }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Limit Transaksi</label>
                        <p class="text-sm text-gray-900">
                            {{ $dompet->limit_transaksi ? 'Rp ' . number_format($dompet->limit_transaksi, 0, ',', '.') : 'Tidak terbatas' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Status</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $dompet->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $dompet->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Info Top Up -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <span class="material-icons-outlined text-blue-400">info</span>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Informasi Top Up</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Top up akan langsung masuk ke saldo dompet</li>
                                <li>Transaksi akan dicatat di buku kas yang dipilih</li>
                                <li>Konfirmasi akan dikirim ke santri</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function formatCurrency(input) {
    // Remove non-digits
    let value = input.value.replace(/\D/g, '');
    
    // Update hidden input with raw value
    const hiddenInput = document.getElementById('jumlah');
    if (hiddenInput) {
        hiddenInput.value = value;
    }
    
    // Format with thousand separator for display
    if (value !== '' && value !== '0') {
        const formattedValue = new Intl.NumberFormat('id-ID').format(parseInt(value));
        input.value = formattedValue;
    } else {
        input.value = '';
    }
}

function setAmount(amount) {
    const displayInput = document.getElementById('jumlah_display');
    const hiddenInput = document.getElementById('jumlah');
    
    if (displayInput && hiddenInput) {
        hiddenInput.value = amount;
        displayInput.value = new Intl.NumberFormat('id-ID').format(amount);
    }
}
</script>
@endsection
