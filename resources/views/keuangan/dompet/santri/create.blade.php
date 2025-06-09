@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Buat Dompet Santri Baru</h1>
            <p class="text-gray-600 mt-2">Buat dompet digital untuk santri</p>
        </div>
        <a href="{{ route('keuangan.dompet.santri.index') }}" class="mt-4 sm:mt-0 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
            <span class="material-icons-outlined text-sm mr-2">arrow_back</span>
            Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form action="{{ route('keuangan.dompet.santri.store') }}" method="POST" class="p-6">
            @csrf

            <!-- Alert Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <span class="material-icons-outlined text-blue-400">info</span>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Informasi</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Nomor dompet akan dibuat otomatis dengan format DS[TAHUN][ID_SANTRI]</li>
                                <li>Jika saldo awal diisi, akan otomatis dicatat sebagai transaksi pemasukan</li>
                                <li>Dompet akan langsung aktif setelah dibuat</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Pilih Santri -->
                    <div>
                        <label for="santri_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih Santri <span class="text-red-500">*</span>
                        </label>
                        <select name="santri_id" id="santri_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">-- Pilih Santri --</option>
                            @foreach($santriTanpaDompet as $santri)
                                <option value="{{ $santri->id }}" {{ old('santri_id') == $santri->id ? 'selected' : '' }}>
                                    {{ $santri->nama_santri }} ({{ $santri->nis }})
                                </option>
                            @endforeach
                        </select>
                        @error('santri_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Saldo Awal -->
                    <div>
                        <label for="saldo_awal" class="block text-sm font-medium text-gray-700 mb-2">
                            Saldo Awal <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <span class="text-gray-500">Rp</span>
                            </div>
                            <input type="text" 
                                   id="saldo_awal_display" 
                                   class="w-full pl-10 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="0" 
                                   value="{{ old('saldo_awal') }}" 
                                   oninput="formatCurrency(this)" 
                                   required>
                            <input type="hidden" id="saldo_awal" name="saldo_awal" value="{{ old('saldo_awal') }}">
                        </div>
                        @error('saldo_awal')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Minimal Rp 0 (boleh kosong untuk nanti diisi)</p>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Limit Transaksi -->
                    <div>
                        <label for="limit_transaksi" class="block text-sm font-medium text-gray-700 mb-2">
                            Limit Transaksi Per Hari (Opsional)
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <span class="text-gray-500">Rp</span>
                            </div>
                            <input type="text" 
                                   id="limit_transaksi_display" 
                                   class="w-full pl-10 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="0" 
                                   value="{{ old('limit_transaksi') }}" 
                                   oninput="formatCurrencyLimit(this)">
                            <input type="hidden" id="limit_transaksi" name="limit_transaksi" value="{{ old('limit_transaksi') }}">
                        </div>
                        @error('limit_transaksi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Kosongkan jika tidak ada limit</p>
                    </div>

                    <!-- Quick Amounts -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nominal Cepat Saldo Awal
                        </label>
                        <div class="grid grid-cols-2 gap-2">
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
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('keuangan.dompet.santri.index') }}" 
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    Batal
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                    <span class="material-icons-outlined text-sm mr-2">save</span>
                    Simpan Dompet
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function formatCurrency(input) {
    // Remove non-digits
    let value = input.value.replace(/\D/g, '');
    
    // Update hidden input with raw value
    const hiddenInput = document.getElementById('saldo_awal');
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

function formatCurrencyLimit(input) {
    // Remove non-digits
    let value = input.value.replace(/\D/g, '');
    
    // Update hidden input with raw value
    const hiddenInput = document.getElementById('limit_transaksi');
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
    const displayInput = document.getElementById('saldo_awal_display');
    const hiddenInput = document.getElementById('saldo_awal');
    
    if (displayInput && hiddenInput) {
        hiddenInput.value = amount;
        displayInput.value = new Intl.NumberFormat('id-ID').format(amount);
    }
}
</script>
@endsection
