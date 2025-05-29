@extends('layouts.admin')
@section('content')
<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="h3 mb-2 text-gray-800">Jenis Kas</h1>
            <p class="mb-4">Daftar jenis kas yang tersedia.</p>
        </div>
        <button onclick="document.getElementById('modal-tambah-jenis-kas').classList.remove('hidden')" type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition duration-150 flex items-center">
            <span class="material-icons align-middle mr-1">add</span> Tambah Jenis Kas
        </button>
    </div>
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Kas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Jenis Kas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($jenisKas as $kas)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $kas->kode_kas }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $kas->nama_jenis_kas }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $kas->keterangan }}</td>
                    <td class="px-6 py-4 whitespace-nowrap flex space-x-2 items-center">
                        <button onclick="openEditModal({{ $kas->id }}, '{{ addslashes($kas->kode_kas) }}', '{{ addslashes($kas->nama_jenis_kas) }}', '{{ addslashes($kas->keterangan) }}')" class="text-yellow-500 hover:text-yellow-700 px-2 py-1 rounded" title="Edit">
                            <span class="material-icons align-middle">edit</span>
                        </button>
                        <form action="{{ route('jenis-kas.destroy', $kas->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?')" class="inline flex items-center">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 px-2 py-1 rounded" title="Hapus">
                                <span class="material-icons align-middle">delete</span>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada data jenis kas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- Modal Tambah Jenis Kas -->
    <div id="modal-tambah-jenis-kas" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-auto">
            <div class="flex justify-between items-center border-b px-6 py-4">
                <h3 class="text-lg font-semibold">Tambah Jenis Kas Baru</h3>
                <button type="button" onclick="document.getElementById('modal-tambah-jenis-kas').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            <form action="{{ route('jenis-kas.store') }}" method="POST" class="px-6 py-4">
                @csrf
                <div class="mb-4">
                    <label for="kode_kas" class="block text-gray-700">Kode Kas</label>
                    <input type="text" name="kode_kas" id="kode_kas" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300" required>
                </div>
                <div class="mb-4">
                    <label for="nama_jenis_kas" class="block text-gray-700">Nama Jenis Kas</label>
                    <input type="text" name="nama_jenis_kas" id="nama_jenis_kas" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300" required>
                </div>
                <div class="mb-4">
                    <label for="keterangan" class="block text-gray-700">Keterangan</label>
                    <input type="text" name="keterangan" id="keterangan" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300" required>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="document.getElementById('modal-tambah-jenis-kas').classList.add('hidden')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal Edit Jenis Kas -->
    <div id="modal-edit-jenis-kas" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-auto">
            <div class="flex justify-between items-center border-b px-6 py-4">
                <h3 class="text-lg font-semibold">Edit Jenis Kas</h3>
                <button type="button" onclick="document.getElementById('modal-edit-jenis-kas').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            <form id="form-edit-jenis-kas" method="POST" class="px-6 py-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_id">
                <div class="mb-4">
                    <label for="edit_kode_kas" class="block text-gray-700">Kode Kas</label>
                    <input type="text" name="kode_kas" id="edit_kode_kas" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300" required>
                </div>
                <div class="mb-4">
                    <label for="edit_nama_jenis_kas" class="block text-gray-700">Nama Jenis Kas</label>
                    <input type="text" name="nama_jenis_kas" id="edit_nama_jenis_kas" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300" required>
                </div>
                <div class="mb-4">
                    <label for="edit_keterangan" class="block text-gray-700">Keterangan</label>
                    <input type="text" name="keterangan" id="edit_keterangan" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300" required>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="document.getElementById('modal-edit-jenis-kas').classList.add('hidden')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
function openEditModal(id, kode_kas, nama_jenis_kas, keterangan) {
  document.getElementById('edit_id').value = id;
  document.getElementById('edit_kode_kas').value = kode_kas;
  document.getElementById('edit_nama_jenis_kas').value = nama_jenis_kas;
  document.getElementById('edit_keterangan').value = keterangan;
  document.getElementById('form-edit-jenis-kas').action = '/keuangan/setting/jenis-kas/' + id;
  document.getElementById('modal-edit-jenis-kas').classList.remove('hidden');
}
</script>
@endsection
{{-- Script untuk DataTable dan AJAX akan ditambahkan di sini --}}