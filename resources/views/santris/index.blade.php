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
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 pb-4 border-b border-gray-200">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                </svg>
                Manajemen Santri
            </h1>
            <p class="text-sm text-gray-500 mt-1">Kelola data santri, impor, ekspor, dan filter data.</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button @click="openDataModal = true"
                    class="w-full sm:w-auto bg-white text-blue-600 border border-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 transition duration-150 ease-in-out flex items-center justify-center gap-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                Opsi Data
            </button>
            <a href="{{ route('santris.create') }}"
               class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out flex items-center justify-center gap-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Tambah Santri
            </a>
            <button @click="openFilter = true"
                    class="w-full sm:w-auto bg-white border border-gray-300 px-3 py-2 rounded-lg shadow-sm hover:bg-gray-50 transition duration-150 ease-in-out flex items-center justify-center">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2l-6 6v7l-4-2v-5L3 6V4z"/>
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

    {{-- Search and Per Page Selector --}}
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <form id="formCariSantri" method="GET" action="{{ route('santris.index') }}" class="w-full sm:w-auto flex-grow sm:flex-grow-0">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input id="searchBox" name="search" value="{{ request('search') }}"
                       placeholder="Cari Nama / NIS Santri..."
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm shadow-sm transition duration-150 ease-in-out"/>
            </div>
            {{-- Hidden fields for existing filters --}}
            @if(request('kelas'))
                <input type="hidden" name="kelas" value="{{ request('kelas') }}">
            @endif
            @if(request('jenis_kelamin'))
                <input type="hidden" name="jenis_kelamin" value="{{ request('jenis_kelamin') }}">
            @endif
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
        </form>

        <form method="GET" action="{{ route('santris.index') }}" id="perPageForm" class="flex items-center gap-2 w-full sm:w-auto">
            <label for="per_page" class="text-sm font-medium text-gray-700">Tampilkan:</label>
            <select name="per_page" id="per_page" onchange="document.getElementById('perPageForm').submit()"
                    class="form-select block w-auto pl-3 pr-8 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg shadow-sm transition duration-150 ease-in-out">
                <option value="10" {{ request('per_page', $santris->perPage()) == 10 ? 'selected' : '' }}>10</option>
                <option value="15" {{ request('per_page', $santris->perPage()) == 15 ? 'selected' : '' }}>15</option>
                <option value="25" {{ request('per_page', $santris->perPage()) == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page', $santris->perPage()) == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page', $santris->perPage()) == 100 ? 'selected' : '' }}>100</option>
            </select>
            <span class="text-sm text-gray-700">data</span>
            {{-- Hidden fields for existing filters and search --}}
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            @if(request('kelas'))
                <input type="hidden" name="kelas" value="{{ request('kelas') }}">
            @endif
            @if(request('jenis_kelamin'))
                <input type="hidden" name="jenis_kelamin" value="{{ request('jenis_kelamin') }}">
            @endif
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
        </form>
    </div>

    {{-- Data Table --}}
    <div class="overflow-x-auto bg-white rounded-xl shadow-lg border border-gray-200 mt-2">
      <table class="min-w-full text-sm text-left text-gray-700">
        <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-600 sticky top-0 z-10">
          <tr>
            <th class="px-4 py-3 text-center">No</th>
            <th class="px-4 py-3 text-center">NIS</th>
            <th class="px-4 py-3 text-left">Nama</th>
            <th class="px-4 py-3 text-center">NISN</th>
            <th class="px-4 py-3 text-left">TTL</th>
            <th class="px-4 py-3 text-center">Jenis Kelamin</th>
            <th class="px-4 py-3 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @forelse($santris as $index => $santri)
            <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
              <td class="px-4 py-3 text-center font-medium">{{ $santris->firstItem() + $index }}</td>
              <td class="px-4 py-3 text-center">{{ $santri->nis }}</td>
              <td class="px-4 py-3 flex items-center gap-3">
                @if($santri->foto)
                  <img src="{{ asset('storage/'.$santri->foto) }}" class="w-9 h-9 rounded-full object-cover shadow-sm"/>
                @else
                  <div class="w-9 h-9 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold text-sm shadow-sm">
                    {{ strtoupper(substr($santri->nama_santri,0,1)) }}
                  </div>
                @endif
                <span class="font-medium">{{ $santri->nama_santri }}</span>
              </td>
              <td class="px-4 py-3 text-center">{{ $santri->nisn }}</td>
              <td class="px-4 py-3">{{ $santri->tempat_lahir.', '.\Carbon\Carbon::parse($santri->tanggal_lahir)->format('d F Y') }}</td>
              <td class="px-4 py-3 text-center">
                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full {{ $santri->jenis_kelamin=='L'?'bg-blue-100 text-blue-700':'bg-pink-100 text-pink-700' }}">
                  {{ $santri->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <div class="flex justify-center items-center gap-2">
                    <a href="{{ route('santris.show', $santri->id) }}" title="Lihat Detail"
                       class="p-2 rounded-full text-green-600 hover:bg-green-100 transition duration-150 ease-in-out">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                      </svg>
                    </a>
                    <a href="{{ route('santris.edit', $santri->id) }}" title="Edit Santri"
                       class="p-2 rounded-full text-blue-600 hover:bg-blue-100 transition duration-150 ease-in-out">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                      </svg>
                    </a>
                    <button type="button" title="Mutasi Santri"
                            @click="mutasiOpen = true; mutasiId = {{ $santri->id }}; mutasiNama = '{{ addslashes($santri->nama_santri) }}';"
                            class="p-2 rounded-full text-yellow-600 hover:bg-yellow-100 transition duration-150 ease-in-out">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path d="M6.99 11L3 15l3.99 4v-3H14v-2H6.99v-3zM21 9l-3.99-4v3H10v2h7.01v3L21 9z"/>
                      </svg>
                    </button>
                    <form action="{{ route('santris.destroy',$santri->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus santri bernama {{ addslashes($santri->nama_santri) }}?')" class="inline-block">
                      @csrf @method('DELETE')
                      <button type="submit" title="Hapus Santri"
                              class="p-2 rounded-full text-red-600 hover:bg-red-100 transition duration-150 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                          <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                        </svg>
                      </button>
                    </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
                <td colspan="7" class="text-center py-10 text-gray-500">
                    <div class="flex flex-col items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-3" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.415L11 9.586V6z" clip-rule="evenodd" />
                          </svg>
                        <p class="font-semibold text-lg">Data Santri Tidak Ditemukan</p>
                        <p class="text-sm">Tidak ada data santri yang sesuai dengan kriteria pencarian atau filter Anda.</p>
                        @if(request()->has('search') || request()->has('kelas') || request()->has('jenis_kelamin') || request()->has('status'))
                            <a href="{{ route('santris.index') }}" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out">
                                Reset Filter & Pencarian
                            </a>
                        @else
                            <a href="{{ route('santris.create') }}" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out">
                                Tambah Santri Baru
                            </a>
                        @endif
                    </div>
                </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $santris->appends(request()->query())->links() }}
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
