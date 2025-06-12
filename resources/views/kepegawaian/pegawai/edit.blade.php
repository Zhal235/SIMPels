@extends('layouts.admin')

@section('title', 'Edit Pegawai - ' . $pegawai->nama_pegawai)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Pegawai</h1>
            <p class="text-gray-600 mt-2">Perbarui data pegawai: {{ $pegawai->nama_pegawai }}</p>
        </div>
        <div class="flex items-center gap-2 mt-4 sm:mt-0">
            <a href="{{ route('kepegawaian.pegawai.show', $pegawai) }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                </svg>
                Lihat Detail
            </a>
            <a href="{{ route('kepegawaian.pegawai.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-md transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
        <div class="flex items-center">
            <span class="material-icons-outlined mr-2">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
        <div class="flex items-center">
            <span class="material-icons-outlined mr-2">error</span>
            <div>
                <p class="font-medium">Ada beberapa kesalahan:</p>
                <ul class="mt-1 list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('kepegawaian.pegawai.update', $pegawai) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
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
                               value="{{ old('nama_pegawai', $pegawai->nama_pegawai) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>

                    <!-- NIK -->
                    <div>
                        <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">
                            NIK <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nik" id="nik" 
                               value="{{ old('nik', $pegawai->nik) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                               maxlength="16" pattern="[0-9]{16}" required>
                    </div>

                    <!-- NIP -->
                    <div>
                        <label for="nip" class="block text-sm font-medium text-gray-700 mb-1">
                            NIP
                        </label>
                        <input type="text" name="nip" id="nip" 
                               value="{{ old('nip', $pegawai->nip) }}"
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
                            <option value="L" {{ old('jenis_kelamin', $pegawai->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $pegawai->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <!-- Tempat Lahir -->
                    <div>
                        <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-1">
                            Tempat Lahir <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir" 
                               value="{{ old('tempat_lahir', $pegawai->tempat_lahir) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Lahir <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" 
                               value="{{ old('tanggal_lahir', $pegawai->tanggal_lahir?->format('Y-m-d')) }}"
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
                            <option value="Islam" {{ old('agama', $pegawai->agama) == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen" {{ old('agama', $pegawai->agama) == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                            <option value="Katolik" {{ old('agama', $pegawai->agama) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                            <option value="Hindu" {{ old('agama', $pegawai->agama) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ old('agama', $pegawai->agama) == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                            <option value="Konghucu" {{ old('agama', $pegawai->agama) == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
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
                            <option value="Belum Menikah" {{ old('status_pernikahan', $pegawai->status_pernikahan) == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah</option>
                            <option value="Menikah" {{ old('status_pernikahan', $pegawai->status_pernikahan) == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                            <option value="Cerai Hidup" {{ old('status_pernikahan', $pegawai->status_pernikahan) == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                            <option value="Cerai Mati" {{ old('status_pernikahan', $pegawai->status_pernikahan) == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                        </select>
                    </div>
                </div>

                <!-- Alamat -->
                <div>
                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">
                        Alamat Lengkap <span class="text-red-500">*</span>
                    </label>
                    <textarea name="alamat" id="alamat" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                              required>{{ old('alamat', $pegawai->alamat) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- No HP -->
                    <div>
                        <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-1">
                            No. HP/WhatsApp
                        </label>
                        <input type="tel" name="no_hp" id="no_hp" 
                               value="{{ old('no_hp', $pegawai->no_hp) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email
                        </label>
                        <input type="email" name="email" id="email" 
                               value="{{ old('email', $pegawai->email) }}"
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
                               value="{{ old('pendidikan_terakhir', $pegawai->pendidikan_terakhir) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Contoh: S1, S2, SMA, dll"
                               required>
                    </div>

                    <!-- Jurusan -->
                    <div>
                        <label for="jurusan" class="block text-sm font-medium text-gray-700 mb-1">
                            Jurusan/Program Studi
                        </label>
                        <input type="text" name="jurusan" id="jurusan" 
                               value="{{ old('jurusan', $pegawai->jurusan) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Institusi -->
                    <div>
                        <label for="institusi" class="block text-sm font-medium text-gray-700 mb-1">
                            Asal Institusi
                        </label>
                        <input type="text" name="institusi" id="institusi" 
                               value="{{ old('institusi', $pegawai->institusi) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Tahun Lulus -->
                    <div>
                        <label for="tahun_lulus" class="block text-sm font-medium text-gray-700 mb-1">
                            Tahun Lulus
                        </label>
                        <input type="number" name="tahun_lulus" id="tahun_lulus" 
                               value="{{ old('tahun_lulus', $pegawai->tahun_lulus) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                               min="1900" max="{{ date('Y') }}">
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">                    <!-- Jabatan Utama -->
                    <div>
                        <label for="jabatan_utama_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Jabatan Utama <span class="text-red-500">*</span>
                        </label>
                        <select name="jabatan_utama_id" id="jabatan_utama_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                required onchange="updateBidangFromJabatan()">
                            <option value="">Pilih Jabatan Utama</option>
                            @foreach($jabatanOptions->groupBy('kategori_jabatan') as $kategori => $jabatans)
                                <optgroup label="{{ ucfirst($kategori) }} - {{ $jabatans->first()->kategori_nama }}">
                                    @foreach($jabatans as $jabatan)
                                        @php
                                            // Check if this is the utama jabatan either from old input or from pivot table
                                            $isUtama = false;
                                            if (old('jabatan_utama_id')) {
                                                $isUtama = old('jabatan_utama_id') == $jabatan->id;
                                            } else {
                                                // Check from pivot table
                                                $jabatanUtama = $pegawai->pegawaiJabatans()->where('is_jabatan_utama', true)->where('status', 'aktif')->first();
                                                if ($jabatanUtama) {
                                                    $isUtama = $jabatanUtama->jabatan_id == $jabatan->id;
                                                } else {
                                                    // Fallback to pegawai->jabatan_id
                                                    $isUtama = $pegawai->jabatan_id == $jabatan->id;
                                                }
                                            }
                                        @endphp
                                        <option value="{{ $jabatan->id }}" 
                                                data-bidang="{{ $jabatan->bidang?->nama_bidang }}"
                                                data-gaji="{{ $jabatan->gaji_pokok }}"
                                                data-level="{{ $jabatan->level_jabatan }}"
                                                {{ $isUtama ? 'selected' : '' }}>
                                            {{ $jabatan->nama_jabatan }}
                                            @if($jabatan->bidang)
                                                ({{ $jabatan->bidang->nama_bidang }})
                                            @endif
                                            - Level {{ $jabatan->level_jabatan }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('jabatan_utama_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>                    <!-- Jabatan Tambahan -->
                    <div>
                        <label for="jabatan_tambahan" class="block text-sm font-medium text-gray-700 mb-1">
                            Jabatan Tambahan (Opsional)
                        </label>
                        <select name="jabatan_tambahan[]" id="jabatan_tambahan" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                multiple>
                            @foreach($jabatanOptions->groupBy('kategori_jabatan') as $kategori => $jabatans)
                                <optgroup label="{{ ucfirst($kategori) }} - {{ $jabatans->first()->kategori_nama }}">
                                    @foreach($jabatans as $jabatan)
                                        @php
                                            // Check if this is a tambahan jabatan
                                            $isTambahan = false;
                                            if (old('jabatan_tambahan')) {
                                                $isTambahan = in_array($jabatan->id, old('jabatan_tambahan', []));
                                            } else {
                                                // Get from pivot table
                                                $jabatanTambahan = $pegawai->pegawaiJabatans()
                                                    ->where('is_jabatan_utama', false)
                                                    ->where('status', 'aktif')
                                                    ->pluck('jabatan_id')
                                                    ->toArray();
                                                $isTambahan = in_array($jabatan->id, $jabatanTambahan);
                                            }
                                        @endphp
                                        <option value="{{ $jabatan->id }}"
                                                {{ $isTambahan ? 'selected' : '' }}>
                                            {{ $jabatan->nama_jabatan }}
                                            @if($jabatan->bidang)
                                                ({{ $jabatan->bidang->nama_bidang }})
                                            @endif
                                            - Level {{ $jabatan->level_jabatan }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            Tahan Ctrl (Windows) atau Cmd (Mac) untuk memilih multiple jabatan
                        </p>
                    </div>

                    <!-- Bidang/Divisi (Auto-filled) -->
                    <div>
                        <label for="divisi" class="block text-sm font-medium text-gray-700 mb-1">
                            Bidang/Divisi
                        </label>
                        <input type="text" name="divisi" id="divisi" 
                               value="{{ old('divisi', $pegawai->divisi) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                               readonly>
                        <p class="mt-1 text-xs text-gray-500">Otomatis terisi berdasarkan jabatan utama yang dipilih</p>
                    </div>

                    <!-- Tanggal Masuk -->
                    <div>
                        <label for="tanggal_masuk" class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Masuk <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_masuk" id="tanggal_masuk" 
                               value="{{ old('tanggal_masuk', $pegawai->tanggal_masuk?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>

                    <!-- Tanggal Keluar -->
                    <div>
                        <label for="tanggal_keluar" class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Keluar
                        </label>
                        <input type="date" name="tanggal_keluar" id="tanggal_keluar" 
                               value="{{ old('tanggal_keluar', $pegawai->tanggal_keluar?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
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
                            <option value="Aktif" {{ old('status_pegawai', $pegawai->status_pegawai) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Non-Aktif" {{ old('status_pegawai', $pegawai->status_pegawai) == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                            <option value="Pensiun" {{ old('status_pegawai', $pegawai->status_pegawai) == 'Pensiun' ? 'selected' : '' }}>Pensiun</option>
                            <option value="Resign" {{ old('status_pegawai', $pegawai->status_pegawai) == 'Resign' ? 'selected' : '' }}>Resign</option>
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
                            <option value="Tetap" {{ old('jenis_pegawai', $pegawai->jenis_pegawai) == 'Tetap' ? 'selected' : '' }}>Tetap</option>
                            <option value="Kontrak" {{ old('jenis_pegawai', $pegawai->jenis_pegawai) == 'Kontrak' ? 'selected' : '' }}>Kontrak</option>
                            <option value="Honorer" {{ old('jenis_pegawai', $pegawai->jenis_pegawai) == 'Honorer' ? 'selected' : '' }}>Honorer</option>
                            <option value="Magang" {{ old('jenis_pegawai', $pegawai->jenis_pegawai) == 'Magang' ? 'selected' : '' }}>Magang</option>
                        </select>
                    </div>

                    <!-- Gaji Pokok -->
                    <div>
                        <label for="gaji_pokok" class="block text-sm font-medium text-gray-700 mb-1">
                            Gaji Pokok
                        </label>
                        <input type="number" name="gaji_pokok" id="gaji_pokok" 
                               value="{{ old('gaji_pokok', $pegawai->gaji_pokok) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                               min="0" step="1000">
                    </div>
                </div>

                <!-- Keterangan -->
                <div>
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">
                        Keterangan Tambahan
                    </label>
                    <textarea name="keterangan" id="keterangan" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">{{ old('keterangan', $pegawai->keterangan) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Foto -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-3">
                <h3 class="font-medium text-gray-700">Foto Pegawai</h3>
            </div>
            <div class="p-6 space-y-4">
                @if($pegawai->foto)
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto Saat Ini</label>
                    <img src="{{ asset('storage/' . $pegawai->foto) }}" 
                         alt="{{ $pegawai->nama_pegawai }}" 
                         class="w-32 h-32 object-cover rounded-lg border">
                </div>
                @endif
                
                <div>
                    <label for="foto" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ $pegawai->foto ? 'Ganti Foto' : 'Upload Foto' }}
                    </label>
                    <input type="file" name="foto" id="foto" accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG. Maksimal 2MB.</p>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('kepegawaian.pegawai.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md font-medium transition-colors duration-200">
                Batal
            </a>
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium transition-colors duration-200">
                Perbarui Data Pegawai
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

// Update bidang dan gaji berdasarkan jabatan yang dipilih
function updateBidangFromJabatan() {
    const jabatanSelect = document.getElementById('jabatan_utama_id');
    const divisiInput = document.getElementById('divisi');
    const gajiInput = document.getElementById('gaji_pokok');
    
    const selectedOption = jabatanSelect.options[jabatanSelect.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
        // Update bidang/divisi
        const bidang = selectedOption.getAttribute('data-bidang');
        if (bidang && bidang !== 'null') {
            divisiInput.value = bidang;
        } else {
            divisiInput.value = '';
        }
        
        // Update gaji pokok jika belum diisi
        const gaji = selectedOption.getAttribute('data-gaji');
        if (gaji && gaji !== 'null' && (!gajiInput.value || gajiInput.value == '0')) {
            gajiInput.value = gaji;
        }
        
        // Update jabatan tambahan - remove jabatan utama dari pilihan
        updateJabatanTambahan();
    } else {
        divisiInput.value = '';
    }
}

// Update pilihan jabatan tambahan
function updateJabatanTambahan() {
    const jabatanUtamaSelect = document.getElementById('jabatan_utama_id');
    const jabatanTambahanSelect = document.getElementById('jabatan_tambahan');
    const selectedUtama = jabatanUtamaSelect.value;
    
    // Enable semua option dulu
    for (let option of jabatanTambahanSelect.options) {
        option.disabled = false;
        option.style.display = 'block';
    }
    
    // Disable jabatan utama di jabatan tambahan
    if (selectedUtama) {
        const optionToDisable = jabatanTambahanSelect.querySelector(`option[value="${selectedUtama}"]`);
        if (optionToDisable) {
            optionToDisable.disabled = true;
            optionToDisable.selected = false;
            optionToDisable.style.display = 'none';
        }
    }
}

// Initialize Select2 untuk multi-select yang lebih user friendly
document.addEventListener('DOMContentLoaded', function() {
    updateBidangFromJabatan();
    
    // Initialize multi-select untuk jabatan tambahan
    const jabatanTambahanSelect = document.getElementById('jabatan_tambahan');
    if (jabatanTambahanSelect) {
        // Simple styling untuk multiple select
        jabatanTambahanSelect.style.height = 'auto';
        jabatanTambahanSelect.style.minHeight = '80px';
    }
    
    // Initial update untuk jabatan tambahan saat load
    updateJabatanTambahan();
});
</script>
@endsection
