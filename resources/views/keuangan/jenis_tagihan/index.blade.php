@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto py-6">
    {{-- Header + Actions --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 pb-4 border-b border-gray-200">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-purple-600" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Jenis Tagihan
            </h1>
            <p class="text-sm text-gray-500 mt-1">Kelola jenis tagihan dan nominal pembayaran untuk santri.</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button onclick="openAutomationModal()"
               class="w-full sm:w-auto bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-150 ease-in-out flex items-center justify-center gap-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
                Otomatisasi Tagihan
            </button>
            <button onclick="openCreateModal()"
               class="w-full sm:w-auto bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition duration-150 ease-in-out flex items-center justify-center gap-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Tambah Jenis Tagihan
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- Data Table --}}
    <div class="overflow-x-auto bg-white rounded-xl shadow-lg border border-gray-200 mt-2">
        <div class="w-full overflow-x-auto">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700">Daftar Jenis Tagihan</h3>
                </div>

                <table class="min-w-full text-sm text-left text-gray-700">
                        <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-600 sticky top-0 z-10">
                            <tr>
                                <th class="px-4 py-3 text-left">Nama Tagihan</th>
                                <th class="px-4 py-3 text-center">Nominal Default</th>
                                <th class="px-4 py-3 text-center">Kategori</th>
                                <th class="px-4 py-3 text-center">Tipe Pembayaran</th>
                                <th class="px-4 py-3 text-center">Buku Kas</th>
                                <th class="px-4 py-3 text-center">Jatuh Tempo</th>
                                <th class="px-4 py-3 text-center">Nominal per Kelas</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>                        <tbody class="divide-y divide-gray-200">
                            @forelse($jenisTagihans as $tagihan)
                            <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                <td class="px-4 py-3">
                                    <div class="flex items-center text-sm">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $tagihan->nama }}</p>
                                            @if($tagihan->deskripsi)
                                                <p class="text-xs text-gray-600">{{ $tagihan->deskripsi }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center font-medium text-gray-900">
                                    Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full {{ $tagihan->kategori_tagihan == 'Rutin' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                        {{ $tagihan->kategori_tagihan }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full {{ $tagihan->is_bulanan ? 'bg-green-100 text-green-700' : 'bg-purple-100 text-purple-700' }}">
                                        {{ $tagihan->is_bulanan ? 'Bulanan' : 'Sekali Bayar' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($tagihan->bukuKas)
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">
                                        {{ $tagihan->bukuKas->nama_kas }}
                                    </span>
                                    @else
                                    <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($tagihan->kategori_tagihan == 'Rutin')
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                        Tanggal 10 setiap bulan
                                    </span>
                                    @else
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-700">
                                        @php
                                            $tanggal = $tagihan->tanggal_jatuh_tempo ?? 10;
                                            $bulanTambahan = $tagihan->bulan_jatuh_tempo ?? 0;
                                        @endphp
                                        <div class="flex flex-col">
                                            <span>Tanggal {{ $tanggal }}</span>
                                            @if($bulanTambahan > 0)
                                                <span class="text-xs mt-0.5">
                                                    @if($bulanTambahan == 1)
                                                        (bulan berikutnya)
                                                    @else
                                                        (+{{ $bulanTambahan }} bulan)
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($tagihan->is_nominal_per_kelas)
                                        <button onclick="window.location.href='{{ route('keuangan.jenis-tagihan.show-kelas', $tagihan->id) }}'"
                                            class="px-3 py-1 text-xs font-medium text-white bg-purple-600 border border-transparent rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-offset-2 transition-colors duration-150">
                                            Lihat Nominal
                                        </button>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center items-center gap-2">
                                        <form action="{{ route('keuangan.jenis-tagihan.generate', $tagihan->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" 
                                                    class="p-2 rounded-full text-green-600 hover:bg-green-100 transition duration-150 ease-in-out"
                                                    title="Generate Tagihan"
                                                    onclick="return confirm('Apakah Anda yakin ingin generate tagihan untuk {{ addslashes($tagihan->nama) }}? Ini akan membuat tagihan untuk semua santri yang belum memiliki tagihan ini.')">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path>
                                                </svg>
                                            </button>
                                        </form>
                                        <form action="{{ route('keuangan.jenis-tagihan.cancel', $tagihan->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" 
                                                    class="p-2 rounded-full text-orange-600 hover:bg-orange-100 transition duration-150 ease-in-out"
                                                    title="Batal Generate"
                                                    onclick="return confirm('Apakah Anda yakin ingin membatalkan tagihan untuk {{ addslashes($tagihan->nama) }}? Ini akan menghapus semua tagihan yang belum dibayar.')">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                </svg>
                                            </button>
                                        </form>
                                        <a href="#" onclick="openEditModal({{ $tagihan->id }})" 
                                           class="p-2 rounded-full text-purple-600 hover:bg-purple-100 transition duration-150 ease-in-out"
                                           title="Edit">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('keuangan.jenis-tagihan.destroy', $tagihan->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jenis tagihan {{ addslashes($tagihan->nama) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="p-2 rounded-full text-red-600 hover:bg-red-100 transition duration-150 ease-in-out"
                                                    title="Hapus">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-10 text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="font-semibold text-lg">Data Jenis Tagihan Tidak Ditemukan</p>
                                        <p class="text-sm">Belum ada jenis tagihan yang ditambahkan dalam sistem.</p>
                                        <button onclick="openCreateModal()" class="mt-4 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition duration-150 ease-in-out">
                                            Tambah Jenis Tagihan Pertama
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal for Add Jenis Tagihan --}}
<div id="createModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Tambah Jenis Tagihan</h3>
                <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <form id="createForm" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tagihan <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" id="nama" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                    <span class="text-red-500 text-xs hidden" id="error-nama"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"></textarea>
                    <span class="text-red-500 text-xs hidden" id="error-deskripsi"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                    <select name="kategori_tagihan" id="kategori_tagihan" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <option value="">Pilih Kategori</option>
                        <option value="Rutin">Rutin</option>
                        <option value="Insidental">Insidental</option>
                    </select>
                    <span class="text-red-500 text-xs hidden" id="error-kategori_tagihan"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Pembayaran <span class="text-red-500">*</span></label>
                    <select name="is_bulanan" id="is_bulanan" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <option value="">Pilih Tipe</option>
                        <option value="1">Bulanan</option>
                        <option value="0">Sekali Bayar</option>
                    </select>
                    <span class="text-red-500 text-xs hidden" id="error-is_bulanan"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Default <span class="text-red-500">*</span></label>
                    <input type="number" name="nominal" id="nominal" required min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                    <span class="text-red-500 text-xs hidden" id="error-nominal"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Per Kelas?</label>
                    <select name="is_nominal_per_kelas" id="is_nominal_per_kelas"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <option value="0">Tidak</option>
                        <option value="1">Ya</option>
                    </select>
                    <span class="text-red-500 text-xs hidden" id="error-is_nominal_per_kelas"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buku Kas <span class="text-red-500">*</span></label>
                    <select name="buku_kas_id" id="buku_kas_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <option value="">Pilih Buku Kas</option>
                        @foreach($bukuKasList as $bukuKas)
                        <option value="{{ $bukuKas->id }}">{{ $bukuKas->nama_kas }} ({{ $bukuKas->jenis_kas }})</option>
                        @endforeach
                    </select>
                    <span class="text-red-500 text-xs hidden" id="error-buku_kas_id"></span>
                </div>

                <div class="jatuh-tempo-settings">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Jatuh Tempo <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Tanggal (1-31)</label>
                            <input type="number" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" required min="1" max="31" value="10"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            <span class="text-red-500 text-xs hidden" id="error-tanggal_jatuh_tempo"></span>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Tambahan Bulan (opsional)</label>
                            <select name="bulan_jatuh_tempo" id="bulan_jatuh_tempo"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                <option value="0" selected>Bulan yang sama</option>
                                <option value="1">Bulan berikutnya</option>
                                <option value="2">2 bulan kemudian</option>
                                <option value="3">3 bulan kemudian</option>
                                <option value="6">6 bulan kemudian</option>
                            </select>
                            <span class="text-red-500 text-xs hidden" id="error-bulan_jatuh_tempo"></span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Untuk tagihan <strong>rutin</strong>, jatuh tempo selalu tanggal 10 setiap bulan. 
                        Untuk tagihan <strong>insidental</strong>, Anda bisa memilih tanggal dan bulan jatuh tempo.
                    </p>
                </div>
            </form>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end pt-4 space-x-2">
                <button onclick="closeCreateModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-150 ease-in-out">
                    Batal
                </button>
                <button onclick="submitForm()" 
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition duration-150 ease-in-out flex items-center gap-2">
                    <svg class="w-4 h-4 hidden animate-spin" id="loading-spinner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span id="submit-text">Simpan</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal for Edit Jenis Tagihan --}}
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Edit Jenis Tagihan</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <form id="editForm" class="mt-4 space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_id">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tagihan <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" id="edit_nama" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                    <span class="text-red-500 text-xs hidden" id="edit-error-nama"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" id="edit_deskripsi" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"></textarea>
                    <span class="text-red-500 text-xs hidden" id="edit-error-deskripsi"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                    <select name="kategori_tagihan" id="edit_kategori_tagihan" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <option value="">Pilih Kategori</option>
                        <option value="Rutin">Rutin</option>
                        <option value="Insidental">Insidental</option>
                    </select>
                    <span class="text-red-500 text-xs hidden" id="edit-error-kategori_tagihan"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Pembayaran <span class="text-red-500">*</span></label>
                    <select name="is_bulanan" id="edit_is_bulanan" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <option value="">Pilih Tipe</option>
                        <option value="1">Bulanan</option>
                        <option value="0">Sekali Bayar</option>
                    </select>
                    <span class="text-red-500 text-xs hidden" id="edit-error-is_bulanan"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Default <span class="text-red-500">*</span></label>
                    <input type="number" name="nominal" id="edit_nominal" required min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                    <span class="text-red-500 text-xs hidden" id="edit-error-nominal"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Per Kelas?</label>
                    <select name="is_nominal_per_kelas" id="edit_is_nominal_per_kelas"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <option value="0">Tidak</option>
                        <option value="1">Ya</option>
                    </select>
                    <span class="text-red-500 text-xs hidden" id="edit-error-is_nominal_per_kelas"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buku Kas <span class="text-red-500">*</span></label>
                    <select name="buku_kas_id" id="edit_buku_kas_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <option value="">Pilih Buku Kas</option>
                        @foreach($bukuKasList as $bukuKas)
                        <option value="{{ $bukuKas->id }}">{{ $bukuKas->nama_kas }} ({{ $bukuKas->jenis_kas }})</option>
                        @endforeach
                    </select>
                    <span class="text-red-500 text-xs hidden" id="edit-error-buku_kas_id"></span>
                </div>

                <div class="jatuh-tempo-settings">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Jatuh Tempo <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Tanggal (1-31)</label>
                            <input type="number" name="tanggal_jatuh_tempo" id="edit_tanggal_jatuh_tempo" required min="1" max="31" value="10"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            <span class="text-red-500 text-xs hidden" id="edit-error-tanggal_jatuh_tempo"></span>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Tambahan Bulan (opsional)</label>
                            <select name="bulan_jatuh_tempo" id="edit_bulan_jatuh_tempo"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                <option value="0">Bulan yang sama</option>
                                <option value="1">Bulan berikutnya</option>
                                <option value="2">2 bulan kemudian</option>
                                <option value="3">3 bulan kemudian</option>
                                <option value="6">6 bulan kemudian</option>
                            </select>
                            <span class="text-red-500 text-xs hidden" id="edit-error-bulan_jatuh_tempo"></span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Untuk tagihan <strong>rutin</strong>, jatuh tempo selalu tanggal 10 setiap bulan. 
                        Untuk tagihan <strong>insidental</strong>, Anda bisa memilih tanggal dan bulan jatuh tempo.
                    </p>
                </div>
            </form>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end pt-4 space-x-2">
                <button onclick="closeEditModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-150 ease-in-out">
                    Batal
                </button>
                <button onclick="submitEditForm()" 
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition duration-150 ease-in-out flex items-center gap-2">
                    <svg class="w-4 h-4 hidden animate-spin" id="edit-loading-spinner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span id="edit-submit-text">Simpan Perubahan</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Otomatisasi Tagihan -->
<div id="automationModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    Otomatisasi Tagihan Rutin
                </h3>
                <button onclick="closeAutomationModal()" class="text-green-100 hover:text-white transition duration-150 ease-in-out">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="px-6 py-6 max-h-[70vh] overflow-y-auto">
                <!-- Informasi -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Otomatisasi Tagihan Rutin:</strong> Fitur ini akan menyalin semua tagihan rutin dari tahun ajaran sebelumnya ke tahun ajaran yang aktif saat ini. Hanya berlaku untuk santri yang masih aktif.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Otomatisasi -->
                <form id="automationForm" onsubmit="submitAutomation(event)">
                    @csrf
                    
                    <!-- Pilihan Tahun Ajaran -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="tahun_ajaran_asal" class="block text-sm font-medium text-gray-700 mb-2">
                                Tahun Ajaran Asal
                            </label>
                            <select id="tahun_ajaran_asal" name="tahun_ajaran_asal" 
                                    class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" required>
                                <option value="">Pilih Tahun Ajaran Asal</option>
                                @foreach($tahunAjarans ?? [] as $ta)
                                    @if(!$ta->is_active)
                                    <option value="{{ $ta->id }}">{{ $ta->nama_tahun_ajaran }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="tahun_ajaran_tujuan" class="block text-sm font-medium text-gray-700 mb-2">
                                Tahun Ajaran Tujuan
                            </label>
                            <select id="tahun_ajaran_tujuan" name="tahun_ajaran_tujuan" 
                                    class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 bg-gray-100" readonly>
                                @foreach($tahunAjarans ?? [] as $ta)
                                    @if($ta->is_active)
                                    <option value="{{ $ta->id }}" selected>{{ $ta->nama_tahun_ajaran }} (Aktif)</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Filter Kategori -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Filter Kategori Tagihan
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="kategori_tagihan[]" value="Rutin" 
                                       class="rounded border-gray-300 text-green-600 focus:ring-green-500" checked>
                                <span class="ml-2 text-sm text-gray-700">Tagihan Rutin</span>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Hanya tagihan rutin yang akan disalin secara otomatis</p>
                    </div>

                    <!-- Opsi Tambahan -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Opsi Tambahan
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="replace_existing" value="1" 
                                       class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <span class="ml-2 text-sm text-gray-700">Ganti tagihan yang sudah ada</span>
                            </label>
                            <p class="text-xs text-gray-500 ml-6">Jika dicentang, tagihan yang sudah ada akan diganti dengan yang baru</p>
                        </div>
                    </div>

                    <!-- Preview Section -->
                    <div id="previewSection" class="hidden mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3">Preview Data yang akan Disalin</h4>
                        <div id="previewContent" class="bg-gray-50 rounded-lg p-4">
                            <!-- Preview content will be loaded here -->
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-between items-center pt-4">
                        <button type="button" onclick="previewAutomation()" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Preview
                        </button>
                        
                        <div class="flex gap-2">
                            <button type="button" onclick="closeAutomationModal()" 
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-150 ease-in-out">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-150 ease-in-out flex items-center gap-2">
                                <svg class="w-4 h-4 hidden animate-spin" id="automation-loading-spinner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <span id="automation-submit-text">Jalankan Otomatisasi</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
    clearForm();
    
    // Setup event listener untuk kategori tagihan
    document.getElementById('kategori_tagihan').addEventListener('change', handleJatuhTempoDisplay);
    // Inisialisasi tampilan jatuh tempo
    handleJatuhTempoDisplay();
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
    clearForm();
}

function openEditModal(id) {
    console.log('Opening edit modal for ID:', id);
    
    // Show loading state
    const editModal = document.getElementById('editModal');
    if (editModal) {
        editModal.classList.remove('hidden');
        // Could add a loading spinner here
    }
    
    // Fetch data for the selected jenis tagihan
    fetch(`/keuangan/jenis-tagihan/${id}/edit`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (response.status === 401) {
            // Try to parse JSON first for Laravel's default 401 response
            return response.json().then(data => {
                alert('Sesi Anda telah berakhir. Silakan login kembali.');
                window.location.href = '/login';
                return null;
            }).catch(() => {
                alert('Sesi Anda telah berakhir. Silakan login kembali.');
                window.location.href = '/login';
                return null;
            });
        }
        
        if (response.status === 403) {
            return response.json().then(data => {
                alert('Anda tidak memiliki akses untuk melakukan tindakan ini.');
                return null;
            }).catch(() => {
                alert('Anda tidak memiliki akses untuk melakukan tindakan ini.');
                return null;
            });
        }
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return response.json();
    })
    .then(data => {
        if (!data) return; // Handle early returns from auth errors
        
        console.log('Received data:', data);
        
        if (data.success) {
            console.log('JenisTagihan data:', data.jenisTagihan);
            console.log('BukuKas list:', data.bukuKasList);
            
            // Populate form fields
            document.getElementById('edit_id').value = data.jenisTagihan.id;
            document.getElementById('edit_nama').value = data.jenisTagihan.nama;
            document.getElementById('edit_deskripsi').value = data.jenisTagihan.deskripsi || '';
            document.getElementById('edit_kategori_tagihan').value = data.jenisTagihan.kategori_tagihan;
            document.getElementById('edit_is_bulanan').value = data.jenisTagihan.is_bulanan;
            document.getElementById('edit_nominal').value = data.jenisTagihan.nominal;
            document.getElementById('edit_is_nominal_per_kelas').value = data.jenisTagihan.is_nominal_per_kelas;
            
            // Update buku kas dropdown with fresh data from server
            const bukuKasSelect = document.getElementById('edit_buku_kas_id');
            if (bukuKasSelect && data.bukuKasList) {
                console.log('Updating buku kas dropdown with', data.bukuKasList.length, 'options');
                
                // Clear existing options
                bukuKasSelect.innerHTML = '<option value="">Pilih Buku Kas</option>';
                
                // Add new options from server data
                data.bukuKasList.forEach(bukuKas => {
                    const option = document.createElement('option');
                    option.value = bukuKas.id;
                    option.textContent = `${bukuKas.nama_kas} (${bukuKas.kode_kas})`;
                    bukuKasSelect.appendChild(option);
                });
                
                // Set selected value
                const selectedBukuKasId = data.jenisTagihan.buku_kas_id;
                console.log('Setting selected buku_kas_id to:', selectedBukuKasId);
                bukuKasSelect.value = selectedBukuKasId || '';
                
                // Verify selection was successful
                if (selectedBukuKasId && bukuKasSelect.value !== selectedBukuKasId.toString()) {
                    console.warn('Failed to select buku kas ID:', selectedBukuKasId, 'Available options:', Array.from(bukuKasSelect.options).map(o => o.value));
                }
            } else {
                console.warn('BukuKas select element not found or bukuKasList not provided');
                // Fallback to original method if bukuKasList not available
                document.getElementById('edit_buku_kas_id').value = data.jenisTagihan.buku_kas_id || '';
            }
            
            // Update form untuk tanggal dan bulan jatuh tempo dengan menggunakan try-catch untuk menghindari error
            try {
                // Set nilai default jika data belum ada
                const defaultTanggal = 10;
                const defaultBulan = 0;
                
                // Ambil elemen form
                const tanggalJatuhTempoEl = document.getElementById('edit_tanggal_jatuh_tempo');
                const bulanJatuhTempoEl = document.getElementById('edit_bulan_jatuh_tempo');
                
                // Set nilai dengan aman, dengan fallback ke default jika data tidak ada
                if (tanggalJatuhTempoEl) {
                    tanggalJatuhTempoEl.value = data.jenisTagihan.tanggal_jatuh_tempo !== null && 
                                              data.jenisTagihan.tanggal_jatuh_tempo !== undefined ? 
                                              data.jenisTagihan.tanggal_jatuh_tempo : defaultTanggal;
                }
                
                if (bulanJatuhTempoEl) {
                    bulanJatuhTempoEl.value = data.jenisTagihan.bulan_jatuh_tempo !== null && 
                                           data.jenisTagihan.bulan_jatuh_tempo !== undefined ? 
                                           data.jenisTagihan.bulan_jatuh_tempo : defaultBulan;
                }
            } catch (error) {
                console.error("Error setting jatuh tempo values:", error);
                // Use default values in case of error
                if (document.getElementById('edit_tanggal_jatuh_tempo')) {
                    document.getElementById('edit_tanggal_jatuh_tempo').value = 10;
                }
                if (document.getElementById('edit_bulan_jatuh_tempo')) {
                    document.getElementById('edit_bulan_jatuh_tempo').value = 0;
                }
            }
            
            // Setup event listener untuk kategori tagihan dengan penanganan error
            try {
                const kategoriEl = document.getElementById('edit_kategori_tagihan');
                if (kategoriEl) {
                    kategoriEl.addEventListener('change', handleEditJatuhTempoDisplay);
                }
                // Inisialisasi tampilan jatuh tempo
                handleEditJatuhTempoDisplay();
            } catch (error) {
                console.error("Error setting up jatuh tempo event listener:", error);
            }
            
            // Show modal (it was already shown for loading, but ensure it's visible)
            document.getElementById('editModal').classList.remove('hidden');
            clearEditForm();
        } else {
            console.error('Failed response:', data);
            closeEditModal(); // Close modal on error
            alert('Gagal memuat data untuk diedit: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        closeEditModal(); // Close modal on error
        alert('Terjadi kesalahan saat memuat data: ' + error.message);
    });
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    clearEditForm();
}

// Fungsi untuk mengelola tampilan field jatuh tempo berdasarkan kategori tagihan
function handleJatuhTempoDisplay() {
    try {
        const kategoriEl = document.getElementById('kategori_tagihan');
        if (!kategoriEl) return;
        
        const kategoriTagihan = kategoriEl.value;
        const jatuhTempoInfo = document.querySelector('#createModal .jatuh-tempo-settings p');
        
        if (!jatuhTempoInfo) return;
        
        if (kategoriTagihan === 'Rutin') {
            // Untuk tagihan rutin, tampilkan pesan bahwa jatuh tempo selalu tanggal 10
            jatuhTempoInfo.innerHTML = 'Untuk tagihan <strong>rutin</strong>, jatuh tempo selalu tanggal 10 setiap bulan. Input di atas akan diabaikan.';
        } else {
            // Untuk tagihan insidental, tampilkan pesan normal
            jatuhTempoInfo.innerHTML = 'Untuk tagihan <strong>insidentil</strong>, Anda bisa memilih tanggal dan bulan jatuh tempo.';
        }
    } catch (error) {
        console.error("Error updating jatuh tempo display:", error);
    }
}

function handleEditJatuhTempoDisplay() {
    try {
        const kategoriEl = document.getElementById('edit_kategori_tagihan');
        if (!kategoriEl) return;
        
        const kategoriTagihan = kategoriEl.value;
        const jatuhTempoInfo = document.querySelector('#editModal .jatuh-tempo-settings p');
        
        if (!jatuhTempoInfo) return;
        
        if (kategoriTagihan === 'Rutin') {
            // Untuk tagihan rutin, tampilkan pesan bahwa jatuh tempo selalu tanggal 10
            jatuhTempoInfo.innerHTML = 'Untuk tagihan <strong>rutin</strong>, jatuh tempo selalu tanggal 10 setiap bulan. Input di atas akan diabaikan.';
        } else {
            // Untuk tagihan insidental, tampilkan pesan normal
            jatuhTempoInfo.innerHTML = 'Untuk tagihan <strong>insidentil</strong>, Anda bisa memilih tanggal dan bulan jatuh tempo.';
        }
    } catch (error) {
        console.error("Error updating jatuh tempo display:", error);
    }
}

function clearForm() {
    document.getElementById('createForm').reset();
    // Clear all error messages
    document.querySelectorAll('[id^="error-"]').forEach(function(el) {
        el.classList.add('hidden');
        el.textContent = '';
    });
    // Reset button state
    document.getElementById('loading-spinner').classList.add('hidden');
    document.getElementById('submit-text').textContent = 'Simpan';
}

function clearEditForm() {
    // Clear all edit error messages
    document.querySelectorAll('[id^="edit-error-"]').forEach(function(el) {
        el.classList.add('hidden');
        el.textContent = '';
    });
    // Reset button state
    document.getElementById('edit-loading-spinner').classList.add('hidden');
    document.getElementById('edit-submit-text').textContent = 'Simpan Perubahan';
}

function submitForm() {
    const form = document.getElementById('createForm');
    const formData = new FormData(form);
    
    // Show loading state
    document.getElementById('loading-spinner').classList.remove('hidden');
    document.getElementById('submit-text').textContent = 'Menyimpan...';
    
    // Clear previous errors
    document.querySelectorAll('[id^="error-"]').forEach(function(el) {
        el.classList.add('hidden');
        el.textContent = '';
    });

    fetch('{{ route("keuangan.jenis-tagihan.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (response.status === 401) {
            alert('Sesi Anda telah berakhir. Silakan login kembali.');
            window.location.href = '/login';
            return;
        }
        
        if (response.status === 403) {
            alert('Anda tidak memiliki akses untuk melakukan tindakan ini.');
            return;
        }
        
        return response.json();
    })
    .then(data => {
        if (!data) return; // Handle early returns from auth errors
        
        if (data.success) {
            // Show success message and reload page
            closeCreateModal();
            showSuccessMessage(data.message);
            
            // Reload the page to show new data
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            // Show validation errors
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const errorElement = document.getElementById(`error-${field}`);
                    if (errorElement) {
                        errorElement.textContent = data.errors[field][0];
                        errorElement.classList.remove('hidden');
                    }
                });
            }
            
            // Reset button state
            document.getElementById('loading-spinner').classList.add('hidden');
            document.getElementById('submit-text').textContent = 'Simpan';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan data.');
        
        // Reset button state
        document.getElementById('loading-spinner').classList.add('hidden');
        document.getElementById('submit-text').textContent = 'Simpan';
    });
}

function submitEditForm() {
    const form = document.getElementById('editForm');
    const formData = new FormData(form);
    const id = document.getElementById('edit_id').value;
    
    console.log('Submitting edit form for ID:', id);
    
    // Add PUT method override for Laravel
    formData.append('_method', 'PUT');
    
    // Debug form data
    for (let [key, value] of formData.entries()) {
        console.log('Form data:', key, '=', value);
    }
    
    // Show loading state
    document.getElementById('edit-loading-spinner').classList.remove('hidden');
    document.getElementById('edit-submit-text').textContent = 'Menyimpan...';
    
    // Clear previous errors
    document.querySelectorAll('[id^="edit-error-"]').forEach(function(el) {
        el.classList.add('hidden');
        el.textContent = '';
    });

    fetch(`/keuangan/jenis-tagihan/${id}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (response.status === 401) {
            alert('Sesi Anda telah berakhir. Silakan login kembali.');
            window.location.href = '/login';
            return;
        }
        
        if (response.status === 403) {
            alert('Anda tidak memiliki akses untuk melakukan tindakan ini.');
            return;
        }
        
        return response.json();
    })
    .then(data => {
        if (!data) return; // Handle early returns from auth errors
        
        if (data.success) {
            // Show success message and reload page
            closeEditModal();
            showSuccessMessage(data.message);
            
            // Reload the page to show updated data
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            // Show validation errors
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const errorElement = document.getElementById(`edit-error-${field}`);
                    if (errorElement) {
                        errorElement.textContent = data.errors[field][0];
                        errorElement.classList.remove('hidden');
                    }
                });
            }
            
            // Reset button state
            document.getElementById('edit-loading-spinner').classList.add('hidden');
            document.getElementById('edit-submit-text').textContent = 'Simpan Perubahan';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan data.');
        
        // Reset button state
        document.getElementById('edit-loading-spinner').classList.add('hidden');
        document.getElementById('edit-submit-text').textContent = 'Simpan Perubahan';
    });
}

function showSuccessMessage(message) {
    // Create and show success alert
    const successAlert = document.createElement('div');
    successAlert.className = 'bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg';
    successAlert.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="font-medium">${message}</p>
        </div>
    `;
    
    // Insert success message after header
    const header = document.querySelector('.max-w-6xl > div:first-child');
    header.insertAdjacentElement('afterend', successAlert);
    
    // Auto-hide success message after 5 seconds
    setTimeout(() => {
        successAlert.remove();
    }, 5000);
}

// Close modal when clicking outside
document.getElementById('createModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCreateModal();
    }
});

document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});

// Automation Modal Functions
function openAutomationModal() {
    document.getElementById('automationModal').classList.remove('hidden');
}

function closeAutomationModal() {
    document.getElementById('automationModal').classList.add('hidden');
    // Reset form
    document.getElementById('automationForm').reset();
    document.getElementById('previewSection').classList.add('hidden');
}

function previewAutomation() {
    const form = document.getElementById('automationForm');
    const formData = new FormData(form);
    
    fetch('{{ route("keuangan.tunggakan.automation-preview") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('previewSection').classList.remove('hidden');
            document.getElementById('previewContent').innerHTML = data.preview_html;
        } else {
            alert(data.message || 'Terjadi kesalahan saat memuat preview');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memuat preview');
    });
}

function submitAutomation(event) {
    event.preventDefault();
    
    // Show loading
    document.getElementById('automation-loading-spinner').classList.remove('hidden');
    document.getElementById('automation-submit-text').textContent = 'Memproses...';
    
    const form = document.getElementById('automationForm');
    const formData = new FormData(form);
    
    fetch('{{ route("keuangan.tunggakan.automation-execute") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Otomatisasi berhasil! ${data.created_count} tagihan berhasil disalin.`);
            closeAutomationModal();
            // Refresh page to show updated data
            window.location.reload();
        } else {
            alert(data.message || 'Terjadi kesalahan saat menjalankan otomatisasi');
        }
        
        // Reset button state
        document.getElementById('automation-loading-spinner').classList.add('hidden');
        document.getElementById('automation-submit-text').textContent = 'Jalankan Otomatisasi';
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menjalankan otomatisasi');
        
        // Reset button state
        document.getElementById('automation-loading-spinner').classList.add('hidden');
        document.getElementById('automation-submit-text').textContent = 'Jalankan Otomatisasi';
    });
}

// Close automation modal when clicking outside
document.getElementById('automationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAutomationModal();
    }
});
</script>
@endsection
