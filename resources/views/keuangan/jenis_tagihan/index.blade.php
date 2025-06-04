@extends('layouts.admin')

@section('content')
<div class="container mx-auto p-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 pb-4 border-b border-gray-200">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zM14 6a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h8zM6 10a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2H8a2 2 0 01-2-2v-2z" clip-rule="evenodd" />
                </svg>
                Jenis Tagihan
            </h1>
            <p class="text-sm text-gray-500 mt-1">Kelola jenis tagihan rutin dan insidental.</p>
            @if($activeTahunAjaran)
                <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                    </svg>
                    Tahun Ajaran: {{ $activeTahunAjaran->nama_tahun_ajaran }}
                </div>
            @else
                <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Tidak ada tahun ajaran aktif
                </div>
            @endif
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <div id="modal-create" class="fixed z-50 inset-0 overflow-y-auto hidden">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6 relative">
                        <button onclick="document.getElementById('modal-create').classList.add('hidden')" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <h2 class="text-xl font-bold mb-4">Tambah Jenis Tagihan</h2>
                        <form action="{{ route('jenis-tagihan.store') }}" method="POST" class="space-y-6">
                            @csrf
                            <div>
                                <label for="nama" class="block text-sm font-medium text-gray-700">Nama Jenis Tagihan</label>
                                <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="kategori_tagihan" class="block text-sm font-medium text-gray-700">Kategori Pembayaran</label>
                                <select name="kategori_tagihan" id="kategori_tagihan" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="rutin" {{ old('kategori_tagihan') == 'rutin' ? 'selected' : '' }}>Rutin</option>
                                    <option value="insidentil" {{ old('kategori_tagihan') == 'insidentil' ? 'selected' : '' }}>Insidentil</option>
                                </select>
                            </div>
                            <div>
                                <label for="jenis_kas" class="block text-sm font-medium text-gray-700">Jenis Kas</label>
                                <select name="jenis_kas" id="jenis_kas" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="">-- Pilih Jenis Kas --</option>
                                    <option value="-" selected>-</option>
                                </select>
                                <p class="text-xs text-gray-400 mt-1">(Fitur relasi ke menu Jenis Kas akan tersedia setelah menu tersebut dibuat)</p>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out shadow-sm">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <a href="#" onclick="document.getElementById('modal-create').classList.remove('hidden')" class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out flex items-center justify-center gap-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Tambah Jenis Tagihan
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Sukses!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori Pembayaran</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Kas</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($jenisTagihans as $jenisTagihan)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $jenisTagihan->nama }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $jenisTagihan->kategori_tagihan }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="#" onclick="openEditModal({{ $jenisTagihan->id }}, '{{ $jenisTagihan->nama }}', '{{ $jenisTagihan->kategori_tagihan }}', '{{ $jenisTagihan->jenis_kas }}')" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13h3l8-8a2.828 2.828 0 10-4-4l-8 8v3z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('jenis-tagihan.destroy', $jenisTagihan->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jenis tagihan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </form>
                                    <a href="#" class="text-blue-600 hover:text-blue-900" title="Opsi Tagihan">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <circle cx="12" cy="12" r="2" />
                                            <circle cx="19" cy="12" r="2" />
                                            <circle cx="5" cy="12" r="2" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Tidak ada jenis tagihan yang tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $jenisTagihans->links() }}
        </div>
    </div>

</div>

    <div id="modal-edit" class="fixed z-50 inset-0 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6 relative">
                <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <h2 class="text-xl font-bold mb-4">Edit Jenis Tagihan</h2>
                <form id="editForm" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="edit_nama" class="block text-sm font-medium text-gray-700">Nama Jenis Tagihan</label>
                        <input type="text" name="nama" id="edit_nama" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label for="edit_kategori_tagihan" class="block text-sm font-medium text-gray-700">Kategori Pembayaran</label>
                        <select name="kategori_tagihan" id="edit_kategori_tagihan" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <option value="">-- Pilih Kategori --</option>
                            <option value="rutin">Rutin</option>
                            <option value="insidentil">Insidentil</option>
                        </select>
                    </div>
                    <div>
                        <label for="edit_jenis_kas" class="block text-sm font-medium text-gray-700">Jenis Kas</label>
                        <select name="jenis_kas" id="edit_jenis_kas" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <option value="">-- Pilih Jenis Kas --</option>
                            <option value="-">-</option>
                        </select>
                        <p class="text-xs text-gray-400 mt-1">(Fitur relasi ke menu Jenis Kas akan tersedia setelah menu tersebut dibuat)</p>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out shadow-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    function openEditModal(id, nama, kategori, jenisKas) {
        document.getElementById('modal-edit').classList.remove('hidden');
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_kategori_tagihan').value = kategori;
        document.getElementById('edit_jenis_kas').value = jenisKas;
        document.getElementById('editForm').action = '/keuangan/jenis-tagihan/' + id;
    }
    </script>
@endsection