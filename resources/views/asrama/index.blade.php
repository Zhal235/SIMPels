@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto py-6">
    {{-- Header + tombol tambah --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 pb-4 border-b border-gray-200">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Manajemen Asrama
            </h1>
            <p class="text-sm text-gray-500 mt-1">Kelola data asrama, impor, dan pindah asrama.</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button onclick="openTambahModal()"
               class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out flex items-center justify-center gap-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Tambah Asrama
            </button>
            <button onclick="openImportModal()"
                    class="w-full sm:w-auto bg-white text-blue-600 border border-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 transition duration-150 ease-in-out flex items-center justify-center gap-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                Import Asrama
            </button>
            <button onclick="openPindahModal()"
               class="w-full sm:w-auto bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition duration-150 ease-in-out flex items-center justify-center gap-2 shadow-sm">
                 <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                    <path d="M6.99 11L3 15l3.99 4v-3H14v-2H6.99v-3zM21 9l-3.99-4v3H10v2h7.01v3L21 9z"/>
                </svg>
                Pindah Asrama
            </button>
        </div>
    </div>

    {{-- Modal Tambah Asrama --}}
    <div id="tambahModal" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
            <button onclick="closeTambahModal()" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <h3 class="text-lg font-bold text-blue-800 mb-4">Tambah Asrama</h3>
            <form id="tambahForm" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Asrama</label>
                    <input type="text" name="kode" id="tambah_kode" required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Asrama</label>
                    <input type="text" name="nama" id="tambah_nama" required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Wali Asrama</label>
                    <input type="text" name="wali_asrama" id="tambah_wali_asrama"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="(Boleh kosong, bisa diisi nanti)">
                </div>
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" onclick="closeTambahModal()" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Asrama --}}
    <div id="editModal" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
            <button onclick="closeEditModal()" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <h3 class="text-lg font-bold text-blue-800 mb-4">Edit Asrama</h3>
            <form id="editForm" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_id">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Asrama</label>
                    <input type="text" name="kode" id="edit_kode" required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Asrama</label>
                    <input type="text" name="nama" id="edit_nama" required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Wali Asrama</label>
                    <input type="text" name="wali_asrama" id="edit_wali_asrama"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="(Boleh kosong, bisa diisi nanti)">
                </div>
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Update</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Import --}}
    <div id="importModal" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
            <button onclick="closeImportModal()" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <h3 class="text-lg font-bold text-blue-800 mb-4">Import Data Asrama</h3>
            <div class="mb-4">
                <a href="{{ asset('templates/asrama_template.xlsx') }}" class="inline-flex items-center px-3 py-1 rounded bg-blue-100 text-blue-700 font-semibold text-sm hover:bg-blue-200 transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Download Template
                </a>
            </div>
            <form action="{{ route('asrama.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <input type="file" name="file" accept=".xlsx" required class="border rounded px-3 py-2 w-full" />
                </div>
                <div class="text-xs text-gray-600 mb-2">
                    Kolom: Kode, Nama, Wali Asrama
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeImportModal()" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Import</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Pindah Asrama --}}
    <div id="pindahModal" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl p-6 relative max-h-[90vh] overflow-y-auto">
            <button onclick="closePindahModal()" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <h3 class="text-lg font-bold text-blue-800 mb-4">Pindah Asrama Santri</h3>
            
            <form id="pindahForm" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                    {{-- Asrama Asal --}}
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block font-semibold text-gray-700 mb-2">Asrama Asal</label>
                        <select id="pindah_asrama_asal" class="w-full rounded-md border border-gray-300 px-3 py-2 mb-3 focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="filterSantriAsal()" required>
                            <option value="">-- Pilih Asrama --</option>
                            @foreach($asrama as $row)
                                <option value="{{ $row->kode }}">{{ $row->nama }} ({{ $row->kode }})</option>
                            @endforeach
                        </select>
                        <div class="border rounded-md bg-white max-h-60 overflow-y-auto">
                            <div class="p-2 text-xs text-gray-500 bg-gray-100 border-b">Pilih santri yang akan dipindah (gunakan Ctrl+Click untuk multi-select)</div>
                            <select name="santri_id[]" id="pindah_santri_asal_list" class="w-full h-48 p-2 focus:outline-none" multiple size="10" required>
                                <!-- Akan diisi oleh JavaScript -->
                            </select>
                        </div>
                    </div>

                    {{-- Tombol Pindah --}}
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l7-7-7-7"/>
                        </svg>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg shadow transition-all duration-150">
                            Pindahkan →
                        </button>
                    </div>

                    {{-- Asrama Tujuan --}}
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block font-semibold text-gray-700 mb-2">Asrama Tujuan</label>
                        <select name="asrama_id" id="pindah_asrama_tujuan" class="w-full rounded-md border border-gray-300 px-3 py-2 mb-3 focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="loadSantriTujuan()" required>
                            <option value="">-- Pilih Asrama --</option>
                            @foreach($asrama as $row)
                                <option value="{{ $row->id }}" data-kode="{{ $row->kode }}">{{ $row->nama }} ({{ $row->kode }})</option>
                            @endforeach
                        </select>
                        <div class="border rounded-md bg-white max-h-60 overflow-y-auto">
                            <div class="p-2 text-xs text-gray-500 bg-gray-100 border-b">Anggota asrama tujuan saat ini</div>
                            <select id="pindah_santri_tujuan_list" class="w-full h-48 p-2 text-gray-700" multiple size="10" disabled>
                                <!-- Akan diisi oleh JavaScript -->
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end gap-2 pt-4 border-t">
                    <button type="button" onclick="closePindahModal()" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Pindahkan Santri</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto bg-white rounded-xl shadow-lg border border-gray-200 mt-2">
        <table class="min-w-full text-sm text-left text-gray-700">
            <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-600 sticky top-0 z-10">
                <tr>
                    <th class="px-4 py-3 text-center">No</th>
                    <th class="px-4 py-3 text-center">Kode Asrama</th>
                    <th class="px-4 py-3 text-left">Nama Asrama</th>
                    <th class="px-4 py-3 text-left">Wali Asrama</th>
                    <th class="px-4 py-3 text-center">Jumlah Santri</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($asrama as $no => $row)
                <tr>
                    <td class="px-4 py-3 text-center font-medium">{{ $asrama->firstItem() + $no }}</td>
                    <td class="px-4 py-3 text-center">{{ $row->kode }}</td>
                    <td class="px-4 py-3">{{ $row->nama }}</td>
                    <td class="px-4 py-3">{{ $row->wali_asrama ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">{{ $row->anggota_asrama_count }}</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex justify-center items-center gap-2">
                        <button onclick="openEditModal({{ $row->id }}, '{{ $row->kode }}', '{{ addslashes($row->nama) }}', '{{ addslashes($row->wali_asrama ?? '') }}')" title="Edit Asrama"
                           class="p-2 rounded-full text-blue-600 hover:bg-blue-100 transition duration-150 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                            </svg>
                        </button>
                        <a href="{{ route('asrama.anggota.index', $row->id) }}" title="Lihat Anggota"
                           class="p-2 rounded-full text-green-600 hover:bg-green-100 transition duration-150 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                            </svg>
                        </a>
                        <form action="{{ route('asrama.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus asrama bernama ' + '{{ addslashes($row->nama) }}' + '?')" class="inline-block">
                            @csrf @method('DELETE')
                            <button type="submit" title="Hapus Asrama"
                                    class="p-2 rounded-full text-red-600 hover:bg-red-100 transition duration-150 ease-in-out">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                </svg>
                            </button>
                        </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-6 px-4 pb-4">
            {{ $asrama->links() }}
        </div>
    </div>
</div>

<script>
// Data santri untuk filter pindah asrama
let santrisData = [];

// Fungsi untuk membuka modal tambah
function openTambahModal() {
    document.getElementById('tambahModal').classList.remove('hidden');
    document.getElementById('tambah_kode').focus();
}

function closeTambahModal() {
    document.getElementById('tambahModal').classList.add('hidden');
    document.getElementById('tambahForm').reset();
}

// Fungsi untuk membuka modal edit
function openEditModal(id, kode, nama, waliAsrama) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_kode').value = kode;
    document.getElementById('edit_nama').value = nama;
    document.getElementById('edit_wali_asrama').value = waliAsrama;
    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('edit_kode').focus();
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.getElementById('editForm').reset();
}

// Fungsi untuk membuka modal import
function openImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
}

