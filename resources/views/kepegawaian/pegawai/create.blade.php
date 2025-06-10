@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Tambah Pegawai</h1>
            <p class="text-gray-600 mt-2">Tambahkan data pegawai baru ke sistem kepegawaian</p>
        </div>
        <div class="flex items-center gap-2 mt-4 sm:mt-0">
            <a href="{{ route('kepegawaian.pegawai.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-md transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
        <div class="flex items-center mb-2">
            <span class="material-icons-outlined mr-2">error</span>
            <h4 class="font-medium">Terdapat kesalahan:</h4>
        </div>
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Form -->
    <form action="{{ route('kepegawaian.pegawai.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <!-- Data Pribadi -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-3">
                <h3 class="font-medium text-gray-700">Data Pribadi</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nama Pegawai -->
                    <div>
                        <label for="nama_pegawai" class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_pegawai" id="nama_pegawai" 
                               value="{{ old('nama_pegawai') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>

                    <!-- NIK -->
                    <div>
                        <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">
                            NIK <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nik" id="nik" 
                               value="{{ old('nik') }}"
                               maxlength="16" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>

                    <!-- NIP -->
                    <div>
                        <label for="nip" class="block text-sm font-medium text-gray-700 mb-1">
                            NIP (Opsional)
                        </label>
                        <input type="text" name="nip" id="nip" 
                               value="{{ old('nip') }}"
                               maxlength="20"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Jenis Kelamin -->
                    <div>
                        <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">
                            Jenis Kelamin <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis_kelamin" id="jenis_kelamin" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <!-- Tempat Lahir -->
                    <div>
                        <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-1">
                            Tempat Lahir <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir" 
                               value="{{ old('tempat_lahir') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Lahir <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" 
                               value="{{ old('tanggal_lahir') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>

                    <!-- Agama -->
                    <div>
                        <label for="agama" class="block text-sm font-medium text-gray-700 mb-1">
                            Agama <span class="text-red-500">*</span>
                        </label>
                        <select name="agama" id="agama" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <option value="">Pilih Agama</option>
                            <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                            <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                            <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                            <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                        </select>
                    </div>

                    <!-- Status Pernikahan -->
                    <div>
                        <label for="status_pernikahan" class="block text-sm font-medium text-gray-700 mb-1">
                            Status Pernikahan <span class="text-red-500">*</span>
                        </label>
                        <select name="status_pernikahan" id="status_pernikahan" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <option value="">Pilih Status</option>
                            <option value="Belum Menikah" {{ old('status_pernikahan') == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah</option>
                            <option value="Menikah" {{ old('status_pernikahan') == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                            <option value="Cerai Hidup" {{ old('status_pernikahan') == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                            <option value="Cerai Mati" {{ old('status_pernikahan') == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                        </select>
                    </div>
                </div>

                <!-- Alamat -->
                <div>
                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">
                        Alamat <span class="text-red-500">*</span>
                    </label>
                    <textarea name="alamat" id="alamat" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                              required>{{ old('alamat') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- No HP -->
                    <div>
                        <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-1">
                            No. HP
                        </label>
                        <input type="text" name="no_hp" id="no_hp" 
                               value="{{ old('no_hp') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email
                        </label>
                        <input type="email" name="email" id="email" 
                               value="{{ old('email') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Pendidikan -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-3">
                <h3 class="font-medium text-gray-700">Data Pendidikan</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Pendidikan Terakhir -->
                    <div>
                        <label for="pendidikan_terakhir" class="block text-sm font-medium text-gray-700 mb-1">
                            Pendidikan Terakhir <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="pendidikan_terakhir" id="pendidikan_terakhir" 
                               value="{{ old('pendidikan_terakhir') }}"
                               placeholder="contoh: S1, S2, SMA, dll"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>

                    <!-- Jurusan -->
                    <div>
                        <label for="jurusan" class="block text-sm font-medium text-gray-700 mb-1">
                            Jurusan
                        </label>
                        <input type="text" name="jurusan" id="jurusan" 
                               value="{{ old('jurusan') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Institusi -->
                    <div>
                        <label for="institusi" class="block text-sm font-medium text-gray-700 mb-1">
                            Institusi/Universitas
                        </label>
                        <input type="text" name="institusi" id="institusi" 
                               value="{{ old('institusi') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Tahun Lulus -->
                    <div>
                        <label for="tahun_lulus" class="block text-sm font-medium text-gray-700 mb-1">
                            Tahun Lulus
                        </label>
                        <input type="number" name="tahun_lulus" id="tahun_lulus" 
                               value="{{ old('tahun_lulus') }}"
                               min="1900" max="{{ date('Y') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Kepegawaian -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-3">
                <h3 class="font-medium text-gray-700">Data Kepegawaian</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Jabatan -->
                    <div>
                        <label for="jabatan" class="block text-sm font-medium text-gray-700 mb-1">
                            Jabatan
                        </label>
                        <input type="text" name="jabatan" id="jabatan" 
                               value="{{ old('jabatan') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Divisi -->
                    <div>
                        <label for="divisi" class="block text-sm font-medium text-gray-700 mb-1">
                            Divisi/Bagian
                        </label>
                        <input type="text" name="divisi" id="divisi" 
                               value="{{ old('divisi') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Tanggal Masuk -->
                    <div>
                        <label for="tanggal_masuk" class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Masuk <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_masuk" id="tanggal_masuk" 
                               value="{{ old('tanggal_masuk') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>

                    <!-- Status Pegawai -->
                    <div>
                        <label for="status_pegawai" class="block text-sm font-medium text-gray-700 mb-1">
                            Status Pegawai <span class="text-red-500">*</span>
                        </label>
                        <select name="status_pegawai" id="status_pegawai" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <option value="">Pilih Status</option>
                            <option value="Aktif" {{ old('status_pegawai') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Non-Aktif" {{ old('status_pegawai') == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                            <option value="Pensiun" {{ old('status_pegawai') == 'Pensiun' ? 'selected' : '' }}>Pensiun</option>
                            <option value="Resign" {{ old('status_pegawai') == 'Resign' ? 'selected' : '' }}>Resign</option>
                        </select>
                    </div>

                    <!-- Jenis Pegawai -->
                    <div>
                        <label for="jenis_pegawai" class="block text-sm font-medium text-gray-700 mb-1">
                            Jenis Pegawai <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis_pegawai" id="jenis_pegawai" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <option value="">Pilih Jenis</option>
                            <option value="Tetap" {{ old('jenis_pegawai') == 'Tetap' ? 'selected' : '' }}>Tetap</option>
                            <option value="Kontrak" {{ old('jenis_pegawai') == 'Kontrak' ? 'selected' : '' }}>Kontrak</option>
                            <option value="Honorer" {{ old('jenis_pegawai') == 'Honorer' ? 'selected' : '' }}>Honorer</option>
                            <option value="Magang" {{ old('jenis_pegawai') == 'Magang' ? 'selected' : '' }}>Magang</option>
                        </select>
                    </div>

                    <!-- Gaji Pokok -->
                    <div>
                        <label for="gaji_pokok" class="block text-sm font-medium text-gray-700 mb-1">
                            Gaji Pokok
                        </label>
                        <input type="number" name="gaji_pokok" id="gaji_pokok" 
                               value="{{ old('gaji_pokok') }}"
                               min="0" step="1000"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Foto -->
                <div>
                    <label for="foto" class="block text-sm font-medium text-gray-700 mb-1">
                        Foto Pegawai
                    </label>
                    <input type="file" name="foto" id="foto" accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB.</p>
                </div>

                <!-- Keterangan -->
                <div>
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">
                        Keterangan
                    </label>
                    <textarea name="keterangan" id="keterangan" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Keterangan tambahan...">{{ old('keterangan') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('kepegawaian.pegawai.index') }}" 
               class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                Simpan Data Pegawai
            </button>
        </div>
    </form>
</div>

<script>
// Auto format NIK input
document.getElementById('nik').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 16) {
        value = value.slice(0, 16);
    }
    e.target.value = value;
});

// Auto format phone number
document.getElementById('no_hp').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    e.target.value = value;
});
</script>
@endsection
