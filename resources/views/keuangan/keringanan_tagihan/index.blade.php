@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
                <span class="material-icons-outlined text-blue-600">price_check</span>
                Keringanan Tagihan
            </h1>
            <p class="text-gray-600 mt-2">Kelola keringanan tagihan untuk santri pesantren tahun ajaran {{ $activeTahunAjaran->nama_tahun_ajaran }}.</p>
        </div>
        <button onclick="openCreateModal()" class="mt-4 sm:mt-0 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
            <span class="material-icons-outlined text-sm mr-2">add</span>
            Tambah Keringanan
        </button>    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg onclick="this.parentElement.parentElement.remove()" class="fill-current h-6 w-6 text-green-500 cursor-pointer" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <title>Close</title>
                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                </svg>
            </span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg onclick="this.parentElement.parentElement.remove()" class="fill-current h-6 w-6 text-red-500 cursor-pointer" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <title>Close</title>
                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                </svg>
            </span>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg onclick="this.parentElement.parentElement.remove()" class="fill-current h-6 w-6 text-red-500 cursor-pointer" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <title>Close</title>
                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                </svg>
            </span>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-800 mb-4">Daftar Santri dengan Keringanan</h2>
                
                @if($santrisWithKeringanan->isEmpty())
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="text-purple-500 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Keringanan</h3>
                        <p class="text-gray-500">Belum ada santri yang mendapatkan keringanan tagihan</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Santri</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asrama</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Keringanan</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($santrisWithKeringanan as $santri)
                                    <tr onclick="openDetailModal({{ $santri->id }})" class="hover:bg-gray-50 cursor-pointer">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $santri->nama_santri }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        NIS: {{ $santri->nis }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $santri->kelasRelasi->pluck('nama')->join(', ') ?: 'Belum ada kelas' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $santri->asrama_anggota_terakhir ? $santri->asrama_anggota_terakhir->asrama->nama_asrama : 'Belum ada asrama' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($santri->keringananTagihans->where('status', 'aktif')->count() > 0)
                                                @foreach($santri->keringananTagihans->where('status', 'aktif')->take(1) as $keringanan)
                                                    @switch($keringanan->jenis_keringanan)
                                                        @case('potongan_persen')
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                Diskon {{ $keringanan->nilai_potongan }}%
                                                            </span>
                                                            @break
                                                        @case('potongan_nominal')
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                Potongan Rp {{ number_format($keringanan->nilai_potongan) }}
                                                            </span>
                                                            @break
                                                        @case('pembebasan')
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                                Pembebasan Biaya
                                                            </span>
                                                            @break
                                                        @case('bayar_satu_gratis_satu')
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                                2 Santri Bayar 1
                                                            </span>
                                                            @break
                                                    @endswitch
                                                    @if($santri->keringananTagihans->where('status', 'aktif')->count() > 1)
                                                        <span class="text-xs text-gray-500">(+{{ $santri->keringananTagihans->where('status', 'aktif')->count() - 1 }} lainnya)</span>
                                                    @endif
                                                @endforeach
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($santri->keringananTagihans->where('status', 'aktif')->count() > 0)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    Nonaktif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                            <button onclick="openDetailModal({{ $santri->id }})" class="text-indigo-600 hover:text-indigo-900 mx-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>    <!-- Modal Tambah Keringanan -->
    <div id="createModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity"></div>
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl z-10 overflow-hidden transform transition-all">
            <div class="px-6 py-4 bg-blue-600 text-white flex justify-between items-center border-b border-blue-700">
                <h3 class="text-lg font-medium flex items-center">
                    <span class="material-icons-outlined mr-2">add_circle</span>
                    Tambah Keringanan Baru
                </h3>
                <button onclick="closeCreateModal()" class="text-white hover:text-gray-200 focus:outline-none">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>            <form id="createKeringananForm" action="{{ route('keuangan.keringanan-tagihan.store') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="tahun_ajaran_id" value="{{ $activeTahunAjaran->id }}">
                <div class="mb-4">                    <label for="santri_id" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <span class="material-icons-outlined text-gray-500 mr-1 text-sm">person</span>
                        Santri
                    </label>
                    <div class="relative">
                        <select id="santri_id" name="santri_id" class="mt-1 block w-full pl-3 pr-10 py-2.5 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg bg-white" required>
                            <option value="">-- Pilih Santri --</option>
                            @foreach($allSantris as $santri)
                                <option value="{{ $santri->id }}">{{ $santri->nama_santri }} ({{ $santri->nis }}) - {{ $santri->kelasRelasi->pluck('nama')->join(', ') }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                            <span class="material-icons-outlined text-sm">expand_more</span>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="jenis_keringanan" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <span class="material-icons-outlined text-gray-500 mr-1 text-sm">category</span>
                        Jenis Keringanan
                    </label>
                    <div class="relative">
                        <select id="jenis_keringanan" name="jenis_keringanan" onchange="toggleKeringananFields()" class="mt-1 block w-full pl-3 pr-10 py-2.5 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg bg-white" required>                        <option value="">-- Pilih Jenis Keringanan --</option>
                        <option value="potongan_persen">Diskon Persentase</option>
                        <option value="potongan_nominal">Potongan Nominal</option>
                        <option value="pembebasan">Pembebasan Biaya (Gratis)</option>
                        <option value="bayar_satu_gratis_satu">2 Santri Bayar 1</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                            <span class="material-icons-outlined text-sm">expand_more</span>
                        </div>
                    </div>
                </div>
                <div id="nilai_potongan_container" class="mb-4 hidden">
                    <label for="nilai_potongan" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <span class="material-icons-outlined text-gray-500 mr-1 text-sm">attach_money</span>
                        Nilai Potongan
                    </label>
                    <div class="mt-1 flex rounded-lg shadow-sm">
                        <span id="nilai_prefix" class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                            Rp
                        </span>
                        <input type="number" name="nilai_potongan" id="nilai_potongan" class="focus:ring-blue-500 focus:border-blue-500 flex-1 block w-full rounded-none rounded-r-lg sm:text-sm border-gray-300" placeholder="0">
                    </div>
                </div>                <div class="mb-4">
                    <label for="jenis_tagihan_id" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <span class="material-icons-outlined text-gray-500 mr-1 text-sm">receipt</span>
                        Jenis Tagihan (Opsional)
                    </label>
                    <div class="relative">
                        <select id="jenis_tagihan_id" name="jenis_tagihan_id" class="mt-1 block w-full pl-3 pr-10 py-2.5 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg bg-white">
                            <option value="">-- Semua Jenis Tagihan --</option>
                            @foreach($jenisTagihans as $jenisTagihan)
                                <option value="{{ $jenisTagihan->id }}">{{ $jenisTagihan->nama }} ({{ $jenisTagihan->kategori_tagihan }})</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                            <span class="material-icons-outlined text-sm">expand_more</span>
                        </div>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Kosongkan untuk memberikan keringanan pada semua jenis tagihan</p>
                </div>                <div id="santri_tertanggung_container" class="mb-4 hidden">
                    <label for="santri_tertanggung_id" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <span class="material-icons-outlined text-gray-500 mr-1 text-sm">person_add</span>
                        Santri yang Ditanggung
                    </label>
                    <div class="relative">
                        <select id="santri_tertanggung_id" name="santri_tertanggung_id" class="mt-1 block w-full pl-3 pr-10 py-2.5 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg bg-white">
                            <option value="">-- Pilih Santri yang Ditanggung --</option>
                            @foreach($allSantris as $santri)
                                <option value="{{ $santri->id }}">{{ $santri->nama_santri }} ({{ $santri->nis }}) - {{ $santri->kelasRelasi->pluck('nama')->join(', ') }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                            <span class="material-icons-outlined text-sm">expand_more</span>
                        </div>
                    </div>
                </div>                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                            <span class="material-icons-outlined text-gray-500 mr-1 text-sm">event</span>
                            Tanggal Mulai (Opsional)
                        </label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                            <span class="material-icons-outlined text-gray-500 mr-1 text-sm">event_busy</span>
                            Tanggal Selesai (Opsional)
                        </label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg">
                    </div>
                </div>                <div class="mb-4">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <span class="material-icons-outlined text-gray-500 mr-1 text-sm">description</span>
                        Keterangan (Opsional)
                    </label>                    <textarea name="keterangan" id="keterangan" rows="3" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg" placeholder="Keterangan tambahan"></textarea>
                </div>
                
                <div class="mt-6 flex justify-end border-t border-gray-200 pt-4">
                    <button type="button" onclick="closeCreateModal()" class="bg-gray-100 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-200 transition duration-150 ease-in-out mr-2 flex items-center">
                        <span class="material-icons-outlined mr-1 text-sm">cancel</span>
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out flex items-center">
                        <span class="material-icons-outlined mr-1 text-sm">save</span>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal Detail Keringanan -->
    <div id="detailModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity"></div>        <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl z-10 overflow-hidden transform transition-all">
            <div class="px-6 py-4 bg-blue-600 text-white flex justify-between items-center border-b border-blue-700">
                <h3 class="text-lg font-medium flex items-center">
                    <span class="material-icons-outlined mr-2">info</span>
                    Detail Keringanan Tagihan
                </h3>
                <button onclick="closeDetailModal()" class="text-white hover:text-gray-200 focus:outline-none">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="p-6 max-h-[80vh] overflow-y-auto" id="detailContent">
                <div class="flex justify-center items-center h-32">
                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-purple-600"></div>
                </div>
            </div>
        </div>
    </div>    @push('scripts')
    <script>
        // Form handling
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('createKeringananForm');
            if (form) {                form.addEventListener('submit', function(e) {
                    console.log('Form submit triggered');
                    
                    // Validasi dasar
                    const santriId = document.getElementById('santri_id').value;
                    const jenisKeringanan = document.getElementById('jenis_keringanan').value;
                    
                    if (!santriId) {
                        e.preventDefault();
                        alert('Harap pilih santri terlebih dahulu');
                        return false;
                    }
                    
                    if (!jenisKeringanan) {
                        e.preventDefault();
                        alert('Harap pilih jenis keringanan terlebih dahulu');
                        return false;
                    }
                    
                    // Bersihkan field yang tidak diperlukan sebelum submit
                    const nilaiPotonganField = document.getElementById('nilai_potongan');
                    const santriTertanggungField = document.getElementById('santri_tertanggung_id');
                    
                    // Reset nilai field yang tidak diperlukan
                    if (jenisKeringanan !== 'potongan_persen' && jenisKeringanan !== 'potongan_nominal') {
                        nilaiPotonganField.value = '';
                    }
                    
                    if (jenisKeringanan !== 'bayar_satu_gratis_satu') {
                        santriTertanggungField.value = '';
                    }
                    
                    // Validasi nilai potongan jika diperlukan
                    if (jenisKeringanan === 'potongan_persen' || jenisKeringanan === 'potongan_nominal') {
                        const nilaiPotongan = nilaiPotonganField.value;
                        if (!nilaiPotongan || parseFloat(nilaiPotongan) <= 0) {
                            e.preventDefault();
                            alert('Harap masukkan nilai potongan yang valid');
                            return false;
                        }
                        
                        if (jenisKeringanan === 'potongan_persen' && parseFloat(nilaiPotongan) > 100) {
                            e.preventDefault();
                            alert('Persentase potongan tidak boleh lebih dari 100%');
                            return false;
                        }
                    } else if (jenisKeringanan === 'pembebasan' || jenisKeringanan === 'bayar_satu_gratis_satu') {
                        // Set nilai default 0 untuk jenis keringanan pembebasan biaya atau 2 santri bayar 1
                        nilaiPotonganField.value = '0';
                    }
                    
                    // Validasi santri tertanggung untuk jenis bayar_satu_gratis_satu
                    if (jenisKeringanan === 'bayar_satu_gratis_satu') {
                        const santriTertanggungId = santriTertanggungField.value;
                        if (!santriTertanggungId) {
                            e.preventDefault();
                            alert('Harap pilih santri yang ditanggung untuk keringanan 2 Santri Bayar 1');
                            return false;
                        }
                        
                        if (santriTertanggungId === santriId) {
                            e.preventDefault();
                            alert('Santri yang ditanggung tidak boleh sama dengan santri pembayar');
                            return false;
                        }
                    }
                    
                    console.log('Form validation passed, submitting...');
                    // Form akan submit secara normal jika sampai di sini
                });
            }
        });
        
        function openCreateModal() {
            document.getElementById('createModal').classList.remove('hidden');
            // Reset form saat modal dibuka
            const form = document.getElementById('createKeringananForm');
            if (form) {
                form.reset();
                toggleKeringananFields(); // Hide conditional fields
            }
        }
        
        function closeCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
        }
          function toggleKeringananFields() {
            const jenisKeringanan = document.getElementById('jenis_keringanan').value;
            const nilaiContainer = document.getElementById('nilai_potongan_container');
            const santriTertanggungContainer = document.getElementById('santri_tertanggung_container');
            const nilaiPrefix = document.getElementById('nilai_prefix');
            const nilaiPotonganField = document.getElementById('nilai_potongan');
            const santriTertanggungField = document.getElementById('santri_tertanggung_id');
            
            // Reset visibility and clear values
            nilaiContainer.classList.add('hidden');
            santriTertanggungContainer.classList.add('hidden');
            nilaiPotonganField.value = '';
            santriTertanggungField.value = '';
            
            // Show relevant fields based on selection
            if (jenisKeringanan === 'potongan_persen') {
                nilaiContainer.classList.remove('hidden');
                nilaiPrefix.textContent = '%';
            } else if (jenisKeringanan === 'potongan_nominal') {
                nilaiContainer.classList.remove('hidden');
                nilaiPrefix.textContent = 'Rp';
            } else if (jenisKeringanan === 'bayar_satu_gratis_satu') {
                santriTertanggungContainer.classList.remove('hidden');
            }
        }
        
        function openDetailModal(santriId) {
            document.getElementById('detailModal').classList.remove('hidden');
            document.getElementById('detailContent').innerHTML = `
                <div class="flex justify-center items-center h-32">
                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-purple-600"></div>
                </div>
            `;
            
            // Fetch keringanan data
            fetch(`/keuangan/keringanan-santri/${santriId}`)
                .then(response => response.json())
                .then(data => {
                    renderDetailModal(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('detailContent').innerHTML = `
                        <div class="text-center text-red-500">
                            <p>Terjadi kesalahan saat memuat data</p>
                        </div>
                    `;
                });
        }
        
        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }
        
        function renderDetailModal(data) {
            const { santri, keringanan, tagihan } = data;
            
            let keringananItems = '';
            if (keringanan.length > 0) {
                keringanan.forEach(item => {
                    let jenisKeringananText = '';
                    let nilaiPotonganText = '';
                    
                    switch(item.jenis_keringanan) {
                        case 'potongan_persen':
                            jenisKeringananText = 'Diskon Persentase';
                            nilaiPotonganText = `${item.nilai_potongan}%`;
                            break;
                        case 'potongan_nominal':
                            jenisKeringananText = 'Potongan Nominal';
                            nilaiPotonganText = `Rp ${numberFormat(item.nilai_potongan)}`;
                            break;
                        case 'pembebasan':
                            jenisKeringananText = 'Pembebasan Biaya';
                            nilaiPotonganText = 'Gratis';
                            break;
                        case 'bayar_satu_gratis_satu':
                            jenisKeringananText = '2 Santri Bayar 1';
                            nilaiPotonganText = item.santri_tertanggung ? `Menanggung: ${item.santri_tertanggung.nama_santri}` : '-';
                            break;
                    }
                    
                    const statusBadge = item.status === 'aktif' 
                        ? `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>`
                        : `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Nonaktif</span>`;
                        
                    const jenisTagihanText = item.jenis_tagihan ? item.jenis_tagihan.nama : 'Semua Jenis Tagihan';
                    
                    keringananItems += `
                        <tr>
                            <td class="px-4 py-3 text-sm">${jenisKeringananText}</td>
                            <td class="px-4 py-3 text-sm">${nilaiPotonganText}</td>
                            <td class="px-4 py-3 text-sm">${jenisTagihanText}</td>
                            <td class="px-4 py-3 text-sm">${statusBadge}</td>
                            <td class="px-4 py-3 text-sm">
                                <form action="/keuangan/keringanan-tagihan/${item.id}" method="POST" class="inline-flex">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="${item.status === 'aktif' ? 'nonaktif' : 'aktif'}">
                                    <button type="submit" class="text-${item.status === 'aktif' ? 'red' : 'green'}-600 hover:text-${item.status === 'aktif' ? 'red' : 'green'}-900">
                                        ${item.status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan'}
                                    </button>
                                </form>
                                <form action="/keuangan/keringanan-tagihan/${item.id}" method="POST" class="inline-flex ml-3">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Yakin ingin menghapus keringanan ini?')" class="text-red-600 hover:text-red-900">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    `;
                });
            } else {
                keringananItems = `
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">Tidak ada data keringanan</td>
                    </tr>
                `;
            }
            
            let tagihanItems = '';
            if (tagihan.length > 0) {
                tagihan.forEach(item => {
                    const statusBadge = getStatusBadge(item.status_pembayaran);
                    
                    tagihanItems += `
                        <tr>
                            <td class="px-4 py-3 text-sm">${item.jenis_tagihan}</td>
                            <td class="px-4 py-3 text-sm">${item.bulan}</td>
                            <td class="px-4 py-3 text-sm">Rp ${numberFormat(item.nominal_tagihan)}</td>
                            <td class="px-4 py-3 text-sm">Rp ${numberFormat(item.nominal_keringanan)}</td>
                            <td class="px-4 py-3 text-sm">Rp ${numberFormat(item.nominal_harus_dibayar)}</td>
                            <td class="px-4 py-3 text-sm">Rp ${numberFormat(item.nominal_dibayar)}</td>
                            <td class="px-4 py-3 text-sm">Rp ${numberFormat(item.sisa_tagihan)}</td>
                            <td class="px-4 py-3 text-sm">${statusBadge}</td>
                        </tr>
                    `;
                });
            } else {
                tagihanItems = `
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-gray-500">Tidak ada tagihan dengan keringanan</td>
                    </tr>
                `;
            }
            
            document.getElementById('detailContent').innerHTML = `
                <div class="mb-6">
                    <h4 class="font-medium text-gray-900 mb-2">Informasi Santri</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm"><span class="font-medium text-gray-700">Nama:</span> ${santri.nama_santri}</p>
                            <p class="text-sm"><span class="font-medium text-gray-700">NIS:</span> ${santri.nis}</p>
                        </div>
                        <div>
                            <p class="text-sm"><span class="font-medium text-gray-700">Kelas:</span> ${santri.kelas}</p>
                            <p class="text-sm"><span class="font-medium text-gray-700">Asrama:</span> ${santri.asrama}</p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-6">
                    <h4 class="font-medium text-gray-900 mb-2">Daftar Keringanan</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full whitespace-nowrap">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Keringanan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Tagihan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                ${keringananItems}
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">Tagihan dengan Keringanan</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full whitespace-nowrap">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Tagihan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal Tagihan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Keringanan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harus Dibayar</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sudah Dibayar</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Tagihan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                ${tagihanItems}
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
        }
        
        function numberFormat(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }
        
        function getStatusBadge(status) {
            switch(status) {
                case 'lunas':
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Lunas</span>';
                case 'sebagian':
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Sebagian</span>';
                case 'belum_bayar':
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Belum Bayar</span>';
                default:
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">-</span>';
            }
        }
    </script>
    @endpush
@endsection
