@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto py-6">
    <div class="flex items-start gap-6">
        {{-- Tabel Anggota Kelas --}}
        <div class="flex-1">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">
                👥 Anggota Kelas: {{ $kelas->nama }} ({{ $kelas->kode }})
            </h2>
            {{-- Notifikasi --}}
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Tabel --}}
            <div class="overflow-x-auto bg-white rounded shadow">
                <table class="min-w-full text-sm text-left border">
                    <thead class="bg-gray-100 text-xs font-semibold uppercase">
                        <tr>
                            <th class="p-3">No</th>
                            <th class="p-3">Nama</th>
                            <th class="p-3">NIS</th>
                            <th class="p-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($anggota as $i => $santri)
                            <tr class="border-b">
                                <td class="p-3">{{ $i+1 }}</td>
                                <td class="p-3">{{ $santri->nama_santri }}</td>
                                <td class="p-3">{{ $santri->nis ?? '-' }}</td>
                                <td class="p-3">
                                    <form action="{{ route('kelas.anggota.destroy', [$kelas, $santri]) }}" method="POST" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button onclick="return confirm('Keluarkan santri ini dari kelas?')" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs">
                                            Keluarkan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-4 text-center text-gray-500">Belum ada anggota kelas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Form Tambah Anggota (Sidebar Kanan) --}}
       <div class="w-80 min-w-[320px]">
    <div class="bg-white rounded-2xl shadow-xl p-5 border border-blue-50">
        <div class="font-semibold mb-4 text-center text-lg text-blue-800 tracking-wide">Tambah Anggota</div>
        
        <!-- SEARCH -->
        <input 
            type="text" 
            id="searchSantri" 
            placeholder="Cari nama/nis..." 
            class="mb-3 w-full border border-blue-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none text-sm bg-blue-50 placeholder:text-blue-400"
            onkeyup="filterSantri()"
        >

        <form action="{{ route('kelas.anggota.store', $kelas) }}" method="POST">
            @csrf
            <div class="overflow-y-auto max-h-72 rounded-xl border border-blue-100 bg-blue-50">
                <table class="min-w-full text-sm">
                    <thead class="sticky top-0 bg-blue-100 text-blue-600 shadow">
                        <tr>
                            <th class="p-2 w-7 text-center"><input type="checkbox" id="selectAllSantri" onclick="toggleAllSantri(this)"></th>
                            <th class="p-2 font-semibold">Nama</th>
                            <th class="p-2 font-semibold">NIS</th>
                        </tr>
                    </thead>
                    <tbody id="santriTable">
                    @forelse($santriNotIn as $santri)
                        <tr class="santri-row border-b border-blue-100 hover:bg-blue-200/40 transition-all">
                            <td class="text-center">
                                <input type="checkbox" name="santri_id[]" value="{{ $santri->id }}" class="santri-checkbox accent-blue-600 rounded">
                            </td>
                            <td class="py-2 px-2 text-gray-800">{{ $santri->nama_santri }}</td>
                            <td class="py-2 px-2 text-gray-500 font-mono">{{ $santri->nis ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-2 text-center text-gray-400">Semua santri sudah punya kelas.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <button 
                type="submit" 
                class="mt-4 flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 transition text-white px-4 py-2 rounded-xl shadow-md w-full text-base font-bold tracking-wide"
            >
                <svg width="18" height="18" class="inline" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
                Tambah ke Kelas
            </button>
        </form>
    </div>
</div>

<script>
function filterSantri() {
    var input = document.getElementById("searchSantri");
    var filter = input.value.toLowerCase();
    var rows = document.querySelectorAll("#santriTable tr.santri-row");
    rows.forEach(function(row) {
        var nama = row.cells[1].textContent.toLowerCase();
        var nis = row.cells[2].textContent.toLowerCase();
        row.style.display = (nama.includes(filter) || nis.includes(filter)) ? "" : "none";
    });
}
function toggleAllSantri(source) {
    var checkboxes = document.querySelectorAll('.santri-checkbox');
    checkboxes.forEach(cb => cb.checked = source.checked);
}
</script>


@endsection