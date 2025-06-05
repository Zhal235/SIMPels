@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 pb-4 border-b border-gray-200">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
                Edit Tahun Ajaran
            </h1>
            <p class="text-sm text-gray-500 mt-1">Perbarui informasi tahun ajaran {{ $tahunAjaran->nama_tahun_ajaran }}.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('akademik.tahun-ajaran.index') }}" 
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                <span class="material-icons-outlined mr-2 text-gray-500">arrow_back</span>
                Kembali
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Status Card -->
    @if($tahunAjaran->is_active)
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="text-green-800 font-medium">Tahun ajaran ini sedang aktif dan digunakan sebagai referensi administrasi keuangan.</span>
            </div>
        </div>
    @endif

    <!-- Form -->
    <div class="bg-white shadow-xl rounded-xl border border-gray-200">
        <form action="{{ route('akademik.tahun-ajaran.update', $tahunAjaran) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Nama Tahun Ajaran -->
            <div>
                <label for="nama_tahun_ajaran" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Tahun Ajaran <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="nama_tahun_ajaran" 
                       name="nama_tahun_ajaran" 
                       value="{{ old('nama_tahun_ajaran', $tahunAjaran->nama_tahun_ajaran) }}"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('nama_tahun_ajaran') border-red-300 @enderror"
                       placeholder="Contoh: Tahun Ajaran 2024/2025"
                       required>
                @error('nama_tahun_ajaran')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Periode Tahun -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="tahun_mulai" class="block text-sm font-medium text-gray-700 mb-2">
                        Tahun Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="tahun_mulai" 
                           name="tahun_mulai" 
                           value="{{ old('tahun_mulai', $tahunAjaran->tahun_mulai) }}"
                           min="2000" 
                           max="2100"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('tahun_mulai') border-red-300 @enderror"
                           required>
                    @error('tahun_mulai')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="tahun_selesai" class="block text-sm font-medium text-gray-700 mb-2">
                        Tahun Selesai <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="tahun_selesai" 
                           name="tahun_selesai" 
                           value="{{ old('tahun_selesai', $tahunAjaran->tahun_selesai) }}"
                           min="2000" 
                           max="2100"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('tahun_selesai') border-red-300 @enderror"
                           required>
                    @error('tahun_selesai')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Periode Tanggal -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="tanggal_mulai" 
                           name="tanggal_mulai" 
                           value="{{ old('tanggal_mulai', $tahunAjaran->tanggal_mulai->format('Y-m-d')) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('tanggal_mulai') border-red-300 @enderror"
                           required>
                    @error('tanggal_mulai')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Selesai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="tanggal_selesai" 
                           name="tanggal_selesai" 
                           value="{{ old('tanggal_selesai', $tahunAjaran->tanggal_selesai->format('Y-m-d')) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('tanggal_selesai') border-red-300 @enderror"
                           required>
                    @error('tanggal_selesai')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Status Aktif -->
            <div>
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="is_active" 
                           name="is_active" 
                           value="1"
                           {{ old('is_active', $tahunAjaran->is_active) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="is_active" class="ml-2 text-sm text-gray-700">
                        Set sebagai tahun ajaran aktif
                    </label>
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    Jika dicentang, tahun ajaran ini akan menjadi aktif dan tahun ajaran lain akan dinonaktifkan.
                </p>
                @error('is_active')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Keterangan -->
            <div>
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                    Keterangan
                </label>
                <textarea id="keterangan" 
                          name="keterangan" 
                          rows="3"
                          class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('keterangan') border-red-300 @enderror"
                          placeholder="Keterangan tambahan (opsional)">{{ old('keterangan', $tahunAjaran->keterangan) }}</textarea>
                @error('keterangan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">
                <button type="submit" 
                        class="w-full sm:w-auto bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out flex items-center justify-center gap-2 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Perbarui Tahun Ajaran
                </button>
                
                <a href="{{ route('akademik.tahun-ajaran.index') }}" 
                   class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-update tahun selesai ketika tahun mulai berubah
document.getElementById('tahun_mulai').addEventListener('change', function() {
    const tahunMulai = parseInt(this.value);
    const tahunSelesaiInput = document.getElementById('tahun_selesai');
    
    if (tahunMulai && parseInt(tahunSelesaiInput.value) <= tahunMulai) {
        tahunSelesaiInput.value = tahunMulai + 1;
    }
});
</script>
@endsection