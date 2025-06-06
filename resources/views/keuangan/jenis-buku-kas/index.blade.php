@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Manajemen Jenis Buku Kas</h1>
            <p class="text-gray-600 mt-2">Kelola kategori jenis buku kas untuk pencatatan transaksi keuangan</p>
        </div>
        <button onclick="openCreateModal()" class="mt-4 sm:mt-0 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
            <span class="material-icons-outlined text-sm mr-2">add</span>
            Tambah Jenis Kas
        </button>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
        <div class="flex items-center">
            <span class="material-icons-outlined mr-2">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
        <div class="flex items-center">
            <span class="material-icons-outlined mr-2">error</span>
            <p>{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Filter & Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('keuangan.jenis-buku-kas.index') }}" class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <span class="material-icons-outlined text-gray-400 text-lg">search</span>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Cari nama jenis kas atau kode..." 
                        class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div class="w-full lg:w-48">
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    Filter
                </button>
                <a href="{{ route('keuangan.jenis-buku-kas.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-3">
            <h3 class="font-medium text-gray-700">Daftar Jenis Buku Kas</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-12">No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Kode</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Deskripsi</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Dipakai</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($jenisKas as $index => $jenis)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">{{ $jenis->kode }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $jenis->nama }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $jenis->deskripsi ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $jenis->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $jenis->is_active ? 'Aktif' : 'Non-aktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">{{ $jenis->used_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                <div class="flex items-center justify-center space-x-2">
                                    <button
                                        class="text-blue-600 hover:text-blue-900 focus:outline-none edit-jenis-kas"
                                        data-id="{{ $jenis->id }}" 
                                        data-nama="{{ $jenis->nama }}"
                                        data-kode="{{ $jenis->kode }}"
                                        data-deskripsi="{{ $jenis->deskripsi }}"
                                        data-is-active="{{ $jenis->is_active ? '1' : '0' }}"
                                        data-used-count="{{ $jenis->used_count }}"
                                    >
                                        <span class="material-icons-outlined">edit</span>
                                    </button>
                                    
                                    <form action="{{ route('keuangan.jenis-buku-kas.destroy', $jenis->id) }}" method="POST" class="inline-block delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            class="text-red-600 hover:text-red-900 focus:outline-none {{ $jenis->used_count > 0 ? 'opacity-50 cursor-not-allowed' : '' }}" 
                                            {{ $jenis->used_count > 0 ? 'disabled' : '' }}
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus jenis kas ini?')"
                                        >
                                            <span class="material-icons-outlined">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="material-icons-outlined text-4xl text-gray-300 mb-2">category</span>
                                    <h3 class="text-lg font-medium text-gray-700 mb-1">Tidak ada data</h3>
                                    <p class="text-sm text-gray-500">Belum ada jenis buku kas yang ditambahkan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Jenis Kas -->
<div class="fixed inset-0 z-50 overflow-y-auto hidden" id="createModal" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" id="createModalOverlay"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="createJenisKasForm" action="{{ route('keuangan.jenis-buku-kas.store') }}" method="POST" class="w-full">
                @csrf
                
                <div class="bg-gray-50 border-b px-6 py-3 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Tambah Jenis Buku Kas</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" id="closeCreateModal">
                        <span class="material-icons-outlined text-2xl">close</span>
                    </button>
                </div>
                
                <div class="p-6 space-y-4">
                    <div>
                        <label for="create_nama" class="block text-sm font-medium text-gray-700 mb-2">Nama Jenis Kas <span class="text-red-500">*</span></label>
                        <input type="text" id="create_nama" name="nama" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                        <div class="text-red-500 text-sm mt-1 hidden" id="create_nama_error"></div>
                    </div>
                    
                    <div>
                        <label for="create_kode" class="block text-sm font-medium text-gray-700 mb-2">Kode <span class="text-red-500">*</span></label>
                        <input type="text" id="create_kode" name="kode" maxlength="10" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                        <div class="text-red-500 text-sm mt-1 hidden" id="create_kode_error"></div>
                    </div>
                    
                    <div>
                        <label for="create_deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea id="create_deskripsi" name="deskripsi" rows="3"
                                 class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm"></textarea>
                        <div class="text-red-500 text-sm mt-1 hidden" id="create_deskripsi_error"></div>
                    </div>
                    
                    <div class="flex items-center mt-2">
                        <input type="checkbox" id="create_is_active" name="is_active" checked
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="create_is_active" class="ml-2 block text-sm text-gray-700">Aktif</label>
                    </div>
                </div>
                
                <div class="bg-gray-50 border-t px-6 py-3 flex justify-end space-x-2">
                    <button type="button" id="cancelCreateBtn" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Jenis Kas -->
