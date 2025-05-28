@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto py-6">

    {{-- Header + tombol tambah --}}
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-bold text-blue-800 flex items-center gap-2">
            {{-- ikon sesuai selera --}}
            <svg class="w-7 h-7 text-blue-500" ...>...</svg>
            Daftar Asrama
        </h2>
        <div class="flex gap-2">
            <a href="{{ route('asrama.create') }}"
               class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 flex items-center gap-2">
                <span class="text-xl font-bold">+</span> Tambah Asrama
            </a>
            <a href="{{ route('asrama.pindah.form') }}"
               class="bg-yellow-500 text-white px-4 py-2 rounded shadow hover:bg-yellow-600 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7"/></svg>
                Pindah Asrama
            </a>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full text-sm text-left border">
            <thead class="bg-gray-100 text-xs font-semibold uppercase">
                <tr>
                    <th class="p-3">NO</th>
                    <th class="p-3">KODE ASRAMA</th>
                    <th class="p-3">NAMA ASRAMA</th>
                    <th class="p-3">WALI ASRAMA</th>
                    <th class="p-3">JUMLAH SANTRI</th>
                    <th class="p-3">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($asrama as $no => $row)
                <tr>
                    <td class="p-3">{{ $no+1 }}</td>
                    <td class="p-3">{{ $row->kode }}</td>
                    <td class="p-3">{{ $row->nama }}</td>
                    <td class="p-3">{{ $row->wali_asrama ?? '-' }}</td>
                    <td class="p-3">{{ $row->santris_count }}</td>
                    <td class="p-3 flex gap-1">
                        <a href="{{ route('asrama.edit', $row->id) }}"
                           class="inline-flex items-center px-2 py-1 rounded bg-yellow-400 text-xs text-white hover:bg-yellow-500 gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036A2.5 2.5 0 1121 8.5l-9.5 9.5-5 1 1-5 9.5-9.5z"/></svg>
                            Edit
                        </a>
                        <a href="{{ route('asrama.anggota.index', $row->id) }}"
                           class="inline-flex items-center px-2 py-1 rounded bg-blue-600 text-xs text-white hover:bg-blue-800 gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 000 7.75"/></svg>
                            Anggota
                        </a>
                        <form action="{{ route('asrama.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Hapus asrama ini?')" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center px-2 py-1 rounded bg-red-500 text-xs text-white hover:bg-red-700 gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-2">
            {{ $asrama->links() }}
        </div>
    </div>
</div>
@endsection
