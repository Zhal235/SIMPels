@extends('layouts.admin')

@section('content')
<div class="p-4">
  <h1 class="text-xl font-semibold mb-4">Tambah UID RFID</h1>

  @if ($errors->any())
    <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
      <ul class="list-disc list-inside">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('rfid-tags.store') }}" method="POST" class="bg-white shadow rounded p-4 space-y-4">
    @csrf

    <div>
      <label class="block mb-1 font-medium">Santri (opsional)</label>
      <select name="santri_id" class="w-full border px-3 py-2 rounded">
        <option value="">– Pilih Santri –</option>
        @foreach($santris as $s)
          <option value="{{ $s->id }}" {{ old('santri_id') == $s->id ? 'selected' : '' }}>
            {{ $s->nama_santri }}
          </option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block mb-1 font-medium">Nomor RFID/UID</label>
      <input type="text" name="tag_uid" value="{{ old('tag_uid') }}"
             class="w-full border px-3 py-2 rounded" required>
    </div>

    <div>
      <label class="block mb-1 font-medium">PIN</label>
      <input type="text" name="pin" value="{{ old('pin') }}"
             class="w-full border px-3 py-2 rounded" required>
    </div>

    <div class="flex justify-end">
      <a href="{{ route('rfid-tags.index') }}"
         class="mr-2 px-4 py-2 border rounded hover:bg-gray-100">
        Batal
      </a>
      <button type="submit"
              class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Simpan
      </button>
    </div>
  </form>
</div>
@endsection
