@extends('layouts.admin')

@section('title', 'Tambah Jabatan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                    <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Jabatan Baru
                </h1>
                <p class="text-gray-600 mt-1">Buat jabatan baru dalam struktur organisasi pesantren</p>
            </div>
            <a href="{{ route('kepegawaian.jabatan.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg flex items-center transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form action="{{ route('kepegawaian.jabatan.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Informasi Dasar -->
                <div class="space-y-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">Informasi Dasar</h3>
                    
                    <!-- Nama Jabatan -->
                    <div>
                        <label for="nama_jabatan" class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Jabatan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama_jabatan" name="nama_jabatan" value="{{ old('nama_jabatan') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama_jabatan') border-red-500 @enderror"
                               placeholder="Contoh: Kepala Madrasah">
                        @error('nama_jabatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kode Jabatan -->
                    <div>
                        <label for="kode_jabatan" class="block text-sm font-medium text-gray-700 mb-1">
                            Kode Jabatan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="kode_jabatan" name="kode_jabatan" value="{{ old('kode_jabatan') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('kode_jabatan') border-red-500 @enderror"
                               placeholder="Contoh: KAMAD" maxlength="10" style="text-transform: uppercase;">
                        @error('kode_jabatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Maksimal 10 karakter, akan otomatis menjadi huruf besar</p>
                    </div>

                    <!-- Level Jabatan -->
                    <div>
                        <label for="level_jabatan" class="block text-sm font-medium text-gray-700 mb-1">
                            Level Jabatan <span class="text-red-500">*</span>
                        </label>
                        <select id="level_jabatan" name="level_jabatan" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('level_jabatan') border-red-500 @enderror">
                            <option value="">Pilih Level</option>
                            @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ old('level_jabatan') == $i ? 'selected' : '' }}>
                                Level {{ $i }} {{ $i == 1 ? '(Tertinggi)' : ($i == 10 ? '(Terendah)' : '') }}
                            </option>
                            @endfor
                        </select>
                        @error('level_jabatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Level 1 adalah yang tertinggi, Level 10 adalah yang terendah</p>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                            <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Non Aktif</option>
                        </select>
                        @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Informasi Gaji & Deskripsi -->
                <div class="space-y-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">Informasi Gaji & Deskripsi</h3>
                    
                    <!-- Gaji Pokok -->
                    <div>
                        <label for="gaji_pokok" class="block text-sm font-medium text-gray-700 mb-1">
                            Gaji Pokok <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                            <input type="number" id="gaji_pokok" name="gaji_pokok" value="{{ old('gaji_pokok') }}" required min="0"
                                   class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('gaji_pokok') border-red-500 @enderror"
                                   placeholder="0">
                        </div>
                        @error('gaji_pokok')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tunjangan -->
                    <div>
                        <label for="tunjangan" class="block text-sm font-medium text-gray-700 mb-1">
                            Tunjangan
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                            <input type="number" id="tunjangan" name="tunjangan" value="{{ old('tunjangan', 0) }}" min="0"
                                   class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tunjangan') border-red-500 @enderror"
                                   placeholder="0">
                        </div>
                        @error('tunjangan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Opsional, kosongkan jika tidak ada</p>
                    </div>

                    <!-- Total Gaji (Preview) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Gaji (Preview)</label>
                        <div class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-gray-900 font-medium">
                            <span id="total_gaji_preview">Rp 0</span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Otomatis dihitung dari gaji pokok + tunjangan</p>
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">
                            Deskripsi Jabatan
                        </label>
                        <textarea id="deskripsi" name="deskripsi" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('deskripsi') border-red-500 @enderror"
                                  placeholder="Tugas dan tanggung jawab jabatan ini...">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('kepegawaian.jabatan.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                    Simpan Jabatan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const gajiPokokInput = document.getElementById('gaji_pokok');
    const tunjanganInput = document.getElementById('tunjangan');
    const totalGajiPreview = document.getElementById('total_gaji_preview');
    
    function updateTotalGaji() {
        const gajiPokok = parseFloat(gajiPokokInput.value) || 0;
        const tunjangan = parseFloat(tunjanganInput.value) || 0;
        const total = gajiPokok + tunjangan;
        
        totalGajiPreview.textContent = 'Rp ' + total.toLocaleString('id-ID');
    }
    
    gajiPokokInput.addEventListener('input', updateTotalGaji);
    tunjanganInput.addEventListener('input', updateTotalGaji);
    
    // Auto uppercase for kode jabatan
    document.getElementById('kode_jabatan').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
    
    // Initial calculation
    updateTotalGaji();
});
</script>
@endsection
