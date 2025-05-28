@extends('layouts.admin')

@section('content')
<div x-data="{ openImportModal: false }">
    <div class="max-w-6xl mx-auto py-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-blue-800 flex items-center gap-2">
                <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 000 7.75"/>
                </svg>
                Daftar Kelas
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('kelas.create') }}"
                   class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 flex items-center gap-2">
                    <span class="text-xl font-bold">+</span> Tambah Kelas
                </a>
                <!-- Tombol Import -->
                <button
                    @click="openImportModal = true"
                    class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 flex items-center gap-2">
                    📤 Import
                </button>
                <a href="{{ route('kelas.pindah.form') }}"
                   class="bg-yellow-500 text-white px-4 py-2 rounded shadow hover:bg-yellow-600 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7"/></svg>
                    Pindah Kelas
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded shadow">{{ session('success') }}</div>
        @endif

        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full text-sm text-left border">
                <thead class="bg-gray-100 text-xs font-semibold uppercase">
                    <tr>
                        <th class="p-3">No</th>
                        <th class="p-3">Kode Kelas</th>
                        <th class="p-3">Nama Kelas</th>
                        <th class="p-3">Tingkat</th>
                        <th class="p-3">Wali Kelas</th>
                        <th class="p-3">Jumlah Siswa</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kelas as $i => $kls)
                        <tr class="border-b">
                            <td class="p-3">{{ $kelas->firstItem() + $i }}</td>
                            <td class="p-3">{{ $kls->kode ?? '-' }}</td>
                            <td class="p-3">{{ $kls->nama ?? '-' }}</td>
                            <td class="p-3">{{ $kls->tingkat ?? '-' }}</td>
                            <td class="p-3">{{ $kls->wali_kelas ?? '-' }}</td>
                            <td class="p-3">{{ $kls->anggota_count ?? 0 }}</td>
                            <td class="p-3">
                                <a href="{{ route('kelas.edit', $kls->id) }}" class="inline-flex items-center px-2 py-1 rounded bg-yellow-400 text-xs text-white hover:bg-yellow-500 mr-1">
                                    ✏️ Edit
                                </a>
                                <a href="{{ route('kelas.anggota.index', $kls->id) }}" class="inline-flex items-center px-2 py-1 rounded bg-blue-600 text-xs text-white hover:bg-blue-700 mr-1">
                                    👥 Anggota
                                </a>
                                <form action="{{ route('kelas.destroy', $kls->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus kelas ini?')">
                                    @csrf @method('DELETE')
                                    <button class="inline-flex items-center px-2 py-1 rounded bg-red-500 text-xs text-white hover:bg-red-600">
                                        🗑️ Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-4 text-center text-gray-400">Belum ada data kelas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $kelas->links() }}
        </div>
    </div>

    <!-- Modal Import Kelas -->
<div
    x-show="openImportModal"
    style="display: none"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
    <div @click.away="openImportModal = false"
         class="bg-white rounded-xl p-8 max-w-md w-full shadow-xl border">
        <h3 class="text-xl font-bold mb-4 text-blue-800">Import Data Kelas</h3>
        {{-- Tombol Download Template --}}
        <a href="{{ route('kelas.template') }}"
           class="inline-block mb-4 px-3 py-1 bg-blue-100 hover:bg-blue-200 rounded text-blue-800 text-sm font-semibold shadow transition">
            📄 Download Template
        </a>
        <form action="{{ route('kelas.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="file" name="file" required
                   class="block w-full border rounded p-2" accept=".xlsx,.xls,.csv">
            <div class="flex justify-end gap-2 mt-4">
                <button type="button"
                        @click="openImportModal = false"
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

@endsection
