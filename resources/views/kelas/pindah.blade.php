@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto py-10">
    <h2 class="text-2xl font-bold mb-6 text-blue-800 flex items-center gap-2">
        <svg class="w-7 h-7 inline-block text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-8 4a4 4 0 004 4h0a4 4 0 004-4m-8 4v6a4 4 0 004 4h0a4 4 0 004-4v-6"></path>
        </svg>
        Pindah Kelas Santri
    </h2>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded shadow">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded shadow">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('kelas.pindah') }}" method="POST" id="pindahKelasForm">
        @csrf
        <div class="grid grid-cols-3 gap-6 items-center">

            {{-- Kelas Asal --}}
            <div class="bg-white rounded-2xl shadow-lg p-5 flex flex-col">
                <label class="block font-bold text-gray-700 mb-2">Kelas Asal</label>
                <select id="kelas_asal" class="w-full rounded-lg border p-2 mb-4 bg-blue-50" onchange="filterSantriAsal()" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->kode }}">{{ $kelas->nama }} ({{ $kelas->kode }})</option>
                    @endforeach
                </select>
                <div class="flex-1 overflow-y-auto rounded-lg border bg-gray-50">
                    <select name="santri_id[]" id="santri_asal_list"
                            class="w-full h-72 p-1 focus:outline-none text-gray-800"
                            multiple size="15" required>
                        @foreach($santris as $santri)
                            <option value="{{ $santri->id }}" data-kelas="{{ $santri->kelas }}">
                                {{ $santri->nis }} - {{ $santri->nama_siswa }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-2 text-xs text-gray-400">Tekan <b>Ctrl</b> / <b>Shift</b> untuk multi-pilih</div>
            </div>

            {{-- Tombol --}}
            <div class="flex flex-col items-center justify-center h-full">
                <svg class="w-8 h-8 text-gray-400 mb-2 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l7-7-7-7"/>
                </svg>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-2xl shadow-xl transition-all duration-150 text-lg">
                    Pindahkan &rarr;
                </button>
            </div>

            {{-- Kelas Tujuan --}}
            <div class="bg-white rounded-2xl shadow-lg p-5 flex flex-col">
                <label class="block font-bold text-gray-700 mb-2">Kelas Tujuan</label>
                <select name="kelas_id" id="kelas_tujuan" class="w-full rounded-lg border p-2 mb-4 bg-blue-50" onchange="loadSantriTujuan()" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" data-kode="{{ $kelas->kode }}">{{ $kelas->nama }} ({{ $kelas->kode }})</option>
                    @endforeach
                </select>
                <div class="flex-1 overflow-y-auto rounded-lg border bg-gray-50">
                    <select id="santri_tujuan_list" class="w-full h-72 p-1 text-gray-800" multiple size="15" disabled>
                        <!-- Diisi oleh JS -->
                    </select>
                </div>
                <div class="mt-2 text-xs text-gray-400">Anggota kelas tujuan</div>
            </div>

        </div>
    </form>
</div>

{{-- JS --}}
<script>
    // Data santri untuk JS filter
    let santris = @json($santris);

    function filterSantriAsal() {
        var asal = document.getElementById('kelas_asal').value;
        var list = document.getElementById('santri_asal_list').options;
        for (var i = 0; i < list.length; i++) {
            if (asal === "" || list[i].getAttribute('data-kelas') === asal) {
                list[i].style.display = '';
            } else {
                list[i].style.display = 'none';
                list[i].selected = false;
            }
        }
    }

    function loadSantriTujuan() {
        var tujuanSelect = document.getElementById('kelas_tujuan');
        var tujuanId = tujuanSelect.value;
        var tujuanKode = tujuanSelect.options[tujuanSelect.selectedIndex]?.getAttribute('data-kode');
        var tujuanList = document.getElementById('santri_tujuan_list');
        tujuanList.innerHTML = '';

        if (tujuanKode) {
            santris.forEach(function(s) {
                if (s.kelas === tujuanKode) {
                    let option = document.createElement('option');
                    option.text = (s.nis ? s.nis : '-') + ' - ' + s.nama_siswa;
                    tujuanList.add(option);
                }
            });
        }
    }
</script>
@endsection