<div class="fixed inset-0 z-50 overflow-y-auto hidden" id="editModal" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" id="editModalOverlay"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="editJenisKasForm" method="POST" class="w-full">
                @csrf
                @method('PUT')
                
                <div class="bg-gray-50 border-b px-6 py-3 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Edit Jenis Buku Kas</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" id="closeEditModal">
                        <span class="material-icons-outlined text-2xl">close</span>
                    </button>
                </div>
                
                <div class="p-6 space-y-4">
                    <div>
                        <label for="edit_nama" class="block text-sm font-medium text-gray-700 mb-2">Nama Jenis Kas <span class="text-red-500">*</span></label>
                        <input type="text" id="edit_nama" name="nama" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                        <div class="text-red-500 text-sm mt-1 hidden" id="edit_nama_error"></div>
                    </div>
                    
                    <div>
                        <label for="edit_kode" class="block text-sm font-medium text-gray-700 mb-2">Kode <span class="text-red-500">*</span></label>
                        <input type="text" id="edit_kode" name="kode" maxlength="10" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                        <div class="text-red-500 text-sm mt-1 hidden" id="edit_kode_error"></div>
                    </div>
                    
                    <div>
                        <label for="edit_deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea id="edit_deskripsi" name="deskripsi" rows="3"
                                 class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm"></textarea>
                        <div class="text-red-500 text-sm mt-1 hidden" id="edit_deskripsi_error"></div>
                    </div>
                    
                    <div class="flex items-center mt-2">
                        <input type="checkbox" id="edit_is_active" name="is_active"
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="edit_is_active" class="ml-2 block text-sm text-gray-700">Aktif</label>
                    </div>
                    
                    <div id="used_count_warning" class="hidden bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 p-4 my-2 rounded">
                        <div class="flex">
                            <span class="material-icons-outlined mr-2">warning</span>
                            <p>Jenis kas ini sedang digunakan oleh <span id="used_count_value" class="font-semibold">0</span> buku kas.</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 border-t px-6 py-3 flex justify-end space-x-2">
                    <button type="button" id="cancelEditBtn" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    
    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        document.getElementById('createJenisKasForm').reset();
    }
    
    function openEditModal(id, nama, kode, deskripsi, isActive, usedCount) {
        document.getElementById('editModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        
        // Set action URL
        document.getElementById('editJenisKasForm').action = `{{ url('keuangan/jenis-buku-kas') }}/${id}`;
        
        // Set values
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_kode').value = kode;
        document.getElementById('edit_deskripsi').value = deskripsi || '';
        document.getElementById('edit_is_active').checked = isActive === '1';
        
        // Show warning if used
        const usedCountWarning = document.getElementById('used_count_warning');
        const usedCountValue = document.getElementById('used_count_value');
        
        if (parseInt(usedCount) > 0) {
            usedCountWarning.classList.remove('hidden');
            usedCountValue.textContent = usedCount;
        } else {
            usedCountWarning.classList.add('hidden');
        }
    }
    
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Setup modal actions
        document.getElementById('closeCreateModal').addEventListener('click', closeCreateModal);
        document.getElementById('cancelCreateBtn').addEventListener('click', closeCreateModal);
        document.getElementById('createModalOverlay').addEventListener('click', closeCreateModal);
        
        document.getElementById('closeEditModal').addEventListener('click', closeEditModal);
        document.getElementById('cancelEditBtn').addEventListener('click', closeEditModal);
        document.getElementById('editModalOverlay').addEventListener('click', closeEditModal);
        
        // Event listener untuk tombol edit
        document.querySelectorAll('.edit-jenis-kas').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const nama = this.dataset.nama;
                const kode = this.dataset.kode;
                const deskripsi = this.dataset.deskripsi;
                const isActive = this.dataset.isActive;
                const usedCount = this.dataset.usedCount;
                
                openEditModal(id, nama, kode, deskripsi, isActive, usedCount);
            });
        });
        
        // Form validation
        const createForm = document.getElementById('createJenisKasForm');
        const editForm = document.getElementById('editJenisKasForm');
        
        if (createForm) {
            createForm.addEventListener('submit', function(e) {
                let valid = true;
                
                if (!document.getElementById('create_nama').value.trim()) {
                    document.getElementById('create_nama_error').textContent = 'Nama jenis kas harus diisi';
                    document.getElementById('create_nama_error').classList.remove('hidden');
                    valid = false;
                } else {
                    document.getElementById('create_nama_error').classList.add('hidden');
                }
                
                if (!document.getElementById('create_kode').value.trim()) {
                    document.getElementById('create_kode_error').textContent = 'Kode jenis kas harus diisi';
                    document.getElementById('create_kode_error').classList.remove('hidden');
                    valid = false;
                } else {
                    document.getElementById('create_kode_error').classList.add('hidden');
                }
                
                if (!valid) {
                    e.preventDefault();
                }
            });
        }
        
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                let valid = true;
                
                if (!document.getElementById('edit_nama').value.trim()) {
                    document.getElementById('edit_nama_error').textContent = 'Nama jenis kas harus diisi';
                    document.getElementById('edit_nama_error').classList.remove('hidden');
                    valid = false;
                } else {
                    document.getElementById('edit_nama_error').classList.add('hidden');
                }
                
                if (!document.getElementById('edit_kode').value.trim()) {
                    document.getElementById('edit_kode_error').textContent = 'Kode jenis kas harus diisi';
                    document.getElementById('edit_kode_error').classList.remove('hidden');
                    valid = false;
                } else {
                    document.getElementById('edit_kode_error').classList.add('hidden');
                }
                
                if (!valid) {
                    e.preventDefault();
                }
            });
        }
    });
</script>
@endsection
