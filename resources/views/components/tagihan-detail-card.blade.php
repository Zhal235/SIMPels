<div class="bg-white rounded-lg shadow p-4">
    <div class="border-b pb-3 mb-4">
        <h3 class="text-lg font-semibold text-gray-900">{{ $tagihan->nama }}</h3>
        <p class="text-sm text-gray-500">{{ $tagihan->deskripsi }}</p>
    </div>

    <div class="space-y-3">
        <div class="flex justify-between">
            <span class="text-sm text-gray-600">Nominal Default</span>
            <span class="font-medium">Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</span>
        </div>

        @if($tagihan->is_nominal_per_kelas && isset($santri))
            <div class="flex justify-between text-blue-600">
                <span class="text-sm">Nominal Kelas {{ $santri->kelas->nama }}</span>
                <span class="font-medium">Rp {{ number_format($tagihan->getNominalForKelas($santri->kelas_id), 0, ',', '.') }}</span>
            </div>
        @endif

        <div class="flex justify-between">
            <span class="text-sm text-gray-600">Tipe Pembayaran</span>
            <span class="font-medium">{{ $tagihan->is_bulanan ? 'Bulanan' : 'Sekali Bayar' }}</span>
        </div>

        <div class="flex justify-between">
            <span class="text-sm text-gray-600">Kategori</span>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                       {{ $tagihan->kategori_tagihan == 'Rutin' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800' }}">
                {{ $tagihan->kategori_tagihan }}
            </span>
        </div>

        @if($tagihan->tahun_ajaran)
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Tahun Ajaran</span>
                <span class="font-medium">{{ $tagihan->tahun_ajaran->nama_tahun_ajaran }}</span>
            </div>
        @endif
    </div>
</div>
