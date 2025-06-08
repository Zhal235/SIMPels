@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Manajemen Buku Kas</h1>
            <p class="text-gray-600 mt-2">Kelola buku kas untuk pencatatan transaksi keuangan</p>
        </div>
        <button onclick="openCreateModal()" class="mt-4 sm:mt-0 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
            <span class="material-icons-outlined text-sm mr-2">add</span>
            Tambah Buku Kas
        </button>
    </div>

    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
        @foreach($statistik as $stat)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-2 rounded-full bg-blue-100">
                    <span class="material-icons-outlined text-blue-600 text-lg">account_balance</span>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">{{ $stat->jenis_kas }}</p>
                    <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($stat->total_saldo, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500">{{ $stat->jumlah_kas }} kas</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Filter & Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('keuangan.buku-kas.index') }}" class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari nama kas, kode kas, atau deskripsi..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="w-full lg:w-48">
                <select name="jenis_kas" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Jenis Kas</option>
                    @foreach($jenisKasList as $jenisKas)
                        <option value="{{ $jenisKas->id }}" {{ request('jenis_kas') == $jenisKas->id ? 'selected' : '' }}>{{ $jenisKas->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full lg:w-40">
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    <span class="material-icons-outlined text-sm">search</span>
                </button>
                <a href="{{ route('keuangan.buku-kas.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    <span class="material-icons-outlined text-sm">refresh</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Buku Kas -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo Saat Ini</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($bukuKas as $kas)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <span class="material-icons-outlined text-blue-600">account_balance</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $kas->nama_kas }}</div>
                                    @if($kas->deskripsi)
                                    <div class="text-sm text-gray-500">{{ Str::limit($kas->deskripsi, 50) }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $kas->kode_kas }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($kas->jenisBukuKas)
                                    @if($kas->jenisBukuKas->nama == 'SPP') bg-green-100 text-green-800
                                    @elseif($kas->jenisBukuKas->nama == 'PSB') bg-blue-100 text-blue-800
                                    @elseif($kas->jenisBukuKas->nama == 'Operasional') bg-yellow-100 text-yellow-800
                                    @elseif($kas->jenisBukuKas->nama == 'Pembangunan') bg-purple-100 text-purple-800
                                    @elseif($kas->jenisBukuKas->nama == 'Insidental') bg-orange-100 text-orange-800
                                    @else bg-gray-100 text-gray-800 @endif
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $kas->jenisBukuKas ? $kas->jenisBukuKas->nama : 'Tidak terkategori' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                            {{ $kas->formatted_saldo }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($kas->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Aktif
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Tidak Aktif
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button onclick="showDetail({{ $kas->id }})" class="text-gray-600 hover:text-gray-900" title="Detail">
                                    <span class="material-icons-outlined text-sm">visibility</span>
                                </button>
                                <button onclick="openEditModal({{ $kas->id }})" class="text-blue-600 hover:text-blue-900" title="Edit">
                                    <span class="material-icons-outlined text-sm">edit</span>
                                </button>
                                <button onclick="deleteBukuKas({{ $kas->id }})" class="text-red-600 hover:text-red-900" title="Hapus">
                                    <span class="material-icons-outlined text-sm">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            <div class="flex flex-col items-center">
                                <span class="material-icons-outlined text-gray-400 text-5xl mb-2">account_balance</span>
                                <span>Belum ada data buku kas</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($bukuKas->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $bukuKas->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Create -->
<div id="createModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Tambah Buku Kas</h3>
                <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            
            <form id="createForm" onsubmit="submitCreate(event)">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">                    <div class="col-span-2">
                        <label for="create_nama_kas" class="block text-sm font-medium text-gray-700 mb-2">Nama Kas *</label>
                        <input type="text" id="create_nama_kas" name="nama_kas" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Contoh: Kas SPP">
                        <div id="create_nama_kas_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                    
                    <div>
                        <label for="create_kode_kas" class="block text-sm font-medium text-gray-700 mb-2">Kode Kas *</label>
                        <input type="text" id="create_kode_kas" name="kode_kas" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Contoh: SPP">
                        <div id="create_kode_kas_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                    
                    <div>
                        <label for="create_jenis_kas" class="block text-sm font-medium text-gray-700 mb-2">Jenis Kas *</label>
                        <select id="create_jenis_kas" name="jenis_kas_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih Jenis Kas</option>
                            @foreach($jenisKasList as $jenisKas)
                                <option value="{{ $jenisKas->id }}">{{ $jenisKas->nama }} ({{ $jenisKas->kode }})</option>
                            @endforeach
                        </select>
                        <div id="create_jenis_kas_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                    
                    <div>
                        <label for="create_saldo_awal" class="block text-sm font-medium text-gray-700 mb-2">Saldo Awal *</label>
                        <input type="number" id="create_saldo_awal" name="saldo_awal" min="0" step="0.01" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="0">
                        <div id="create_saldo_awal_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                    
                    <div>
                        <label class="flex items-center mt-6">
                            <input type="checkbox" id="create_is_active" name="is_active" checked
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Aktif</span>
                        </label>
                        <div id="create_is_active_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                    
                    <div class="col-span-2">
                        <label for="create_deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea id="create_deskripsi" name="deskripsi" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Deskripsi kas (opsional)"></textarea>
                        <div id="create_deskripsi_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeCreateModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-200">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Edit Buku Kas</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            
            <form id="editForm" onsubmit="submitEdit(event)">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_kas_id" name="kas_id">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label for="edit_nama_kas" class="block text-sm font-medium text-gray-700 mb-2">Nama Kas *</label>
                        <input type="text" id="edit_nama_kas" name="nama_kas" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div id="edit_nama_kas_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                    
                    <div>
                        <label for="edit_kode_kas" class="block text-sm font-medium text-gray-700 mb-2">Kode Kas *</label>
                        <input type="text" id="edit_kode_kas" name="kode_kas" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div id="edit_kode_kas_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                    
                    <div>
                        <label for="edit_jenis_kas" class="block text-sm font-medium text-gray-700 mb-2">Jenis Kas *</label>
                        <select id="edit_jenis_kas" name="jenis_kas_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih Jenis Kas</option>
                            @foreach($jenisKasList as $jenisKas)
                                <option value="{{ $jenisKas->id }}">{{ $jenisKas->nama }} ({{ $jenisKas->kode }})</option>
                            @endforeach
                        </select>
                        <div id="edit_jenis_kas_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                    
                    <div>
                        <label for="edit_saldo_awal" class="block text-sm font-medium text-gray-700 mb-2">Saldo Awal *</label>
                        <input type="number" id="edit_saldo_awal" name="saldo_awal" min="0" step="0.01" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div id="edit_saldo_awal_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                    
                    <div>
                        <label class="flex items-center mt-6">
                            <input type="checkbox" id="edit_is_active" name="is_active"
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Aktif</span>
                        </label>
                        <div id="edit_is_active_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                    
                    <div class="col-span-2">
                        <label for="edit_deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea id="edit_deskripsi" name="deskripsi" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        <div id="edit_deskripsi_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeEditModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-200">
                        Update
                    </button>
                </div>
            </form>        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Modal Create Functions
function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
    document.getElementById('createForm').reset();
    clearErrors('create');
    
    // Add real-time validation
    setupRealTimeValidation('create');
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
}

function submitCreate(event) {
    event.preventDefault();
    
    const form = document.getElementById('createForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Basic validation
    const namaKas = document.getElementById('create_nama_kas').value.trim();
    const kodeKas = document.getElementById('create_kode_kas').value.trim();
    const jenisKasId = document.getElementById('create_jenis_kas').value;
    const saldoAwal = document.getElementById('create_saldo_awal').value;
    
    if (!namaKas || !kodeKas || !jenisKasId || !saldoAwal) {
        Swal.fire({
            icon: 'error',
            title: 'Form tidak lengkap',
            text: 'Mohon lengkapi semua field yang wajib diisi'
        });
        return;
    }
    
    // Clear previous errors
    clearErrors('create');
    
    // Set loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="flex items-center"><span class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>Menyimpan...</span>';
    
    // Prepare form data
    const formData = new FormData(form);
    
    // Handle is_active checkbox properly
    const isActiveCheckbox = document.getElementById('create_is_active');
    if (isActiveCheckbox) {
        formData.delete('is_active');
        formData.append('is_active', isActiveCheckbox.checked ? '1' : '0');
    }
    
    // Submit the form
    fetch('{{ route("keuangan.buku-kas.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        // Handle session timeout
        if (response.status === 419) {
            throw { status: 419, message: 'CSRF token mismatch' };
        }
        
        return response.json().then(data => {
            return { status: response.status, data: data };
        }).catch(jsonError => {
            return { status: response.status, data: { message: 'Invalid response format' } };
        });
    })
    .then(({status, data}) => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Simpan';
        
        // Handle success
        if (status >= 200 && status < 300 && data.success) {
            closeCreateModal();
            
            // Show success notification
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: data.message || 'Data buku kas berhasil disimpan',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
            return;
        }
        
        // Handle validation errors
        if (status === 422 && data.errors) {
            // Show validation errors on form
            for (const field in data.errors) {
                const errorMsg = data.errors[field][0];
                const errorElement = document.getElementById(`create_${field === 'jenis_kas_id' ? 'jenis_kas' : field}_error`);
                
                if (errorElement) {
                    errorElement.textContent = errorMsg;
                    errorElement.classList.remove('hidden');
                    
                    // Highlight field
                    const inputField = document.getElementById(`create_${field === 'jenis_kas_id' ? 'jenis_kas' : field}`);
                    if (inputField) {
                        inputField.classList.add('border-red-500');
                    }
                }
            }
            
            // Show error notification
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: 'Mohon periksa kembali data yang diinput'
            });
            return;
        }
        
        // Handle other errors
        throw { status, data };
    })
    .catch(error => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Simpan';
        
        // Handle session timeout
        if (error.status === 419) {
            Swal.fire({
                icon: 'warning',
                title: 'Session Berakhir',
                text: 'Halaman akan dimuat ulang untuk memperbarui session',
                confirmButtonText: 'Muat Ulang'
            }).then(() => {
                location.reload();
            });
        } else {
            // Show generic error
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: 'Gagal menyimpan data. Silakan coba lagi.'
            });
        }
    });
}

