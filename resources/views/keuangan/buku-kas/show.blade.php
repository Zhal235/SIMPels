@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <div class="flex items-center mb-2">
                <a href="{{ route('keuangan.buku-kas.index') }}" class="text-gray-500 hover:text-gray-700 mr-2">
                    <span class="material-icons-outlined">arrow_back</span>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Detail Buku Kas</h1>
            </div>
            <p class="text-gray-600">Informasi lengkap {{ $bukuKas->nama_kas }}</p>
        </div>
        <div class="flex space-x-2 mt-4 sm:mt-0">
            <button onclick="openEditModal({{ $bukuKas->id }})" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                <span class="material-icons-outlined text-sm mr-2">edit</span>
                Edit
            </button>
            <button onclick="deleteBukuKas({{ $bukuKas->id }})" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                <span class="material-icons-outlined text-sm mr-2">delete</span>
                Hapus
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Info Buku Kas -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Informasi Buku Kas</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Nama Kas</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $bukuKas->nama_kas }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Kode Kas</label>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            {{ $bukuKas->kode_kas }}
                        </span>
                    </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Jenis Kas</label>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                            @if($bukuKas->jenisBukuKas && $bukuKas->jenisBukuKas->nama == 'SPP') bg-green-100 text-green-800
                            @elseif($bukuKas->jenisBukuKas && $bukuKas->jenisBukuKas->nama == 'PSB') bg-blue-100 text-blue-800
                            @elseif($bukuKas->jenisBukuKas && $bukuKas->jenisBukuKas->nama == 'Operasional') bg-yellow-100 text-yellow-800
                            @elseif($bukuKas->jenisBukuKas && $bukuKas->jenisBukuKas->nama == 'Pembangunan') bg-purple-100 text-purple-800
                            @elseif($bukuKas->jenisBukuKas && $bukuKas->jenisBukuKas->nama == 'Insidental') bg-orange-100 text-orange-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $bukuKas->jenisBukuKas ? $bukuKas->jenisBukuKas->nama : 'Tidak ada' }}
                        </span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                        @if($bukuKas->is_active)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Aktif
                        </span>
                        @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                            Tidak Aktif
                        </span>
                        @endif
                    </div>
                </div>
                
                @if($bukuKas->deskripsi)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Deskripsi</label>
                    <p class="text-gray-900">{{ $bukuKas->deskripsi }}</p>
                </div>
                @endif
                
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Dibuat</label>
                            <p class="text-gray-900">{{ $bukuKas->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Terakhir Diupdate</label>
                            <p class="text-gray-900">{{ $bukuKas->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jenis Tagihan Terkait -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Jenis Tagihan Terkait</h2>
                
                @if($bukuKas->jenisTagihan->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Tagihan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($bukuKas->jenisTagihan as $tagihan)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $tagihan->nama }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($tagihan->kategori_tagihan == 'Rutin') bg-green-100 text-green-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ $tagihan->kategori_tagihan }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-8">
                    <span class="material-icons-outlined text-gray-400 text-5xl mb-2">category</span>
                    <p class="text-gray-500">Belum ada jenis tagihan yang terkait dengan buku kas ini</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar Saldo -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Saldo</h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                        <span class="text-sm font-medium text-blue-700">Saldo Awal</span>
                        <span class="text-lg font-bold text-blue-900">{{ $bukuKas->formatted_saldo }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                        <span class="text-sm font-medium text-green-700">Saldo Saat Ini</span>
                        <span class="text-lg font-bold text-green-900">Rp {{ number_format($bukuKas->saldo_awal, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistik</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Jenis Tagihan</span>
                        <span class="font-semibold">{{ $bukuKas->jenisTagihan->count() }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Transaksi Terakhir</span>
                        <span class="font-semibold">{{ $bukuKas->transaksiKas->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit (sama seperti di index) -->
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
                    </div>
                    
                    <div>
                        <label for="edit_kode_kas" class="block text-sm font-medium text-gray-700 mb-2">Kode Kas *</label>
                        <input type="text" id="edit_kode_kas" name="kode_kas" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="edit_jenis_kas" class="block text-sm font-medium text-gray-700 mb-2">Jenis Kas *</label>                        <select id="edit_jenis_kas" name="jenis_kas_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih Jenis Kas</option>
                            <!-- Options will be loaded dynamically -->
                        </select>
                    </div>
                    
                    <div>
                        <label for="edit_saldo_awal" class="block text-sm font-medium text-gray-700 mb-2">Saldo Awal *</label>
                        <input type="number" id="edit_saldo_awal" name="saldo_awal" min="0" step="0.01" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="flex items-center mt-6">
                            <input type="checkbox" id="edit_is_active" name="is_active"
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Aktif</span>
                        </label>
                    </div>
                    
                    <div class="col-span-2">
                        <label for="edit_deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea id="edit_deskripsi" name="deskripsi" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
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
            </form>
        </div>
    </div>
</div>

<script>
// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Modal Edit Functions
function openEditModal(kasId) {
    // Load jenis kas options first
    loadJenisKasOptions();
    
    fetch(`/api/buku-kas/${kasId}`, {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const kas = data.data;
            document.getElementById('edit_kas_id').value = kas.id;
            document.getElementById('edit_nama_kas').value = kas.nama_kas;
            document.getElementById('edit_kode_kas').value = kas.kode_kas;
            document.getElementById('edit_jenis_kas').value = kas.jenis_kas_id;
            document.getElementById('edit_saldo_awal').value = kas.saldo_awal;
            document.getElementById('edit_is_active').checked = kas.is_active;
            document.getElementById('edit_deskripsi').value = kas.deskripsi || '';
            
            document.getElementById('editModal').classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Gagal memuat data buku kas.'
        });
    });
}

function loadJenisKasOptions() {
    fetch('/api/jenis-buku-kas/dropdown')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('edit_jenis_kas');
                select.innerHTML = '<option value="">Pilih Jenis Kas</option>';
                
                data.data.forEach(jenis => {
                    select.innerHTML += `<option value="${jenis.id}">${jenis.nama}</option>`;
                });
            }
        })
        .catch(error => {
            console.error('Error loading jenis kas:', error);
        });
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

function submitEdit(event) {
    event.preventDefault();
    
    const kasId = document.getElementById('edit_kas_id').value;
    const formData = new FormData(document.getElementById('editForm'));
    
    fetch(`/keuangan/buku-kas/${kasId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
            closeEditModal();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message || 'Terjadi kesalahan saat memperbarui data.'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Terjadi kesalahan saat memperbarui data.'
        });
    });
}

// Delete Function
function deleteBukuKas(kasId) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data buku kas akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
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
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '{{ route("keuangan.buku-kas.index") }}';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan saat menghapus data.'
                });
            });
        }
    });
}

// Close modal when clicking outside
window.onclick = function(event) {
    const editModal = document.getElementById('editModal');
    
    if (event.target === editModal) {
        closeEditModal();
    }
}
</script>
@endsection
