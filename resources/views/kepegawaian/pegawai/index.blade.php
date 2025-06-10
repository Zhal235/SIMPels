@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Data Pegawai</h1>
            <p class="text-gray-600 mt-2">Kelola data pegawai dan informasi kepegawaian</p>
        </div>
        <div class="flex items-center gap-2 mt-4 sm:mt-0">
            <a href="{{ route('kepegawaian.pegawai.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Tambah Pegawai
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

    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
        <div class="flex items-center">
            <span class="material-icons-outlined mr-2">error</span>
            <p>{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Filter Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-3">
            <h3 class="font-medium text-gray-700">Filter & Pencarian</h3>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('kepegawaian.pegawai.index') }}" class="flex flex-col lg:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari Pegawai</label>
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               id="search"
                               value="{{ request('search') }}"
                               placeholder="Nama, NIP, NIK, Jabatan, atau Divisi..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="w-full lg:w-48">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Non-Aktif" {{ request('status') == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                        <option value="Pensiun" {{ request('status') == 'Pensiun' ? 'selected' : '' }}>Pensiun</option>
                        <option value="Resign" {{ request('status') == 'Resign' ? 'selected' : '' }}>Resign</option>
                    </select>
                </div>
                <div class="w-full lg:w-48">
                    <label for="jabatan" class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                    <select name="jabatan" id="jabatan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Jabatan</option>
                        @foreach($jabatanOptions as $jabatan)
                            <option value="{{ $jabatan }}" {{ request('jabatan') == $jabatan ? 'selected' : '' }}>{{ $jabatan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full lg:w-48">
                    <label for="divisi" class="block text-sm font-medium text-gray-700 mb-2">Divisi</label>
                    <select name="divisi" id="divisi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Divisi</option>
                        @foreach($divisiOptions as $divisi)
                            <option value="{{ $divisi }}" {{ request('divisi') == $divisi ? 'selected' : '' }}>{{ $divisi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Cari
                    </button>
                    <a href="{{ route('kepegawaian.pegawai.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-3">
            <h3 class="font-medium text-gray-700">Daftar Pegawai ({{ $pegawais->total() }} orang)</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Pegawai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan & Divisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pegawais as $index => $pegawai)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ ($pegawais->currentPage() - 1) * $pegawais->perPage() + $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex-shrink-0 h-12 w-12">
                                @if($pegawai->foto)
                                    <img class="h-12 w-12 rounded-full object-cover" 
                                         src="{{ asset('storage/' . $pegawai->foto) }}" 
                                         alt="{{ $pegawai->nama_pegawai }}">
                                @else
                                    <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="material-icons-outlined text-gray-500">person</span>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                <div class="font-medium text-gray-900">{{ $pegawai->nama_pegawai }}</div>
                                <div class="text-gray-500">
                                    @if($pegawai->nip)
                                        NIP: {{ $pegawai->nip }}<br>
                                    @endif
                                    NIK: {{ $pegawai->nik }}
                                </div>
                                <div class="text-gray-500">{{ $pegawai->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                <div class="font-medium text-gray-900">{{ $pegawai->jabatan ?: '-' }}</div>
                                <div class="text-gray-500">{{ $pegawai->divisi ?: '-' }}</div>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $pegawai->jenis_pegawai == 'Tetap' ? 'bg-green-100 text-green-800' : 
                                       ($pegawai->jenis_pegawai == 'Kontrak' ? 'bg-yellow-100 text-yellow-800' : 
                                        ($pegawai->jenis_pegawai == 'Honorer' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                    {{ $pegawai->jenis_pegawai }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $pegawai->status_pegawai == 'Aktif' ? 'bg-green-100 text-green-800' : 
                                   ($pegawai->status_pegawai == 'Non-Aktif' ? 'bg-red-100 text-red-800' : 
                                    ($pegawai->status_pegawai == 'Pensiun' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                {{ $pegawai->status_pegawai }}
                            </span>
                            <div class="text-xs text-gray-500 mt-1">
                                Masuk: {{ $pegawai->tanggal_masuk->format('d/m/Y') }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                @if($pegawai->no_hp)
                                    <div class="text-gray-900">{{ $pegawai->no_hp }}</div>
                                @endif
                                @if($pegawai->email)
                                    <div class="text-gray-500">{{ $pegawai->email }}</div>
                                @endif
                                @if(!$pegawai->no_hp && !$pegawai->email)
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('kepegawaian.pegawai.show', $pegawai) }}" 
                                   class="text-blue-600 hover:text-blue-900 p-1 rounded" title="Lihat Detail">
                                    <span class="material-icons-outlined text-lg">visibility</span>
                                </a>
                                <a href="{{ route('kepegawaian.pegawai.edit', $pegawai) }}" 
                                   class="text-green-600 hover:text-green-900 p-1 rounded" title="Edit">
                                    <span class="material-icons-outlined text-lg">edit</span>
                                </a>
                                <button onclick="confirmDelete('{{ $pegawai->id }}', '{{ $pegawai->nama_pegawai }}')" 
                                        class="text-red-600 hover:text-red-900 p-1 rounded" title="Hapus">
                                    <span class="material-icons-outlined text-lg">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <span class="material-icons-outlined text-4xl text-gray-300 mb-2">group</span>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">Tidak Ada Data Pegawai</h3>
                                <p>Belum ada data pegawai yang tersedia atau sesuai dengan filter yang dipilih.</p>
                                <a href="{{ route('kepegawaian.pegawai.create') }}" 
                                   class="mt-3 inline-flex items-center px-4 py-2 border border-blue-300 text-blue-700 rounded-md hover:bg-blue-50 transition-colors">
                                    <span class="material-icons-outlined mr-2">add</span>
                                    Tambah Pegawai Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($pegawais->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <p class="text-sm text-gray-700">
                        Menampilkan {{ $pegawais->firstItem() ?? 0 }} sampai {{ $pegawais->lastItem() ?? 0 }} 
                        dari {{ $pegawais->total() }} hasil
                    </p>
                </div>
                <div>
                    {{ $pegawais->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <span class="material-icons-outlined text-red-600">warning</span>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-2">Hapus Pegawai</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Apakah Anda yakin ingin menghapus pegawai <strong id="pegawaiName"></strong>? 
                    Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex justify-center space-x-3">
                        <button type="button" onclick="closeDeleteModal()" 
                                class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md hover:bg-gray-600 transition-colors">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md hover:bg-red-600 transition-colors">
                            Ya, Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(pegawaiId, pegawaiName) {
    document.getElementById('pegawaiName').textContent = pegawaiName;
    document.getElementById('deleteForm').action = `/kepegawaian/pegawai/${pegawaiId}`;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>
@endsection
