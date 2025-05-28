@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto py-6">

    <h2 class="text-2xl font-semibold text-gray-700 mb-6">👁️ Preview Data Santri</h2>

    <div class="flex flex-col md:flex-row md:space-x-6">
        {{-- Kiri: Detail --}}
        <div class="md:w-3/4 grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- NIS & NISN --}}
            <div>
                <label class="block font-medium text-sm text-gray-600">NIS</label>
                <p class="mt-1 text-gray-800">{{ $santri->nis }}</p>
            </div>
            <div>
                <label class="block font-medium text-sm text-gray-600">NISN</label>
                <p class="mt-1 text-gray-800">{{ $santri->nisn ?? '-' }}</p>
            </div>

            {{-- Loop field lain secara manual --}}
            @foreach ([
                'nik_siswa'      => 'NIK Siswa',
                'nama_siswa'     => 'Nama Lengkap',
                'tempat_lahir'   => 'Tempat Lahir',
                'tanggal_lahir'  => 'Tanggal Lahir',
                'jenis_kelamin'  => 'Jenis Kelamin',
                'kelas'          => 'Tingkat/Kelas',
                'asal_sekolah'   => 'Asal Sekolah',
                'hobi'           => 'Hobi',
                'cita_cita'      => 'Cita-Cita',
                'jumlah_saudara' => 'Jumlah Saudara',
                'alamat'         => 'Alamat',
                'provinsi'       => 'Provinsi',
                'kabupaten'      => 'Kabupaten/Kota',
                'kecamatan'      => 'Kecamatan',
                'desa'           => 'Desa/Kelurahan',
                'kode_pos'       => 'Kode Pos',
                'no_kk'          => 'No. KK'
            ] as $field => $label)
                <div>
                    <label class="block font-medium text-sm text-gray-600">{{ $label }}</label>
                    <p class="mt-1 text-gray-800">{{ $santri->$field ?? '-' }}</p>
                </div>
            @endforeach

            {{-- Data Ayah --}}
            <h3 class="col-span-full text-lg font-semibold text-gray-700 pt-4">Data Ayah</h3>
            @foreach (['nama_ayah' => 'Nama Ayah','nik_ayah' => 'NIK Ayah','pendidikan_ayah' => 'Pendidikan','hp_ayah'=>'No. HP'] as $f=>$l)
                <div>
                    <label class="block font-medium text-sm text-gray-600">{{ $l }}</label>
                    <p class="mt-1 text-gray-800">{{ $santri->$f ?? '-' }}</p>
                </div>
            @endforeach
            <div>
                <label class="block font-medium text-sm text-gray-600">Pekerjaan Ayah</label>
                <p class="mt-1 text-gray-800">{{ $santri->pekerjaanAyah->nama ?? '-' }}</p>
            </div>

            {{-- Data Ibu --}}
            <h3 class="col-span-full text-lg font-semibold text-gray-700 pt-4">Data Ibu</h3>
            @foreach (['nama_ibu' => 'Nama Ibu','nik_ibu' => 'NIK Ibu','pendidikan_ibu' => 'Pendidikan','hp_ibu'=>'No. HP'] as $f=>$l)
                <div>
                    <label class="block font-medium text-sm text-gray-600">{{ $l }}</label>
                    <p class="mt-1 text-gray-800">{{ $santri->$f ?? '-' }}</p>
                </div>
            @endforeach
            <div>
                <label class="block font-medium text-sm text-gray-600">Pekerjaan Ibu</label>
                <p class="mt-1 text-gray-800">{{ $santri->pekerjaanIbu->nama ?? '-' }}</p>
            </div>

            {{-- Bantuan Sosial --}}
            <h3 class="col-span-full text-lg font-semibold text-gray-700 pt-4">Bantuan Sosial</h3>
            @foreach (['no_bpjs'=>'BPJS','no_pkh'=>'PKH','no_kip'=>'KIP'] as $f=>$l)
                <div>
                    <label class="block font-medium text-sm text-gray-600">No. {{ $l }}</label>
                    <p class="mt-1 text-gray-800">{{ $santri->$f ?? '-' }}</p>
                </div>
            @endforeach

            {{-- Akademik --}}
            <h3 class="col-span-full text-lg font-semibold text-gray-700 pt-4">Akademik Sebelumnya</h3>
            @foreach ([
                'npsn_sekolah'=>'NPSN Sekolah',
                'no_blanko_skhu'=>'Blanko SKHU',
                'no_seri_ijazah'=>'Seri Ijazah',
                'total_nilai_un'=>'Total Nilai UN',
                'tanggal_kelulusan'=>'Tanggal Kelulusan'
            ] as $f=>$l)
                <div>
                    <label class="block font-medium text-sm text-gray-600">{{ $l }}</label>
                    <p class="mt-1 text-gray-800">{{ $santri->$f ?? '-' }}</p>
                </div>
            @endforeach
        </div>

        {{-- Kanan: Foto --}}
        <div class="md:w-1/4 mt-6 md:mt-0">
            <label class="block font-medium text-sm text-gray-600 mb-2">Pas Foto</label>
            @if($santri->foto)
                <img src="{{ asset('storage/' . $santri->foto) }}"
                     alt="Foto {{ $santri->nama_siswa }}"
                     class="border rounded w-full aspect-[3/4] object-cover">
            @else
                <div class="border rounded w-full aspect-[3/4] flex items-center justify-center bg-gray-100 text-gray-500">
                    Tidak ada foto
                </div>
            @endif
        </div>
    </div>

    {{-- Tombol Kembali & Edit --}}
    <div class="mt-6 flex space-x-2">
        <a href="{{ route('santris.index') }}"
           class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
            Kembali
        </a>
        <a href="{{ route('santris.edit', $santri->id) }}"
           class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Edit
        </a>
    </div>

</div>
@endsection
