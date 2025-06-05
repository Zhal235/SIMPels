@extends('layouts.admin')

@section('content')
    <div class="container px-6 mx-auto grid">
        <div class="flex justify-between items-center">
            <h2 class="my-6 text-2xl font-semibold text-gray-700">
                Nominal per Kelas - {{ $jenisTagihan->nama }}
            </h2>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <form action="{{ route('keuangan.jenis-tagihan.update-kelas', $jenisTagihan->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <div class="font-medium text-gray-700">Nominal Default: Rp {{ number_format($jenisTagihan->nominal, 0, ',', '.') }}</div>
                            <p class="text-sm text-gray-500">Atur nominal berbeda untuk setiap kelas di bawah ini.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($kelas as $kls)
                                <div class="border rounded-lg p-4">
                                    <label for="kelas_{{ $kls->id }}" class="block text-sm font-medium text-gray-700">
                                        {{ $kls->nama }} ({{ $kls->tingkat }})
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">Rp</span>
                                        </div>
                                        <input type="number" 
                                               name="nominal[{{ $kls->id }}]" 
                                               id="kelas_{{ $kls->id }}"
                                               value="{{ old('nominal.' . $kls->id, $jenisTagihan->getNominalForKelas($kls->id)) }}"
                                               class="pl-12 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    @error('nominal.' . $kls->id)
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ route('keuangan.jenis-tagihan.index') }}" 
                               class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Kembali
                            </a>
                            <button type="submit"
                                    class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>                </div>
            </div>
        </div>
    </div>
@endsection