function closeImportModal() {
    document.getElementById('importModal').classList.add('hidden');
}

// Fungsi untuk membuka modal pindah
function openPindahModal() {
    document.getElementById('pindahModal').classList.remove('hidden');
    loadSantrisData();
}

function closePindahModal() {
    document.getElementById('pindahModal').classList.add('hidden');
    document.getElementById('pindahForm').reset();
}

// Load data santri untuk pindah asrama
function loadSantrisData() {
    fetch('/api/santris-with-asrama', {
        method: 'GET',
        credentials: 'same-origin', // Include cookies for authentication
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        santrisData = data;
        populateSantriAsalList();
    })
    .catch(error => {
        console.error('Error loading santris data:', error);
        alert('Gagal memuat data santri. Error: ' + error.message);
    });
}

// Populate daftar santri asal
function populateSantriAsalList() {
    const list = document.getElementById('pindah_santri_asal_list');
    list.innerHTML = '';
    
    santrisData.forEach(santri => {
        const option = document.createElement('option');
        option.value = santri.id;
        option.textContent = `${santri.nis || '-'} - ${santri.nama_santri}`;
        option.setAttribute('data-asrama', santri.asrama ? santri.asrama.kode : '');
        list.appendChild(option);
    });
}

// Filter santri berdasarkan asrama asal
function filterSantriAsal() {
    const asalKode = document.getElementById('pindah_asrama_asal').value;
    const list = document.getElementById('pindah_santri_asal_list');
    const options = list.querySelectorAll('option');
    
    options.forEach(option => {
        const asramaKode = option.getAttribute('data-asrama');
        if (asalKode === '' || asramaKode === asalKode) {
            option.style.display = '';
        } else {
            option.style.display = 'none';
            option.selected = false;
        }
    });
}

