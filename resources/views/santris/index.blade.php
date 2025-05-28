@extends('layouts.admin')

@section('content')
<div x-data="{
        openFilter: false,
        openDataModal: false,
        mutasiOpen: false,
        mutasiId: null,
        mutasiNama: ''
    }"
    class="max-w-6xl mx-auto py-6"
>

    {{-- Header + Actions --}}
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-bold text-blue-800 flex items-center gap-2">📋 Daftar Santri</h2>
        <div class="flex items-center gap-2">
            <button @click="openDataModal = true"
                    class="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 flex items-center gap-2">
                <!-- 👁 Icon Opsi Data -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2" />
                </svg>
                Opsi Data
            </button>
            <a href="{{ route('santris.create') }}"
               class="bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700 flex items-center gap-2">
                ➕ Tambah Santri
            </a>
            <button @click="openFilter = true"
                    class="bg-white border px-2 py-2 rounded-full shadow hover:bg-blue-100 transition">
                <!-- 🔍 Filter Icon -->
                <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2l-6 6v7l-4-2v-5L3 6V4z"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Import/Export Modal --}}
    <div x-show="openDataModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
      <div @click.away="openDataModal = false" class="bg-white rounded-xl p-8 max-w-md w-full shadow-xl border">
        <h3 class="text-xl font-bold mb-4 text-blue-800">Kelola Data Santri</h3>
        <div class="flex flex-col gap-4">
          {{-- Export --}}
          <form action="{{ route('santris.export') }}" method="GET" class="flex flex-col gap-2">
            <label class="text-blue-600 font-semibold">Export Excel</label>
            <select name="kelas_id" class="border rounded p-2">
              <option value="">Semua Kelas</option>
              @foreach($kelasList as $kelas)
                <option value="{{ $kelas->id }}">{{ $kelas->nama }}</option>
              @endforeach
            </select>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Export</button>
          </form>
          {{-- Template --}}
          <a href="{{ route('santris.template') }}"
             class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded text-center block">
             Download Template Excel
          </a>
          {{-- Import --}}
          <form action="{{ route('santris.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-2">
            @csrf
            <label class="text-blue-600 font-semibold">Import Excel</label>
            <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="border rounded p-2" />
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">Import</button>
          </form>
        </div>
        <div class="flex justify-end mt-6">
          <button @click="openDataModal = false" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Tutup</button>
        </div>
      </div>
    </div>

    {{-- Filter Modal --}}
    <div x-show="openFilter" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
      <div class="bg-white p-6 rounded shadow max-w-sm w-full relative">
        <button class="absolute top-2 right-2" @click="openFilter = false">&times;</button>
        <h3 class="text-lg font-semibold mb-4">Filter Data Santri</h3>
        <form method="GET" action="{{ route('santris.index') }}">
          {{-- Kelas --}}
          <div class="mb-3">
            <label class="block text-sm">Kelas</label>
            <select name="kelas" class="border rounded w-full">
              <option value="">Semua</option>
              @foreach($kelasList as $kelas)
                <option value="{{ $kelas->id }}" {{ request('kelas')==$kelas->id?'selected':'' }}>
                  {{ $kelas->nama }}
                </option>
              @endforeach
            </select>
          </div>
          {{-- JK --}}
          <div class="mb-3">
            <label class="block text-sm">Jenis Kelamin</label>
            <select name="jenis_kelamin" class="border rounded w-full">
              <option value="">Semua</option>
              <option value="L" {{ request('jenis_kelamin')=='L'?'selected':'' }}>Laki-laki</option>
              <option value="P" {{ request('jenis_kelamin')=='P'?'selected':'' }}>Perempuan</option>
            </select>
          </div>
          {{-- Status --}}
          <div class="mb-3">
            <label class="block text-sm">Status</label>
            <select name="status" class="border rounded w-full">
              <option value="">Semua</option>
              <option value="aktif"  {{ request('status')=='aktif'?'selected':'' }}>Aktif</option>
              <option value="alumni" {{ request('status')=='alumni'?'selected':'' }}>Alumni</option>
              <option value="cuti"   {{ request('status')=='cuti'?'selected':'' }}>Cuti</option>
            </select>
          </div>
          <div class="flex gap-2 mt-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-full">Terapkan</button>
            <a href="{{ route('santris.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded w-full text-center">Reset</a>
          </div>
        </form>
      </div>
    </div>

    {{-- Search --}}
    <div class="flex justify-end mb-4">
      <form id="formCariSantri" method="GET" action="{{ route('santris.index') }}" class="flex gap-2">
        <div class="relative">
          <input id="searchBox" name="search" value="{{ request('search') }}"
                 placeholder="Cari Nama / NIS"
                 class="form-input border border-blue-200 rounded-lg px-3 py-2 pl-8 focus:ring-blue-400 focus:border-blue-400 shadow-sm transition"/>
          <span class="absolute left-2 top-1/2 -translate-y-1/2 text-blue-300">
            <!-- 🔍 -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1016.65 16.65z"/>
            </svg>
          </span>
        </div>
      </form>
    </div>

    {{-- Data Table --}}
    <div class="overflow-x-auto bg-white rounded-xl shadow-lg border mt-2">
      <table class="min-w-full text-sm text-left">
        <thead class="bg-blue-100 text-xs font-bold uppercase sticky top-0 z-10">
          <tr>
            <th class="p-3">NIS</th>
            <th class="p-3">Nama</th>
            <th class="p-3">NISN</th>
            <th class="p-3">TTL</th>
            <th class="p-3">JK</th>
            <th class="p-3 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($santris as $santri)
            <tr class="hover:bg-blue-50 border-t">
              <td class="p-3">{{ $santri->nis }}</td>
              <td class="p-3 flex items-center gap-2">
                @if($santri->foto)
                  <img src="{{ asset('storage/'.$santri->foto) }}" class="w-8 h-8 rounded-full"/>
                @else
                  <div class="w-8 h-8 rounded-full bg-blue-300 flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr($santri->nama_siswa,0,1)) }}
                  </div>
                @endif
                {{ $santri->nama_siswa }}
              </td>
              <td class="p-3">{{ $santri->nisn }}</td>
              <td class="p-3">{{ $santri->tempat_lahir.', '.\Carbon\Carbon::parse($santri->tanggal_lahir)->format('d-m-Y') }}</td>
              <td class="p-3">
                <span class="inline-block px-2 py-1 text-xs rounded-full {{ $santri->jenis_kelamin=='L'?'bg-blue-100 text-blue-700':'bg-pink-100 text-pink-700' }}">
                  {{ $santri->jenis_kelamin }}
                </span>
              </td>
              <td class="p-3 text-center flex justify-center gap-2">
                <!-- Preview -->
                <a class="p-2 rounded-full bg-green-50 hover:bg-green-200 text-green-700">
                  <!-- 👁 -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7-5.065 7-9.542 7S3.732 16.057 2.458 12z"/>
                  </svg>
                </a>
                <!-- Edit -->
                <a class="p-2 rounded-full bg-blue-50 hover:bg-blue-200 text-blue-700">
                  <!-- ✏️ -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M4 20h4l11-11-4-4L4 16v4z"/>
                  </svg>
                </a>
                <!-- Mutasi -->
                <button type="button"
                        @click="mutasiOpen = true; mutasiId = {{ $santri->id }}; mutasiNama = '{{ addslashes($santri->nama_siswa) }}';"
                         class="p-2 rounded-full bg-yellow-50 hover:bg-yellow-200 text-yellow-700">
                  <!-- 🔄 -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 11-6 0V7a3 3 0 116 0v1"/>
                  </svg>
                </button>
                <!-- Hapus -->
                <form action="{{ route('santris.destroy',$santri->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?')" class="inline-block">
                  @csrf @method('DELETE')
                  <button type="submit" class="p-2 rounded-full bg-red-50 hover:bg-red-200 text-red-600">
                    <!-- 🗑️ -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19 7H5M6 7l1 12a2 2 0 002 2h6a2 2 0 002-2l1-12M10 11v6M14 11v6M9 7V4h6v3"/>
                    </svg>
                  </button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    <div class="px-4 py-3 bg-gray-50 rounded-b-lg flex justify-end">
      {{ $santris->links() }}
    </div>

    {{-- Mutasi Modal --}}
    <div x-show="mutasiOpen" @click.away="mutasiOpen = false" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h2 class="text-lg font-bold mb-3">Mutasi Santri</h2>
        <form method="POST" :action="`/santris/${mutasiId}/mutasi`">
          @csrf
          <div class="mb-3">
            <label class="block text-sm mb-1 font-semibold">Nama Santri</label>
            <input type="text" x-model="mutasiNama" disabled
                   class="form-input w-full bg-gray-100"/>
          </div>
          <div class="mb-3">
            <label class="block text-sm mb-1">Alasan Mutasi</label>
            <input type="text" name="alasan" class="form-input w-full" required>
          </div>
          <div class="mb-3">
            <label class="block text-sm mb-1">Tujuan Mutasi</label>
            <input type="text" name="tujuan_mutasi" class="form-input w-full" required>
          </div>
          <div class="flex justify-end gap-2">
            <button type="button" @click="mutasiOpen = false"
                    class="bg-gray-300 px-3 py-1 rounded">Batal</button>
            <button type="submit" class="bg-yellow-500 text-white px-4 py-1 rounded hover:bg-yellow-600">
              Mutasi
            </button>
          </div>
        </form>
      </div>
    </div>
</div>

{{-- Search debounce --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
  let t;
  const f = document.getElementById('formCariSantri'),
        i = document.getElementById('searchBox');
  if(f && i) i.addEventListener('input', () => {
    clearTimeout(t);
    t = setTimeout(() => f.submit(), 400);
  });
});
</script>
@endsection
