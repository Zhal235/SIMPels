@extends('layouts.admin')

@section('title', 'Detail Pegawai - ' . $pegawai->nama_pegawai)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Detail Pegawai</h1>
            <p class="text-gray-600 mt-2">Informasi lengkap pegawai: {{ $pegawai->nama_pegawai }}</p>
        </div>
        <div class="flex items-center gap-2 mt-4 sm:mt-0">
            <a href="{{ route('kepegawaian.pegawai.edit', $pegawai) }}" 
               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
                Edit Data
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Foto dan Info Utama -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 text-center">
                    <div class="mb-4">
                        @if($pegawai->foto)
                            <img class="w-32 h-32 rounded-full object-cover mx-auto border-4 border-blue-100" 
                                 src="{{ asset('storage/' . $pegawai->foto) }}" 
                                 alt="{{ $pegawai->nama_pegawai }}">
                        @else
                            <div class="w-32 h-32 rounded-full bg-gray-300 flex items-center justify-center mx-auto border-4 border-gray-100">
                                <span class="material-icons-outlined text-4xl text-gray-500">person</span>
                            </div>
                        @endif
                    </div>
                    
                    <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $pegawai->nama_pegawai }}</h2>
                      <div class="space-y-2">
                        @php
                            $jabatanUtama = $pegawai->jabatan_utama;
                            $allJabatans = $pegawai->jabatansAktif;
                        @endphp
                        
                        @if($jabatanUtama)
                            <p class="text-lg text-blue-600 font-medium">{{ $jabatanUtama->nama_jabatan }}</p>
                            @if($jabatanUtama->bidang)
                                <p class="text-gray-600">{{ $jabatanUtama->bidang->nama_bidang }}</p>
                            @endif
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $jabatanUtama->kategori_nama }} (UTAMA)
                            </span>
                            
                            @if($allJabatans->count() > 1)
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <p class="text-sm text-gray-500 mb-2">Jabatan Tambahan:</p>
                                    @foreach($allJabatans as $jabatan)
                                        @php
                                            $pivotData = $pegawai->pegawaiJabatans->where('jabatan_id', $jabatan->id)->first();
                                        @endphp
                                        @if($pivotData && !$pivotData->is_jabatan_utama)
                                        <div class="mb-1">
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-700">
                                                {{ $jabatan->nama_jabatan }}
                                            </span>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        @elseif($pegawai->jabatan)
                            <p class="text-lg text-blue-600 font-medium">{{ $pegawai->jabatan->nama_jabatan }}</p>
                            @if($pegawai->jabatan->bidang)
                                <p class="text-gray-600">{{ $pegawai->jabatan->bidang->nama_bidang }}</p>
                            @endif
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $pegawai->jabatan->kategori_nama }}
                            </span>
                        @else
                            <p class="text-gray-600">{{ $pegawai->jabatan ?: 'Jabatan tidak ditentukan' }}</p>
                            @if($pegawai->divisi)
                                <p class="text-gray-600">{{ $pegawai->divisi }}</p>
                            @endif
                        @endif
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex justify-center space-x-4">
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                {{ $pegawai->status_pegawai == 'Aktif' ? 'bg-green-100 text-green-800' : 
                                   ($pegawai->status_pegawai == 'Non-Aktif' ? 'bg-red-100 text-red-800' : 
                                    ($pegawai->status_pegawai == 'Pensiun' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                {{ $pegawai->status_pegawai }}
                            </span>
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                {{ $pegawai->jenis_pegawai == 'Tetap' ? 'bg-green-100 text-green-800' : 
                                   ($pegawai->jenis_pegawai == 'Kontrak' ? 'bg-yellow-100 text-yellow-800' : 
                                    ($pegawai->jenis_pegawai == 'Honorer' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ $pegawai->jenis_pegawai }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kontak -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-6">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-3">
                    <h3 class="font-medium text-gray-700">Informasi Kontak</h3>
                </div>
                <div class="p-6 space-y-4">
                    @if($pegawai->no_hp)
                    <div class="flex items-start">
                        <span class="material-icons-outlined text-gray-400 mr-3 mt-0.5">phone</span>
                        <div>
                            <p class="text-sm text-gray-500">No. HP/WhatsApp</p>
                            <p class="font-medium">{{ $pegawai->no_hp }}</p>
                        </div>
                    </div>
                    @endif

                    @if($pegawai->email)
                    <div class="flex items-start">
                        <span class="material-icons-outlined text-gray-400 mr-3 mt-0.5">email</span>
                        <div>
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="font-medium">{{ $pegawai->email }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="flex items-start">
                        <span class="material-icons-outlined text-gray-400 mr-3 mt-0.5">location_on</span>
                        <div>
                            <p class="text-sm text-gray-500">Alamat</p>
                            <p class="font-medium">{{ $pegawai->alamat }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Informasi -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Data Pribadi -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-3">
                    <h3 class="font-medium text-gray-700">Data Pribadi</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm text-gray-500 mb-1">NIK</label>
                            <p class="font-medium text-gray-900">{{ $pegawai->nik }}</p>
                        </div>

                        @if($pegawai->nip)
                        <div>
                            <label class="block text-sm text-gray-500 mb-1">NIP</label>
                            <p class="font-medium text-gray-900">{{ $pegawai->nip }}</p>
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm text-gray-500 mb-1">Jenis Kelamin</label>
                            <p class="font-medium text-gray-900">{{ $pegawai->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-500 mb-1">Tempat, Tanggal Lahir</label>
                            <p class="font-medium text-gray-900">
                                {{ $pegawai->tempat_lahir }}, {{ $pegawai->tanggal_lahir?->format('d F Y') }}
                                @if($pegawai->tanggal_lahir)
                                    <span class="text-gray-500">({{ $pegawai->umur }} tahun)</span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-500 mb-1">Agama</label>
                            <p class="font-medium text-gray-900">{{ $pegawai->agama }}</p>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-500 mb-1">Status Pernikahan</label>
                            <p class="font-medium text-gray-900">{{ $pegawai->status_pernikahan }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Pendidikan -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-3">
                    <h3 class="font-medium text-gray-700">Data Pendidikan</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm text-gray-500 mb-1">Pendidikan Terakhir</label>
                            <p class="font-medium text-gray-900">{{ $pegawai->pendidikan_terakhir }}</p>
                        </div>

                        @if($pegawai->jurusan)
                        <div>
                            <label class="block text-sm text-gray-500 mb-1">Jurusan/Program Studi</label>
                            <p class="font-medium text-gray-900">{{ $pegawai->jurusan }}</p>
                        </div>
                        @endif

                        @if($pegawai->institusi)
                        <div>
                            <label class="block text-sm text-gray-500 mb-1">Asal Institusi</label>
                            <p class="font-medium text-gray-900">{{ $pegawai->institusi }}</p>
                        </div>
                        @endif

                        @if($pegawai->tahun_lulus)
                        <div>
                            <label class="block text-sm text-gray-500 mb-1">Tahun Lulus</label>
                            <p class="font-medium text-gray-900">{{ $pegawai->tahun_lulus }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Data Kepegawaian -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-3">
                    <h3 class="font-medium text-gray-700">Data Kepegawaian</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">                        <div>                            <label class="block text-sm text-gray-500 mb-1">Jabatan Utama</label>
                            @php
                                $jabatanUtama = $pegawai->jabatan_utama;
                                $allJabatans = $pegawai->jabatansAktif;
                            @endphp
                            
                            @if($jabatanUtama)
                                <p class="font-medium text-gray-900">
                                    {{ $jabatanUtama->nama_jabatan }}
                                    <span class="ml-2 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        UTAMA
                                    </span>
                                </p>
                                <p class="text-sm text-gray-600 mt-1">
                                    Level {{ $jabatanUtama->level_jabatan }} - {{ $jabatanUtama->kategori_nama }}
                                </p>
                                
                                <!-- Jabatan Tambahan -->
                                @if($allJabatans && $allJabatans->where('pivot.is_jabatan_utama', false)->count() > 0)
                                    <div class="mt-3">
                                        <label class="block text-sm text-gray-500 mb-1">Jabatan Tambahan</label>
                                        <ul class="list-disc ml-5">
                                            @foreach($allJabatans->where('pivot.is_jabatan_utama', false) as $jabatanTambahan)
                                                <li class="text-sm text-gray-700">
                                                    {{ $jabatanTambahan->nama_jabatan }}
                                                    @if($jabatanTambahan->bidang)
                                                        ({{ $jabatanTambahan->bidang->nama_bidang }})
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                
                                @if($allJabatans->count() > 1)
                                    <div class="mt-2 space-y-1">
                                        <p class="text-xs text-gray-500">Jabatan Tambahan:</p>
                                        @foreach($allJabatans as $jabatan)
                                            @php
                                                $pivotData = $pegawai->pegawaiJabatans->where('jabatan_id', $jabatan->id)->first();
                                            @endphp
                                            @if($pivotData && !$pivotData->is_jabatan_utama)
                                            <p class="text-sm text-gray-700">
                                                • {{ $jabatan->nama_jabatan }}
                                                <span class="text-xs text-gray-500">(Level {{ $jabatan->level_jabatan }})</span>
                                            </p>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            @elseif($pegawai->jabatan)
                                <p class="font-medium text-gray-900">{{ $pegawai->jabatan->nama_jabatan }}</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    Level {{ $pegawai->jabatan->level_jabatan }} - {{ $pegawai->jabatan->kategori_nama }}
                                </p>
                            @else
                                <p class="font-medium text-gray-900">{{ $pegawai->jabatan ?: '-' }}</p>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm text-gray-500 mb-1">Bidang/Divisi</label>
                            @if($jabatanUtama && $jabatanUtama->bidang)
                                <p class="font-medium text-gray-900">{{ $jabatanUtama->bidang->nama_bidang }}</p>
                            @elseif($pegawai->jabatan && $pegawai->jabatan->bidang)
                                <p class="font-medium text-gray-900">{{ $pegawai->jabatan->bidang->nama_bidang }}</p>
                            @else
                                <p class="font-medium text-gray-900">{{ $pegawai->divisi ?: '-' }}</p>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm text-gray-500 mb-1">Tanggal Masuk</label>
                            <p class="font-medium text-gray-900">{{ $pegawai->tanggal_masuk?->format('d F Y') }}</p>
                            @if($pegawai->tanggal_masuk)
                                <p class="text-sm text-gray-600 mt-1">
                                    Masa kerja: {{ $pegawai->tanggal_masuk->diffForHumans() }}
                                </p>
                            @endif
                        </div>

                        @if($pegawai->tanggal_keluar)
                        <div>
                            <label class="block text-sm text-gray-500 mb-1">Tanggal Keluar</label>
                            <p class="font-medium text-gray-900">{{ $pegawai->tanggal_keluar?->format('d F Y') }}</p>
                        </div>
                        @endif

                        @if($pegawai->gaji_pokok && $pegawai->gaji_pokok > 0)
                        <div>
                            <label class="block text-sm text-gray-500 mb-1">Gaji Pokok</label>
                            <p class="font-medium text-gray-900">Rp {{ number_format($pegawai->gaji_pokok, 0, ',', '.') }}</p>
                        </div>
                        @endif
                    </div>

                    @if($pegawai->keterangan)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <label class="block text-sm text-gray-500 mb-2">Keterangan Tambahan</label>
                        <p class="text-gray-900">{{ $pegawai->keterangan }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Riwayat Dompet (jika ada) -->
            @if($pegawai->dompet)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-3">
                    <h3 class="font-medium text-gray-700">Informasi Dompet</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm text-gray-500 mb-1">Saldo Dompet</label>
                            <p class="font-medium text-gray-900">Rp {{ number_format($pegawai->dompet->saldo, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-500 mb-1">Status Dompet</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $pegawai->dompet->status == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($pegawai->dompet->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
