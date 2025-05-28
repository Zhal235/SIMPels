@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto py-6">

    <h2 class="text-2xl font-bold mb-6">📋 Riwayat Mutasi Santri</h2>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    <table class="min-w-full bg-white border rounded shadow">
        <thead class="bg-gray-100 font-semibold text-left">
            <tr>
                <th class="p-3 border">Nama Santri</th>
                <th class="p-3 border">Alasan Mutasi</th>
                <th class="p-3 border">Tujuan Mutasi</th>
                <th class="p-3 border">Tanggal Mutasi</th>
                <th class="p-3 border text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($mutasiList as $mutasi)
            <tr class="hover:bg-gray-50 border-t">
                <td class="p-3 border">{{ $mutasi->nama }}</td>
                <td class="p-3 border">{{ $mutasi->alasan }}</td>
                <td class="p-3 border">{{ $mutasi->tujuan_mutasi }}</td>
                <td class="p-3 border">{{ \Carbon\Carbon::parse($mutasi->tanggal_mutasi)->format('d-m-Y') }}</td>
                <td class="p-3 border text-center">
                    <form action="{{ route('mutasi_santri.batal', $mutasi->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan mutasi?')">
                        @csrf
                        <button type="submit" class="text-red-600 hover:underline font-semibold">
                            Batal
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-3 text-center text-gray-500">Belum ada data mutasi</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $mutasiList->links() }}
    </div>

</div>
@endsection