// Load santri di asrama tujuan
function loadSantriTujuan() {
    const tujuanSelect = document.getElementById('pindah_asrama_tujuan');
    const tujuanKode = tujuanSelect.options[tujuanSelect.selectedIndex]?.getAttribute('data-kode');
    const tujuanList = document.getElementById('pindah_santri_tujuan_list');
    
    tujuanList.innerHTML = '';
    
    if (tujuanKode) {
        santrisData.forEach(santri => {
            if (santri.asrama && santri.asrama.kode === tujuanKode) {
                const option = document.createElement('option');
                option.textContent = `${santri.nis || '-'} - ${santri.nama_santri}`;
                tujuanList.appendChild(option);
            }
        });
    }
}

// Handle form submissions dengan AJAX
document.getElementById('tambahForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route("asrama.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Asrama berhasil ditambahkan!');
            location.reload();
        } else {
            alert('Gagal menambahkan asrama: ' + (data.message || 'Error tidak diketahui'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menambahkan asrama');
    });
});

document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const id = document.getElementById('edit_id').value;
    
    fetch(`{{ url('asrama') }}/${id}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Asrama berhasil diperbarui!');
            location.reload();
        } else {
            alert('Gagal memperbarui asrama: ' + (data.message || 'Error tidak diketahui'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memperbarui asrama');
    });
});

document.getElementById('pindahForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route("asrama.pindah") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Santri berhasil dipindahkan!');
            closePindahModal();
            location.reload();
        } else {
            alert('Gagal memindahkan santri: ' + (data.message || 'Error tidak diketahui'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memindahkan santri');
    });
});

// Close modal when clicking outside
window.addEventListener('click', function(e) {
    const modals = ['tambahModal', 'editModal', 'importModal', 'pindahModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
});
</script>
@endsection
