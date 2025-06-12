@extends('layouts.admin')

@section('title', 'Edit Jabatan - ' . $jabatan->nama_jabatan)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                    <svg class="w-8 h-8 mr-3 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Jabatan: {{ $jabatan->nama_jabatan }}
                </h1>
                <p class="text-gray-600 mt-1">Perbarui informasi jabatan dalam struktur organisasi pesantren</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('kepegawaian.jabatan.show', $jabatan) }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg flex items-center transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Lihat Detail
                </a>
                <a href="{{ route('kepegawaian.jabatan.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg flex items-center transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form action="{{ route('kepegawaian.jabatan.update', $jabatan) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Informasi Dasar -->
                <div class="space-y-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">Informasi Dasar</h3>
                    
                    <!-- Nama Jabatan -->
                    <div>
                        <label for="nama_jabatan" class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Jabatan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama_jabatan" name="nama_jabatan" value="{{ old('nama_jabatan', $jabatan->nama_jabatan) }}" required
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
                        <input type="text" id="kode_jabatan" name="kode_jabatan" value="{{ old('kode_jabatan', $jabatan->kode_jabatan) }}" required
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
                            <option value="{{ $i }}" {{ old('level_jabatan', $jabatan->level_jabatan) == $i ? 'selected' : '' }}>
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
                            <option value="aktif" {{ old('status', $jabatan->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status', $jabatan->status) == 'nonaktif' ? 'selected' : '' }}>Non Aktif</option>
                        </select>
                        @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($jabatan->pegawais->count() > 0)
                        <p class="mt-1 text-xs text-yellow-600">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            Jabatan ini digunakan oleh {{ $jabatan->pegawais->count() }} pegawai
                        </p>
                        @endif
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
                            <input type="number" id="gaji_pokok" name="gaji_pokok" value="{{ old('gaji_pokok', $jabatan->gaji_pokok) }}" required min="0"
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
                            <input type="number" id="tunjangan" name="tunjangan" value="{{ old('tunjangan', $jabatan->tunjangan) }}" min="0"
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
                            <span id="total_gaji_preview">{{ $jabatan->total_gaji_formatted }}</span>
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
                                  placeholder="Tugas dan tanggung jawab jabatan ini...">{{ old('deskripsi', $jabatan->deskripsi) }}</textarea>
                        @error('deskripsi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('kepegawaian.jabatan.show', $jabatan) }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                    Perbarui Jabatan
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