// Modal Edit Functions
function openEditModal(kasId) {
    // Dapatkan referensi ke modal dan form
    const modal = document.getElementById('editModal');
    const form = document.getElementById('editForm');
    
    // Tampilkan modal
    modal.classList.remove('hidden');
    
    // Reset form dan clear errors
    form.reset();
    clearErrors('edit');
    
    // Tampilkan loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="flex items-center"><span class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>Memuat...</span>';
    
    // Set ID yang akan diedit pada form
    document.getElementById('edit_kas_id').value = kasId;    // Lakukan request ke API dengan fetch
    fetch(`/keuangan/buku-kas/${kasId}?nocache=${Date.now()}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Gagal memuat data');
        }
        return response.json();
    })
    .then(response => {
        if (response.success && response.data) {
            // Isi form dengan data yang diterima
            const data = response.data;
            document.getElementById('edit_nama_kas').value = data.nama_kas;
            document.getElementById('edit_kode_kas').value = data.kode_kas;
            document.getElementById('edit_jenis_kas').value = data.jenis_kas_id;
            document.getElementById('edit_saldo_awal').value = data.saldo_awal;
            document.getElementById('edit_deskripsi').value = data.deskripsi || '';
            document.getElementById('edit_is_active').checked = Boolean(data.is_active);
            
            // Aktifkan tombol submit
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Update';
        } else {
            throw new Error('Data tidak ditemukan');
        }
    })
    .catch(error => {
        // Tampilkan pesan error
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Gagal memuat data: ' + error.message
        });
        
        // Tutup modal
        closeEditModal();
    });
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

function submitEdit(event) {
    event.preventDefault();
    
    const kasId = document.getElementById('edit_kas_id').value;
    const submitBtn = document.querySelector('#editForm button[type="submit"]');
    
    // Basic validation
    const namaKas = document.getElementById('edit_nama_kas').value.trim();
    const kodeKas = document.getElementById('edit_kode_kas').value.trim();
    const jenisKasId = document.getElementById('edit_jenis_kas').value;
    const saldoAwal = document.getElementById('edit_saldo_awal').value;
    
    if (!namaKas || !kodeKas || !jenisKasId || !saldoAwal) {
        Swal.fire({
            icon: 'error',
            title: 'Form tidak lengkap',
            text: 'Mohon lengkapi semua field yang wajib diisi'
        });
        return;
    }
    
    // Update button state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="flex items-center"><span class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>Menyimpan...</span>';
    
    // Prepare form data
    const formData = new FormData(document.getElementById('editForm'));
    formData.append('_method', 'PUT');
    formData.append('is_active', document.getElementById('edit_is_active').checked ? '1' : '0');
    
    // Submit the form via fetch
    fetch(`/keuangan/buku-kas/${kasId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw { status: response.status, data: data };
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Success notification
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data buku kas berhasil diperbarui',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                // Reload page to show updated data
                window.location.reload();
            });
            
            // Close modal
            closeEditModal();
        } else {
            throw new Error('Gagal memperbarui data');
        }
    })
    .catch(error => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Update';
        
        // Handle validation errors
        if (error.status === 422 && error.data && error.data.errors) {
            // Get all error messages
            let errorMessages = [];
            
            for (const field in error.data.errors) {
                const errorMsg = error.data.errors[field][0];
                errorMessages.push(errorMsg);
                
                // Display error under the field
                const errorElement = document.getElementById(`edit_${field === 'jenis_kas_id' ? 'jenis_kas' : field}_error`);
                if (errorElement) {
                    errorElement.textContent = errorMsg;
                    errorElement.classList.remove('hidden');
                    
                    // Highlight field
                    const inputField = document.getElementById(`edit_${field === 'jenis_kas_id' ? 'jenis_kas' : field}`);
                    if (inputField) {
                        inputField.classList.add('border-red-500');
                    }
                }
            }
            
            // Show validation error message
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: errorMessages.join(', ')
            });
        } 
        // Handle session timeout
        else if (error.status === 419 || (error.data && error.data.message && error.data.message.includes('CSRF'))) {
            Swal.fire({
                icon: 'warning',
                title: 'Session Habis',
                text: 'Sesi anda telah berakhir. Halaman akan dimuat ulang.',
                confirmButtonText: 'Muat Ulang'
            }).then(() => {
                window.location.reload();
            });
        } 
        // Handle other errors
        else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.data?.message || error.message || 'Terjadi kesalahan saat memperbarui data'
            });
        }
    });
}

