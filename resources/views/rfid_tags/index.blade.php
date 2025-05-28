@extends('layouts.admin')

@section('content')
<div x-data="{ openFilter:false, openModal:false, selected:{id:null,nis:'',nama_siswa:''} }"
     class="max-w-6xl mx-auto py-6">

  {{-- HEADER + SEARCH/FILTER --}}
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-2xl font-bold text-blue-800 flex items-center gap-2">
      📡 Daftar RFID Santri
    </h2>
    <div class="flex items-center space-x-2">
      {{-- Search --}}
      <form id="formCari" method="GET" action="{{ route('rfid-tags.index') }}" class="relative">
        <svg xmlns="http://www.w3.org/2000/svg"
             class="absolute left-3 top-1/2 -translate-y-1/2 text-blue-300 w-4 h-4"
             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1016.65 16.65z"/>
        </svg>
        <input name="search" value="{{ request('search') }}"
               placeholder="Cari Nama / NIS"
               class="form-input border border-blue-200 rounded-lg px-3 py-2 pl-10 focus:ring-blue-400 focus:border-blue-400 shadow-sm w-64"/>
      </form>
      {{-- Filter --}}
      <button @click="openFilter = true"
              class="bg-white border border-blue-200 text-gray-700 px-4 py-2 rounded-lg shadow hover:bg-blue-100 transition flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2l-6 6v7l-4-2v-5L3 6V4z"/>
        </svg>
        Filter
      </button>
    </div>
  </div>

  {{-- FILTER POPUP --}}
  <div x-show="openFilter" @click.away="openFilter=false" x-cloak
       class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white p-6 rounded-xl shadow-lg max-w-sm w-full relative">
      <button @click="openFilter=false"
              class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-2xl leading-none">
        &times;
      </button>
      <h3 class="text-lg font-semibold mb-4">Filter Data Santri</h3>
      <form method="GET" action="{{ route('rfid-tags.index') }}">
        {{-- Kelas --}}
        <div class="mb-4">
          <label class="block text-sm mb-1">Kelas</label>
         <select name="kelas" class="w-full border rounded px-3 py-2">
  <option value="">Semua</option>
  @foreach($kelasList as $k)
    <option value="{{ $k->id }}" {{ request('kelas')==$k->id ? 'selected':'' }}>
      {{ $k->nama }}
    </option>
  @endforeach
</select>

        </div>
        {{-- Jenis Kelamin --}}
        <div class="mb-4">
          <label class="block text-sm mb-1">Jenis Kelamin</label>
          <select name="jenis_kelamin" class="w-full border rounded px-3 py-2">
            <option value="">Semua</option>
            <option value="L" {{ request('jenis_kelamin')=='L'?'selected':'' }}>Laki-laki</option>
            <option value="P" {{ request('jenis_kelamin')=='P'?'selected':'' }}>Perempuan</option>
          </select>
        </div>
        {{-- Status --}}
        <div class="mb-4">
          <label class="block text-sm mb-1">Status</label>
          <select name="status" class="w-full border rounded px-3 py-2">
            <option value="">Semua</option>
            <option value="aktif"  {{ request('status')=='aktif'?'selected':'' }}>Aktif</option>
            <option value="alumni" {{ request('status')=='alumni'?'selected':'' }}>Alumni</option>
            <option value="cuti"   {{ request('status')=='cuti'?'selected':'' }}>Cuti</option>
          </select>
        </div>
        <div class="flex gap-2">
          <button type="submit"
                  class="bg-blue-600 text-white px-4 py-2 rounded w-full">
            Terapkan
          </button>
          <a href="{{ route('rfid-tags.index') }}"
             class="bg-gray-400 text-white px-4 py-2 rounded w-full text-center">
            Reset
          </a>
        </div>
      </form>
    </div>
  </div>

  {{-- DATA TABLE --}}
  <div class="overflow-x-auto bg-white rounded-xl shadow-lg border mt-2">
    <table class="min-w-full text-sm text-left">
      <thead class="bg-blue-100 text-xs font-bold uppercase sticky top-0 z-10">
        <tr>
          <th class="p-3">No</th>
          <th class="p-3">NIS</th>
          <th class="p-3">Nama Santri</th>
          <th class="p-3">Kelas</th>
          <th class="p-3">Nomor RFID/UID</th>
          <th class="p-3">PIN</th>
          <th class="p-3 text-center">Aksi</th>
        </tr>
      </thead>
    <tbody>
  @foreach($santris as $i => $santri)
    <tr class="hover:bg-blue-50 border-t">
      <td class="p-3">{{ $santris->firstItem() + $i }}</td>
      <td class="p-3">{{ $santri->nis }}</td>
      <td class="p-3">{{ $santri->nama_siswa }}</td>
     <td class="p-3">
  {{ $santri->kelasRelasi->pluck('nama')->join(', ') ?: '-' }}
</td>

      <td class="p-3">{{ $santri->rfidTag->tag_uid ?? '-' }}</td>
      <td class="p-3">{{ $santri->rfidTag->pin     ?? '-' }}</td>
      <td class="p-3 text-center">
        {{-- Icon-only button --}}
        <button @click="openModal=true;
                       selected.id={{$santri->id}};
                       selected.nis='{{$santri->nis}}';
                       selected.nama_siswa='{{addslashes($santri->nama_siswa)}}';"
                class="p-2 bg-green-50 hover:bg-green-100 text-green-700 rounded-full">
          <svg xmlns="http://www.w3.org/2000/svg"
               class="w-5 h-5" fill="none" viewBox="0 0 24 24"
               stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M4 7H20M4 12H20M4 17H20"/>
          </svg>
        </button>
      </td>
    </tr>
  @endforeach
</tbody>

    </table>
  </div>

  {{-- PAGINATION --}}
  <div class="px-4 py-3 bg-gray-50 rounded-b-lg flex justify-end mt-2">
    {{ $santris->links() }}
  </div>

  {{-- SET RFID MODAL --}}
  <div x-show="openModal" @click.away="openModal=false" x-cloak
       class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
      <h2 class="text-lg font-semibold mb-3">Set RFID untuk Santri</h2>
      <p class="mb-4"><strong x-text="selected.nis"></strong> — <span x-text="selected.nama_siswa"></span></p>
      <form action="{{ route('rfid-tags.store') }}" method="POST" class="space-y-4">
        @csrf
        <input type="hidden" name="santri_id" :value="selected.id">
        <div>
          <label class="block text-sm mb-1">Nomor RFID/UID</label>
          <input type="text" name="tag_uid" required class="w-full border rounded px-3 py-2"/>
        </div>
        <div>
          <label class="block text-sm mb-1">PIN</label>
          <input type="text" name="pin" required class="w-full border rounded px-3 py-2"/>
        </div>
        <div class="flex justify-end gap-2">
          <button type="button" @click="openModal=false" class="px-4 py-2 border rounded">Batal</button>
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection
