@extends('layouts.admin')

@section('content')
<div>
    <div class="max-w-6xl mx-auto py-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 pb-4 border-b border-gray-200">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Manajemen Kelas
                </h1>
                <p class="text-sm text-gray-500 mt-1">Kelola data kelas dan impor data kelas.</p>
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button onclick="openCreateModal()"
                   class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition duration-150 ease-in-out flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v4h4a1 1 0 110 2h-4v4a1 1 0 11-2 0v-4H5a1 1 0 110-2h4V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Tambah Kelas
                </button>
                <button onclick="openImportModal()"
                         class="w-full sm:w-auto bg-green-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 transition duration-150 ease-in-out flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Import Kelas
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded shadow">{{ session('success') }}</div>
        @endif

        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200 text-gray-700">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode Kelas</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Kelas</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tingkat</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Wali Kelas</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Jumlah Santri</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($kelas as $index => $kls)
                        <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                            <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $kelas->firstItem() + $index }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $kls->kode ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">{{ $kls->nama ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $kls->tingkat ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $kls->wali_kelas ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center">{{ $kls->anggota_count ?? 0 }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                <div class="flex justify-center items-center gap-2">
                                    <button onclick="openEditModal({{ $kls->id }}, {{ json_encode($kls->kode) }}, {{ json_encode($kls->nama) }}, {{ json_encode($kls->tingkat) }}, {{ json_encode($kls->wali_kelas) }})" class="p-2 rounded-full text-blue-600 hover:bg-blue-100 hover:text-blue-700 transition-colors duration-150" title="Edit Kelas">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                            <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <a href="{{ route('kelas.anggota.index', $kls->id) }}" class="p-2 rounded-full text-green-600 hover:bg-green-100 hover:text-green-700 transition-colors duration-150" title="Lihat Anggota Kelas">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('kelas.destroy', $kls->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus kelas bernama {{ $kls->nama }}? Data yang terhapus tidak dapat dikembalikan.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-full text-red-600 hover:bg-red-100 hover:text-red-700 transition-colors duration-150" title="Hapus Kelas">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 whitespace-nowrap">
                                <div class="text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2zM12 9v3m0 3h.01" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Data Kelas Tidak Ditemukan</h3>
                                    <p class="mt-1 text-sm text-gray-500">Belum ada data kelas yang tersedia. Silakan tambahkan kelas baru.</p>
                                    <div class="mt-6">
                                        <a href="{{ route('kelas.create') }}"
                                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v4h4a1 1 0 110 2h-4v4a1 1 0 11-2 0v-4H5a1 1 0 110-2h4V4a1 1 0 011-1z" clip-rule="evenodd" />
                                            </svg>
                                            Tambah Kelas Baru
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 rounded-b-xl shadow-md">
            <div class="flex-1 flex justify-between sm:hidden">
                @if ($kelas->onFirstPage())
                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-white cursor-default">
                        Sebelumnya
                    </span>
                @else
                    <a href="{{ $kelas->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Sebelumnya
                    </a>
                @endif

                @if ($kelas->hasMorePages())
                    <a href="{{ $kelas->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Berikutnya
                    </a>
                @else
                    <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-white cursor-default">
                        Berikutnya
                    </span>
                @endif
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Menampilkan
                        <span class="font-medium">{{ $kelas->firstItem() }}</span>
                        sampai
                        <span class="font-medium">{{ $kelas->lastItem() }}</span>
                        dari
                        <span class="font-medium">{{ $kelas->total() }}</span>
                        hasil
                    </p>
                </div>
                <div>
                    {{ $kelas->links('vendor.pagination.tailwind') }}
                </div>
            </div>
    </div>
</div>

<!-- Modal Import Kelas -->
<div id="importModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Import Data Kelas</h3>
                <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="mt-4">
                <a href="{{ route('kelas.template') }}"
                   class="inline-block mb-4 px-3 py-1 bg-blue-100 hover:bg-blue-200 rounded text-blue-800 text-sm font-semibold shadow transition">
                    📄 Download Template
                </a>
                <form action="{{ route('kelas.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="file" name="file" required
                           class="block w-full border rounded p-2" accept=".xlsx,.xls,.csv">
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" onclick="closeImportModal()"
                                class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-gray-700">
                            Batal
                        </button>
                        <button type="submit"
                                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                            Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Kelas -->
<div id="createModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Tambah Kelas</h3>
                <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="createForm" action="{{ route('kelas.store') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Kelas <span class="text-red-500">*</span></label>
                    <input type="text" name="kode" id="kode" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="Contoh: VII-A, VIII-B" value="{{ old('kode') }}">
                    <span class="text-red-500 text-xs hidden" id="error-kode"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kelas <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" id="nama" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="Contoh: Kelas 7A" value="{{ old('nama') }}">
                    <span class="text-red-500 text-xs hidden" id="error-nama"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tingkat <span class="text-red-500">*</span></label>
                    <select name="tingkat" id="tingkat" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">-- Pilih Tingkat --</option>
                        <option value="7" {{ old('tingkat') == '7' ? 'selected' : '' }}>7</option>
                        <option value="8" {{ old('tingkat') == '8' ? 'selected' : '' }}>8</option>
                        <option value="9" {{ old('tingkat') == '9' ? 'selected' : '' }}>9</option>
                        <option value="10" {{ old('tingkat') == '10' ? 'selected' : '' }}>10</option>
                        <option value="11" {{ old('tingkat') == '11' ? 'selected' : '' }}>11</option>
                        <option value="12" {{ old('tingkat') == '12' ? 'selected' : '' }}>12</option>
                    </select>
                    <span class="text-red-500 text-xs hidden" id="error-tingkat"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Wali Kelas</label>
                    <input type="text" name="wali_kelas" id="wali_kelas" disabled
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-500" 
                           placeholder="Akan diisi ketika menu kepegawaian tersedia" value="{{ old('wali_kelas') }}">
                    <p class="text-xs text-gray-500 mt-1">*Akan diisi ketika menu kepegawaian sudah tersedia</p>
                    <span class="text-red-500 text-xs hidden" id="error-wali_kelas"></span>
                </div>
            </form>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end pt-4 space-x-2">
                <button onclick="closeCreateModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-150 ease-in-out">
                    Batal
                </button>
                <button onclick="submitForm()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out flex items-center gap-2">
                    <svg class="w-4 h-4 hidden animate-spin" id="loading-spinner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span id="submit-text">Simpan</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Kelas -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Edit Kelas</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="editForm" method="POST" class="mt-4 space-y-4">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Kelas <span class="text-red-500">*</span></label>
                    <input type="text" name="kode" id="edit_kode" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="Contoh: VII-A, VIII-B">
                    <span class="text-red-500 text-xs hidden" id="edit-error-kode"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kelas <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" id="edit_nama" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="Contoh: Kelas 7A">
                    <span class="text-red-500 text-xs hidden" id="edit-error-nama"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tingkat <span class="text-red-500">*</span></label>
                    <select name="tingkat" id="edit_tingkat" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">-- Pilih Tingkat --</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                    </select>
                    <span class="text-red-500 text-xs hidden" id="edit-error-tingkat"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Wali Kelas</label>
                    <input type="text" name="wali_kelas" id="edit_wali_kelas" disabled
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-500" 
                           placeholder="Akan diisi ketika menu kepegawaian tersedia">
                    <p class="text-xs text-gray-500 mt-1">*Akan diisi ketika menu kepegawaian sudah tersedia</p>
                    <span class="text-red-500 text-xs hidden" id="edit-error-wali_kelas"></span>
                </div>
            </form>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end pt-4 space-x-2">
                <button onclick="closeEditModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-150 ease-in-out">
                    Batal
                </button>
                <button onclick="submitEditForm()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out flex items-center gap-2">
                    <svg class="w-4 h-4 hidden animate-spin" id="edit-loading-spinner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span id="edit-submit-text">Update</span>
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Debug: Test jika JavaScript dimuat
console.log('JavaScript loaded for kelas index');

let currentEditId = null;

function openCreateModal() {
    console.log('openCreateModal called');
    const modal = document.getElementById('createModal');
    console.log('Create modal element:', modal);
    if (modal) {
        modal.classList.remove('hidden');
        console.log('Modal classes after opening:', modal.className);
    }
    clearForm();
}

function closeCreateModal() {
    console.log('closeCreateModal called');
    document.getElementById('createModal').classList.add('hidden');
    clearForm();
}

function openEditModal(id, kode, nama, tingkat, waliKelas) {
    console.log('openEditModal called with params:', {id, kode, nama, tingkat, waliKelas});
    
    if (!id) {
        console.error('ID is missing!');
        return;
    }
    
    currentEditId = id;
    console.log('currentEditId set to:', currentEditId);
    
    // Populate form fields
    const kodeField = document.getElementById('edit_kode');
    const namaField = document.getElementById('edit_nama');
    const tingkatField = document.getElementById('edit_tingkat');
    const waliField = document.getElementById('edit_wali_kelas');
    
    console.log('Form fields found:', {
        kodeField: !!kodeField,
        namaField: !!namaField,
        tingkatField: !!tingkatField,
        waliField: !!waliField
    });
    
    if (kodeField) kodeField.value = kode || '';
    if (namaField) namaField.value = nama || '';
    if (tingkatField) tingkatField.value = tingkat || '';
    if (waliField) waliField.value = waliKelas || '';
    
    // Clear any previous errors
    clearEditForm();
    
    const modal = document.getElementById('editModal');
    console.log('Edit modal element:', modal);
    
    if (modal) {
        modal.classList.remove('hidden');
        console.log('Edit modal opened, classes:', modal.className);
    }
}

function closeEditModal() {
    console.log('closeEditModal called');
    document.getElementById('editModal').classList.add('hidden');
    clearEditForm();
    currentEditId = null;
}

function openImportModal() {
    console.log('openImportModal called');
    document.getElementById('importModal').classList.remove('hidden');
}

function closeImportModal() {
    console.log('closeImportModal called');
    document.getElementById('importModal').classList.add('hidden');
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
    // Clear all error messages for edit form
    document.querySelectorAll('[id^="edit-error-"]').forEach(function(el) {
        el.classList.add('hidden');
        el.textContent = '';
    });
    // Reset button state
    document.getElementById('edit-loading-spinner').classList.add('hidden');
    document.getElementById('edit-submit-text').textContent = 'Update';
}

function submitForm() {
    console.log('submitForm called');
    const form = document.getElementById('createForm');
    console.log('Form element:', form);
    
    if (!form) {
        console.error('Form not found!');
        return;
    }
    
    const formData = new FormData(form);
    console.log('FormData created');
    
    // Log form data
    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }
    
    // Show loading state
    document.getElementById('loading-spinner').classList.remove('hidden');
    document.getElementById('submit-text').textContent = 'Menyimpan...';
    
    // Clear previous errors
    document.querySelectorAll('[id^="error-"]').forEach(function(el) {
        el.classList.add('hidden');
        el.textContent = '';
    });

    console.log('About to send fetch request to:', '{{ route("kelas.store") }}');

    fetch('{{ route("kelas.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        console.log('Response received:', response);
        console.log('Response status:', response.status);
        
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
        console.log('Response data:', data);
        
        if (!data) return; // Handle early returns from auth errors
        
        if (data.success) {
            console.log('Success! Closing modal and showing message.');
            // Show success message and reload page
            closeCreateModal();
            showSuccessMessage(data.message || 'Kelas berhasil ditambahkan.');
            
            // Reload the page to show new data
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            console.log('Validation errors:', data.errors);
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
        console.error('Fetch error:', error);
        alert('Terjadi kesalahan saat menyimpan data.');
        
        // Reset button state
        document.getElementById('loading-spinner').classList.add('hidden');
        document.getElementById('submit-text').textContent = 'Simpan';
    });
}

function submitEditForm() {
    if (!currentEditId) {
        alert('Error: ID kelas tidak ditemukan.');
        return;
    }
    
    const form = document.getElementById('editForm');
    const formData = new FormData(form);
    
    // Show loading state
    document.getElementById('edit-loading-spinner').classList.remove('hidden');
    document.getElementById('edit-submit-text').textContent = 'Mengupdate...';
    
    // Clear previous errors
    document.querySelectorAll('[id^="edit-error-"]').forEach(function(el) {
        el.classList.add('hidden');
        el.textContent = '';
    });

    fetch(`/kelas/${currentEditId}`, {
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
            showSuccessMessage(data.message || 'Kelas berhasil diupdate.');
            
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
            document.getElementById('edit-submit-text').textContent = 'Update';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengupdate data.');
        
        // Reset button state
        document.getElementById('edit-loading-spinner').classList.add('hidden');
        document.getElementById('edit-submit-text').textContent = 'Update';
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

document.getElementById('importModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImportModal();
    }
});

// Debug: Test jika DOM sudah ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM ready, checking elements...');
    console.log('Create modal:', document.getElementById('createModal'));
    console.log('Edit modal:', document.getElementById('editModal'));
    console.log('Import modal:', document.getElementById('importModal'));
    console.log('Create button:', document.querySelector('[onclick="openCreateModal()"]'));
    console.log('Import button:', document.querySelector('[onclick="openImportModal()"]'));
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const createModal = document.getElementById('createModal');
        const editModal = document.getElementById('editModal');
        const importModal = document.getElementById('importModal');
        
        if (!createModal.classList.contains('hidden')) {
            closeCreateModal();
        }
        if (!editModal.classList.contains('hidden')) {
            closeEditModal();
        }
        if (!importModal.classList.contains('hidden')) {
            closeImportModal();
        }
    }
});

// Auto open modal if there are validation errors
@if($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
        openCreateModal();
    });
@endif
</script>
@endsection
