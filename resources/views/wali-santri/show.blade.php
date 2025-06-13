@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Detail Wali Santri</h2>
                    <div class="flex space-x-2">
                        <a href="{{ route('wali-santri.edit', $waliSantri->id) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">
                            Edit
                        </a>
                        <a href="{{ route('wali-santri.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                            Kembali
                        </a>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-md mb-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Informasi Wali Santri</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Nama</p>
                            <p class="font-medium">{{ $waliSantri->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="font-medium">{{ $waliSantri->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Nomor Telepon</p>
                            <p class="font-medium">{{ $waliSantri->phone ?? 'Tidak ada' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tanggal Registrasi</p>
                            <p class="font-medium">{{ $waliSantri->created_at->format('d-m-Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Daftar Santri</h3>
                    @if($santris->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            No
                                        </th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            NIS
                                        </th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Nama Santri
                                        </th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Kelas
                                        </th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Asrama
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($santris as $index => $santri)
                                        <tr>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                {{ $index + 1 }}
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                {{ $santri->nis }}
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                {{ $santri->nama_santri }}
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                @php
                                                    $kelasAnggota = $santri->kelasAnggotaAktif->first();
                                                    $kelas = $kelasAnggota ? $kelasAnggota->kelas->nama : '-';
                                                @endphp
                                                {{ $kelas }}
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                @php
                                                    $asramaAnggota = $santri->asramaAnggotaAktif->first();
                                                    $asrama = $asramaAnggota ? $asramaAnggota->asrama->nama : '-';
                                                @endphp
                                                {{ $asrama }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">Tidak ada santri yang terhubung dengan wali santri ini</p>
                    @endif
                </div>

                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Log Aktivitas</h3>
                    <p class="text-gray-500">Fitur log aktivitas akan segera tersedia</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
