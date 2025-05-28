@extends('layouts.admin')

@section('content')
<div class="flex gap-6 max-w-7xl mx-auto py-6">

    {{-- Table anggota asrama --}}
    <div class="flex-1">
        <h2 class="text-2xl font-bold mb-4 text-blue-900">
            <i class="fa fa-users"></i>
            Anggota Asrama: {{ $asrama->nama }}
        </h2>
        <div class="bg-white rounded shadow">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-gray-100 font-semibold">
                    <tr>
                        <th class="p-2">NO</th>
                        <th class="p-2">NAMA</th>
                        <th class="p-2">NIS</th>
                        <th class="p-2">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($asrama->santris as $no => $santri)
                    <tr>
                        <td class="p-2">{{ $no+1 }}</td>
                        <td class="p-2">{{ $santri->nama_siswa }}</td>
                        <td class="p-2">{{ $santri->nis }}</td>
                        <td class="p-2">
                            <form action="{{ route('asrama.anggota.destroy', [$asrama->id, $santri->id]) }}" method="POST" onsubmit="return confirm('Keluarkan santri ini dari asrama?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded">Keluarkan</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center p-3">Belum ada anggota di asrama ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Panel Tambah Anggota Asrama --}}
<div class="w-80 bg-white rounded-xl shadow-md p-6 h-fit">
    <h3 class="text-lg font-semibold mb-3 text-center">Tambah Anggota</h3>
    <form action="{{ route('asrama.anggota.store', $asrama->id) }}" method="POST">
        @csrf
        <input type="text" class="border rounded px-3 py-1 w-full mb-3 text-sm" placeholder="Cari nama/nis..." oninput="filterSantriAsrama(this.value)">
        <div style="max-height: 220px; overflow-y: auto;" id="daftar-santri-available-asrama">
            <table class="w-full text-sm bg-blue-50 rounded">
                <thead class="sticky top-0 bg-blue-100">
                    <tr>
                        <th class="p-2"><input type="checkbox" onclick="toggleAllAsrama(this)"></th>
                        <th class="p-2">Nama</th>
                        <th class="p-2">NIS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($santriNotIn as $santri)
                    <tr>
                        <td class="p-2 text-center">
                            <input type="checkbox" name="santri_id[]" value="{{ $santri->id }}">
                        </td>
                        <td class="p-2">{{ $santri->nama_siswa }}</td>
                        <td class="p-2">{{ $santri->nis }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded w-full mt-4 text-base flex justify-center items-center gap-2">
            <span class="text-xl">+</span> Tambah ke Asrama
        </button>
    </form>
</div>

<script>
function filterSantriAsrama(keyword) {
    keyword = keyword.toLowerCase();
    document.querySelectorAll("#daftar-santri-available-asrama tbody tr").forEach(tr => {
        let nama = tr.children[1].textContent.toLowerCase();
        let nis = tr.children[2].textContent.toLowerCase();
        tr.style.display = (nama.includes(keyword) || nis.includes(keyword)) ? "" : "none";
    });
}

function toggleAllAsrama(source) {
    document.querySelectorAll("#daftar-santri-available-asrama input[type='checkbox'][name='santri_id[]']").forEach(cb => {
        cb.checked = source.checked;
    });
}
</script>

@endsection