// Delete Function - Simplified
function deleteBukuKas(kasId) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: "Apakah Anda yakin ingin menghapus data buku kas ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Menghapus...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Send delete request
            fetch(`/keuangan/buku-kas/${kasId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data buku kas berhasil dihapus',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Gagal menghapus data');
                }
            }).catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menghapus',
                    text: 'Terjadi kesalahan saat menghapus data buku kas.'
                });
            });
        }
    });
}

// Detail Function
function showDetail(kasId) {
    window.location.href = `{{ route('keuangan.buku-kas.index') }}/${kasId}`;
}

// Close modals when clicking outside
window.onclick = function(event) {
    const createModal = document.getElementById('createModal');
    const editModal = document.getElementById('editModal');
    
    if (event.target === createModal) {
        closeCreateModal();
    }
    if (event.target === editModal) {
        closeEditModal();
    }
}

// Helper functions for error handling
function displayErrors(errors, prefix) {
    console.log('Displaying errors for prefix:', prefix, errors);
    
    // Check if errors object is valid
    if (!errors || typeof errors !== 'object') {
        console.error('Invalid errors object:', errors);
        return;
    }
    
    // Check for boolean conversion errors
    if (errors.is_active && Array.isArray(errors.is_active)) {
        console.log('is_active error detected:', errors.is_active);
        // Handle checkbox specially
        const isActiveCheckbox = document.getElementById(`${prefix}_is_active`);
        if (isActiveCheckbox) {
            const errorElement = document.getElementById(`${prefix}_is_active_error`);
            if (!errorElement) {
                // Create error element if it doesn't exist
                const container = isActiveCheckbox.closest('div');
                if (container) {
                    const newErrorElement = document.createElement('div');
                    newErrorElement.id = `${prefix}_is_active_error`;
                    newErrorElement.className = 'text-red-500 text-sm mt-1';
                    newErrorElement.textContent = errors.is_active[0];
                    container.appendChild(newErrorElement);
                }
            }
        }
    }
    
    Object.keys(errors).forEach(field => {
        // Handle field name mappings (e.g. jenis_kas_id might be displayed as jenis_kas)
        let displayField = field;
        if (field === 'jenis_kas_id') displayField = 'jenis_kas';
        
        const errorElement = document.getElementById(`${prefix}_${displayField}_error`);
        const inputElement = document.getElementById(`${prefix}_${displayField}`);
        
        if (errorElement && inputElement) {
            const errorMessage = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
            errorElement.textContent = errorMessage;
            errorElement.classList.remove('hidden');
            inputElement.classList.add('border-red-500');
            inputElement.classList.remove('border-gray-300');
            console.log(`Error set for ${field}:`, errorMessage);
        } else {
            console.warn(`Error element not found for field ${prefix}_${displayField}_error or input ${prefix}_${displayField}`);
            // Log all available error elements for debugging
            const allErrorElements = document.querySelectorAll(`[id^="${prefix}_"][id$="_error"]`);
            console.log('Available error elements:', Array.from(allErrorElements).map(el => el.id));
        }
    });
}

function clearErrors(prefix) {
    const errorElements = document.querySelectorAll(`[id^="${prefix}_"][id$="_error"]`);
    errorElements.forEach(errorElement => {
        errorElement.classList.add('hidden');
        errorElement.textContent = '';
        
        const fieldName = errorElement.id.replace(`${prefix}_`, '').replace('_error', '');
        const inputElement = document.getElementById(`${prefix}_${fieldName}`);
        if (inputElement) {
            inputElement.classList.remove('border-red-500');
            inputElement.classList.add('border-gray-300');
        }
    });
}

// Client-side validation functions
function validateCreateForm() {
    clearErrors('create');
    let isValid = true;
    const errors = {};
    
    // Validate nama_kas
    const namaKas = document.getElementById('create_nama_kas').value.trim();
    if (!namaKas) {
        errors.nama_kas = ['Nama kas wajib diisi.'];
        isValid = false;
    } else if (namaKas.length > 255) {
        errors.nama_kas = ['Nama kas maksimal 255 karakter.'];
        isValid = false;
    }
    
    // Validate kode_kas
    const kodeKas = document.getElementById('create_kode_kas').value.trim();
    if (!kodeKas) {
        errors.kode_kas = ['Kode kas wajib diisi.'];
        isValid = false;
    } else if (kodeKas.length > 50) {
        errors.kode_kas = ['Kode kas maksimal 50 karakter.'];
        isValid = false;
    }
    
    // Validate jenis_kas_id
    const jenisKasId = document.getElementById('create_jenis_kas').value;
    if (!jenisKasId) {
        errors.jenis_kas_id = ['Jenis kas wajib dipilih.'];
        isValid = false;
    }
    
    // Validate saldo_awal
    const saldoAwal = document.getElementById('create_saldo_awal').value;
    if (!saldoAwal && saldoAwal !== '0') {
        errors.saldo_awal = ['Saldo awal wajib diisi.'];
        isValid = false;
    } else if (isNaN(saldoAwal) || parseFloat(saldoAwal) < 0) {
        errors.saldo_awal = ['Saldo awal harus berupa angka dan tidak boleh negatif.'];
        isValid = false;
    }
    
    console.log('Client validation result:', { isValid, errors });
    
    return { isValid, errors };
}

function validateEditForm() {
    clearErrors('edit');
    let isValid = true;
    const errors = {};
    
    // Validate nama_kas
    const namaKas = document.getElementById('edit_nama_kas').value.trim();
    if (!namaKas) {
        errors.nama_kas = ['Nama kas wajib diisi.'];
        isValid = false;
    } else if (namaKas.length > 255) {
        errors.nama_kas = ['Nama kas maksimal 255 karakter.'];
        isValid = false;
    }
    
    // Validate kode_kas
    const kodeKas = document.getElementById('edit_kode_kas').value.trim();
    if (!kodeKas) {
        errors.kode_kas = ['Kode kas wajib diisi.'];
        isValid = false;
    } else if (kodeKas.length > 50) {
        errors.kode_kas = ['Kode kas maksimal 50 karakter.'];
        isValid = false;
    }
    
    // Validate jenis_kas_id
    const jenisKasId = document.getElementById('edit_jenis_kas').value;
    if (!jenisKasId) {
        errors.jenis_kas_id = ['Jenis kas wajib dipilih.'];
        isValid = false;
    }
    
    // Validate saldo_awal
    const saldoAwal = document.getElementById('edit_saldo_awal').value;
    if (!saldoAwal && saldoAwal !== '0') {
        errors.saldo_awal = ['Saldo awal wajib diisi.'];
        isValid = false;
    } else if (isNaN(saldoAwal) || parseFloat(saldoAwal) < 0) {
        errors.saldo_awal = ['Saldo awal harus berupa angka dan tidak boleh negatif.'];
        isValid = false;
    }
    
    console.log('Edit validation result:', { isValid, errors });
    
    return { isValid, errors };
}

// Setup real-time validation
function setupRealTimeValidation(prefix) {
    // Validate nama_kas
    const namaKasInput = document.getElementById(`${prefix}_nama_kas`);
    if (namaKasInput) {
        namaKasInput.addEventListener('blur', function() {
            validateField(prefix, 'nama_kas', this.value.trim());
        });
    }
    
    // Validate kode_kas
    const kodeKasInput = document.getElementById(`${prefix}_kode_kas`);
    if (kodeKasInput) {
        kodeKasInput.addEventListener('blur', function() {
            validateField(prefix, 'kode_kas', this.value.trim());
        });
    }
    
    // Validate jenis_kas_id
    const jenisKasInput = document.getElementById(`${prefix}_jenis_kas`);
    if (jenisKasInput) {
        jenisKasInput.addEventListener('change', function() {
            validateField(prefix, 'jenis_kas_id', this.value);
        });
    }
    
    // Validate saldo_awal
    const saldoAwalInput = document.getElementById(`${prefix}_saldo_awal`);
    if (saldoAwalInput) {
        saldoAwalInput.addEventListener('blur', function() {
            validateField(prefix, 'saldo_awal', this.value);
        });
    }
}

function validateField(prefix, fieldName, value) {
    const errorElement = document.getElementById(`${prefix}_${fieldName}_error`);
    const inputElement = document.getElementById(`${prefix}_${fieldName}`);
    
    if (!errorElement || !inputElement) return;
    
    let errorMessage = '';
    
    switch (fieldName) {
        case 'nama_kas':
            if (!value) {
                errorMessage = 'Nama kas wajib diisi.';
            } else if (value.length > 255) {
                errorMessage = 'Nama kas maksimal 255 karakter.';
            }
            break;
            
        case 'kode_kas':
            if (!value) {
                errorMessage = 'Kode kas wajib diisi.';
            } else if (value.length > 50) {
                errorMessage = 'Kode kas maksimal 50 karakter.';
            }
            break;
            
        case 'jenis_kas_id':
            if (!value) {
                errorMessage = 'Jenis kas wajib dipilih.';
            }
            break;
            
        case 'saldo_awal':
            if (!value && value !== '0') {
                errorMessage = 'Saldo awal wajib diisi.';
            } else if (isNaN(value) || parseFloat(value) < 0) {
                errorMessage = 'Saldo awal harus berupa angka dan tidak boleh negatif.';
            }
            break;
    }
    
    if (errorMessage) {
        errorElement.textContent = errorMessage;
        errorElement.classList.remove('hidden');
        inputElement.classList.add('border-red-500');
        inputElement.classList.remove('border-gray-300');
    } else {
        errorElement.classList.add('hidden');
        errorElement.textContent = '';
        inputElement.classList.remove('border-red-500');
        inputElement.classList.add('border-gray-300');
    }
}

// Expose functions to global scope
window.openCreateModal = openCreateModal;
window.closeCreateModal = closeCreateModal;
window.openEditModal = openEditModal;
window.closeEditModal = closeEditModal;
window.deleteBukuKas = deleteBukuKas;
window.showDetail = showDetail;
</script>
@endpush
