@extends('layouts.admin')
@section('content')
<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="h3 mb-2 text-gray-800">Jenis Bayar</h1>
            <p class="mb-4">Daftar jenis pembayaran yang tersedia.</p>
        </div>
        <button onclick="document.getElementById('modal-tambah-jenis').classList.remove('hidden')" type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition duration-150 flex items-center">
            <span class="material-icons align-middle mr-1">add</span> Tambah Jenis Bayar
        </button>
    </div>
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pembayaran</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Klasifikasi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe Kas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($jenisBayar as $jenis)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $jenis->nama_jenis_bayar }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst(str_replace('_', ' ', $jenis->klasifikasi)) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $jenis->jenisKas->nama_jenis_kas ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap flex space-x-2 items-center">
                        <button onclick="openEditModal({{ $jenis->id }}, '{{ addslashes($jenis->nama_jenis_bayar) }}', '{{ addslashes($jenis->klasifikasi) }}', {{ $jenis->jenis_kas_id }})" class="text-yellow-500 hover:text-yellow-700 px-2 py-1 rounded" title="Edit">
                            <span class="material-icons align-middle">edit</span>
                        </button>
                        <form action="{{ route('jenis-bayar.destroy', $jenis->id) }}" method="POST" class="contents" onsubmit="return confirm('Yakin ingin menghapus?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 px-2 py-1 rounded" title="Hapus">
                                <span class="material-icons align-middle">delete</span>
                            </button>
                        </form>
                        <a href="{{ route('setting-pembayaran.index', ['jenis_bayar_id' => $jenis->id]) }}" class="text-blue-500 hover:text-blue-700 px-2 py-1 rounded" title="Set Pembayaran">
                            <span class="material-icons align-middle">settings</span>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada data jenis pembayaran.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<!-- Modal Tambah Jenis Bayar -->
<div id="modal-tambah-jenis" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
        <button onclick="document.getElementById('modal-tambah-jenis').classList.add('hidden')" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
            <span class="material-icons">close</span>
        </button>
        <h2 class="text-lg font-semibold mb-4">Tambah Jenis Bayar</h2>
        <form action="{{ route('jenis-bayar.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="nama_jenis_bayar" class="block text-gray-700">Nama Jenis Bayar</label>
                <input type="text" name="nama_jenis_bayar" id="nama_jenis_bayar" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300" required>
            </div>
            <div class="mb-4">
                <label for="klasifikasi" class="block text-gray-700">Klasifikasi</label>
                <select name="klasifikasi" id="klasifikasi" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300" required>
                    <option value="">-- Pilih Klasifikasi --</option>
                    <option value="biaya_rutin_bulanan">Biaya Rutin Bulanan</option>
                    <option value="biaya_incidential">Biaya Incidential</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="jenis_kas_id" class="block text-gray-700">Tipe Kas</label>
                <select name="jenis_kas_id" id="jenis_kas_id" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300" required>
                    <option value="">-- Pilih Tipe Kas --</option>
                    @foreach($jenisKas as $kas)
                        <option value="{{ $kas->id }}">{{ $kas->nama_jenis_kas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('modal-tambah-jenis').classList.add('hidden')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded">Batal</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('scripts')
<script>
function openEditModal(id, nama_jenis_bayar, klasifikasi, jenis_kas_id) {
  document.getElementById('edit_id').value = id;
  document.getElementById('edit_nama_jenis_bayar').value = nama_jenis_bayar;
  document.getElementById('edit_klasifikasi').value = klasifikasi;
  document.getElementById('edit_jenis_kas_id').value = jenis_kas_id;
  document.getElementById('form-edit-jenis-bayar').action = '/jenis-bayar/' + id;
  document.getElementById('modal-edit-jenis-bayar').classList.remove('hidden');
}
</script>
@endsection

<!-- Modal Edit Jenis Bayar -->
<div id="modal-edit-jenis-bayar" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-auto">
        <div class="flex justify-between items-center border-b px-6 py-4">
            <h3 class="text-lg font-semibold">Edit Jenis Bayar</h3>
            <button type="button" onclick="document.getElementById('modal-edit-jenis-bayar').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <form id="form-edit-jenis-bayar" method="POST" class="px-6 py-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="edit_id">
            <div class="mb-4">
                <label for="edit_nama_jenis_bayar" class="block text-gray-700">Nama Jenis Bayar</label>
                <input type="text" name="nama_jenis_bayar" id="edit_nama_jenis_bayar" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300" required>
            </div>
            <div class="mb-4">
                <label for="edit_klasifikasi" class="block text-gray-700">Klasifikasi</label>
                <select name="klasifikasi" id="edit_klasifikasi" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300" required>
                    <option value="">-- Pilih Klasifikasi --</option>
                    <option value="biaya_rutin_bulanan">Biaya Rutin Bulanan</option>
                    <option value="biaya_incidential">Biaya Incidential</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="edit_jenis_kas_id" class="block text-gray-700">Tipe Kas</label>
                <select name="jenis_kas_id" id="edit_jenis_kas_id" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300" required>
                    <option value="">-- Pilih Tipe Kas --</option>
                    @foreach($jenisKas as $kas)
                        <option value="{{ $kas->id }}">{{ $kas->nama_jenis_kas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('modal-edit-jenis-bayar').classList.add('hidden')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded">Batal</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">Update</button>
            </div>
        </form>
    </div>
</div>