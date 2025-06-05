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
                                <td colspan="6" class="text-center py-10 text-gray-500">
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

<script>
function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
    clearForm();
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
    clearForm();
}

function openEditModal(id) {
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
        
        if (data.success) {
            // Populate form fields
            document.getElementById('edit_id').value = data.jenisTagihan.id;
            document.getElementById('edit_nama').value = data.jenisTagihan.nama;
            document.getElementById('edit_deskripsi').value = data.jenisTagihan.deskripsi || '';
            document.getElementById('edit_kategori_tagihan').value = data.jenisTagihan.kategori_tagihan;
            document.getElementById('edit_is_bulanan').value = data.jenisTagihan.is_bulanan;
            document.getElementById('edit_nominal').value = data.jenisTagihan.nominal;
            document.getElementById('edit_is_nominal_per_kelas').value = data.jenisTagihan.is_nominal_per_kelas;
            
            // Show modal
            document.getElementById('editModal').classList.remove('hidden');
            clearEditForm();
        } else {
            alert('Gagal memuat data untuk diedit.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memuat data.');
    });
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    clearEditForm();
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
    
    // Add PUT method override for Laravel
    formData.append('_method', 'PUT');
    
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
</script>
@endsection
