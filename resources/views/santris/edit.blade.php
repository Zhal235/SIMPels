@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto py-6">

    {{-- ✅ Popup sukses --}}
    @if (session('success'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 5000)"
            class="fixed top-20 right-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded shadow z-50"
        >
            <strong class="font-semibold">Berhasil:</strong>
            <div class="text-sm mt-1">{{ session('success') }}</div>
        </div>
    @endif

    {{-- ❌ Popup error --}}
    @if ($errors->any())
        <div
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 5000)"
            class="fixed top-20 right-6 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded shadow z-50"
        >
            <strong class="font-semibold">Terjadi kesalahan:</strong>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h2 class="text-2xl font-semibold text-gray-700 mb-6">✏️ Edit Data Santri</h2>

    <form method="POST" action="{{ route('santris.update', $santri->id) }}" enctype="multipart/form-data" class="bg-white p-6 rounded shadow-md space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

            {{-- Form kolom 3/4 --}}
            <div class="md:col-span-3 space-y-4">
                {{-- NIS & NISN --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-sm">NIS</label>
                        <input type="text" name="nis" value="{{ old('nis', $santri->nis) }}" required
                               class="form-input w-full border-gray-300 rounded">
                    </div>
                    <div>
                        <label class="block font-medium text-sm">NISN</label>
                        <input type="text" name="nisn" value="{{ old('nisn', $santri->nisn) }}"
                               class="form-input w-full border-gray-300 rounded">
                    </div>
                </div>

                {{-- Loop field lain --}}
                @foreach ([
                    'nik_siswa'        => 'NIK Siswa',
                    'nama_siswa'       => 'Nama Lengkap',
                    'tempat_lahir'     => 'Tempat Lahir',
                    'tanggal_lahir'    => 'Tanggal Lahir',
                    'jenis_kelamin'    => 'Jenis Kelamin (L/P)',
                    'kelas'            => 'Tingkat/Kelas',
                    'asal_sekolah'     => 'Asal Sekolah',
                    'hobi'             => 'Hobi',
                    'cita_cita'        => 'Cita-Cita',
                    'jumlah_saudara'   => 'Jumlah Saudara',
                    'alamat'           => 'Alamat',
                    'provinsi'         => 'Provinsi',
                    'kabupaten'        => 'Kabupaten/Kota',
                    'kecamatan'        => 'Kecamatan',
                    'desa'             => 'Desa/Kelurahan',
                    'kode_pos'         => 'Kode Pos',
                    'no_kk'            => 'No. Kartu Keluarga'
                ] as $field => $label)
                    <div>
                        <label class="block font-medium text-sm">{{ $label }}</label>
                        <input
                          type="{{ $field == 'tanggal_lahir' ? 'date' : 'text' }}"
                          name="{{ $field }}"
                          value="{{ old($field, $santri->$field) }}"
                          class="form-input w-full border-gray-300 rounded"
                        >
                    </div>
                @endforeach

                {{-- Data Ayah --}}
                <h3 class="text-lg font-semibold text-gray-600 pt-4">Data Ayah</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ([
                        'nama_ayah'       => 'Nama Ayah',
                        'nik_ayah'        => 'NIK Ayah',
                        'pendidikan_ayah' => 'Pendidikan Ayah',
                        'hp_ayah'         => 'No. HP Ayah'
                    ] as $field => $label)
                        <div>
                            <label class="block font-medium text-sm">{{ $label }}</label>
                            <input type="text" name="{{ $field }}" value="{{ old($field, $santri->$field) }}"
                                   class="form-input w-full border-gray-300 rounded">
                        </div>
                    @endforeach
                </div>
                <div>
                    <label class="block font-medium text-sm">Pekerjaan Ayah</label>
                    <select name="pekerjaan_ayah" class="form-select w-full border-gray-300 rounded">
                        <option value="">-- Pilih Pekerjaan --</option>
                        @foreach ($pekerjaans as $p)
                            <option value="{{ $p->id }}"
                              {{ old('pekerjaan_ayah', $santri->pekerjaan_ayah) == $p->id ? 'selected' : '' }}>
                              {{ $p->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Data Ibu --}}
                <h3 class="text-lg font-semibold text-gray-600 pt-4">Data Ibu</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ([
                        'nama_ibu'       => 'Nama Ibu',
                        'nik_ibu'        => 'NIK Ibu',
                        'pendidikan_ibu' => 'Pendidikan Ibu',
                        'hp_ibu'         => 'No. HP Ibu'
                    ] as $field => $label)
                        <div>
                            <label class="block font-medium text-sm">{{ $label }}</label>
                            <input type="text" name="{{ $field }}" value="{{ old($field, $santri->$field) }}"
                                   class="form-input w-full border-gray-300 rounded">
                        </div>
                    @endforeach
                </div>
                <div>
                    <label class="block font-medium text-sm">Pekerjaan Ibu</label>
                    <select name="pekerjaan_ibu" class="form-select w-full border-gray-300 rounded">
                        <option value="">-- Pilih Pekerjaan --</option>
                        @foreach ($pekerjaans as $p)
                            <option value="{{ $p->id }}"
                              {{ old('pekerjaan_ibu', $santri->pekerjaan_ibu) == $p->id ? 'selected' : '' }}>
                              {{ $p->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Bantuan Sosial & Akademik --}}
                <h3 class="text-lg font-semibold text-gray-600 pt-4">Bantuan Sosial</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ([
                        'no_bpjs' => 'Nomor BPJS',
                        'no_pkh'  => 'Nomor Kartu PKH',
                        'no_kip'  => 'Nomor KIP'
                    ] as $name => $label)
                        <div>
                            <label class="block font-medium text-sm">{{ $label }}</label>
                            <input type="text" name="{{ $name }}" class="form-input w-full border-gray-300 rounded" value="{{ old($name, $santri->$name) }}">
                        </div>
                    @endforeach
                </div>

                <h3 class="text-lg font-semibold text-gray-600 pt-4">Akademik Sebelumnya</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ([
                        'npsn_sekolah'    => 'NPSN Sekolah',
                        'no_blanko_skhu'  => 'No. Blanko SKHU',
                        'no_seri_ijazah'  => 'No. Seri Ijazah',
                        'total_nilai_un'  => 'Total Nilai UN',
                        'tanggal_kelulusan'=> 'Tanggal Kelulusan'
                    ] as $name => $label)
                        <div>
                            <label class="block font-medium text-sm">{{ $label }}</label>
                            <input type="{{ $name == 'tanggal_kelulusan' ? 'date' : 'text' }}" name="{{ $name }}" class="form-input w-full border-gray-300 rounded" value="{{ old($name, $santri->$name) }}">
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Kolom FOTO --}}
            <div>
                <label for="previewInput" class="block text-sm font-medium text-gray-700 mb-2">Pas Foto</label>

                {{-- Tampilkan foto saat ini jika ada --}}
                @if ($santri->foto)
                    <img src="{{ asset('storage/'.$santri->foto) }}"
                         alt="Foto Lama"
                         class="mb-2 w-full aspect-[3/4] object-cover rounded border">
                @endif

                {{-- Input file baru untuk preview dan upload --}}
                <input type="file"
                       name="foto"
                       id="previewInput"
                       accept="image/*"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">

                {{-- Preview foto baru --}}
                <img id="previewFoto"
                     src="#"
                     alt="Preview Foto"
                     class="hidden mt-2 border rounded w-full aspect-[3/4] object-cover">
            </div>
        </div>

        <button type="submit"
                class="mt-6 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Perbarui
        </button>
    </form>
</div>

<script>
    const input = document.getElementById('previewInput');
    const preview = document.getElementById('previewFoto');

    input.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            preview.src = '#';
            preview.classList.add('hidden');
        }
    });
</script>
@endsection
