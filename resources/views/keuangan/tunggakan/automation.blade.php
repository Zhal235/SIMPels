@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
                <span class="material-icons-outlined text-blue-600">autorenew</span>
                Otomatisasi Tagihan Rutin
            </h1>
            <p class="text-gray-600 mt-2">Kelola otomatisasi penyalinan tagihan rutin ke tahun ajaran baru.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 mt-4 sm:mt-0">
            <a href="{{ route('keuangan.tunggakan.santri-aktif') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                <span class="material-icons-outlined text-sm mr-2">arrow_back</span>
                Kembali ke Tunggakan
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Statistics Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Statistik Sistem</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Tahun Ajaran Aktif:</span>
                        <span class="font-medium">{{ $currentTahunAjaran->nama ?? 'Tidak ada' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Santri Aktif:</span>
                        <span class="font-medium">{{ number_format($totalActiveSantri) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Jenis Tagihan Rutin:</span>
                        <span class="font-medium">{{ $routineJenisTagihan->count() }}</span>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t">
                    <h4 class="font-medium text-gray-900 mb-2">Jenis Tagihan Rutin:</h4>
                    <div class="space-y-1">
                        @foreach($routineJenisTagihan as $jenis)
                        <div class="text-sm text-gray-600 flex items-center">
                            <span class="w-2 h-2 bg-blue-400 rounded-full mr-2"></span>
                            {{ $jenis->nama }}
                            @if($jenis->is_bulanan)
                                <span class="ml-1 text-xs bg-blue-100 text-blue-700 px-1 rounded">Bulanan</span>
                            @else
                                <span class="ml-1 text-xs bg-green-100 text-green-700 px-1 rounded">Tahunan</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Copy Routine Tagihan Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6" x-data="copyRoutineForm()">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 border-b pb-2">
                    <span class="material-icons-outlined text-blue-600 mr-2">content_copy</span>
                    Salin Tagihan Rutin ke Tahun Ajaran Baru
                </h3>

                <form @submit.prevent="submitForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Target Year -->
                        <div>
                            <label for="target_year_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Tahun Ajaran Tujuan *
                            </label>
                            <select x-model="form.target_year_id" @change="getPreview" 
                                    id="target_year_id" name="target_year_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih Tahun Ajaran Tujuan</option>
                                @foreach($allTahunAjaran as $tahun)
                                    <option value="{{ $tahun->id }}">{{ $tahun->nama }}</option>
                                @endforeach
                            </select>
                            <p class="text-sm text-gray-500 mt-1">Tahun ajaran yang akan menerima tagihan rutin</p>
                        </div>

                        <!-- Source Year -->
                        <div>
                            <label for="source_year_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Tahun Ajaran Sumber
                            </label>
                            <select x-model="form.source_year_id" @change="getPreview"
                                    id="source_year_id" name="source_year_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Otomatis (Tahun Sebelumnya)</option>
                                @foreach($allTahunAjaran as $tahun)
                                    <option value="{{ $tahun->id }}">{{ $tahun->nama }}</option>
                                @endforeach
                            </select>
                            <p class="text-sm text-gray-500 mt-1">Kosongkan untuk menggunakan tahun ajaran sebelumnya</p>
                        </div>
                    </div>

                    <!-- Preview -->
                    <div x-show="preview.show" class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h4 class="font-medium text-blue-900 mb-3">Preview Penyalinan:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-blue-700">Dari:</span>
                                <span x-text="preview.data.source_year" class="font-medium"></span>
                            </div>
                            <div>
                                <span class="text-blue-700">Ke:</span>
                                <span x-text="preview.data.target_year" class="font-medium"></span>
                            </div>
                            <div>
                                <span class="text-blue-700">Santri yang Terpengaruh:</span>
                                <span x-text="preview.data.affected_santri" class="font-medium"></span>
                            </div>
                            <div>
                                <span class="text-blue-700">Estimasi Tagihan:</span>
                                <span x-text="preview.data.estimated_tagihan" class="font-medium"></span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="text-blue-700 text-sm">Jenis Tagihan Rutin:</span>
                            <div class="flex flex-wrap gap-1 mt-1">
                                <template x-for="jenis in preview.data.routine_jenis_list">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800" x-text="jenis"></span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6 flex justify-end">
                        <button type="submit" :disabled="loading || !canSubmit" 
                                class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                            <span x-show="loading" class="material-icons-outlined text-sm mr-2 animate-spin">refresh</span>
                            <span x-show="!loading" class="material-icons-outlined text-sm mr-2">content_copy</span>
                            <span x-text="loading ? 'Memproses...' : 'Salin Tagihan Rutin'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Info Section -->
    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <span class="material-icons-outlined text-yellow-600">info</span>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Informasi Otomatisasi Tagihan Rutin</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Fitur ini akan menyalin semua tagihan rutin dari tahun ajaran sebelumnya ke tahun ajaran baru untuk santri yang masih aktif.</li>
                        <li>Tagihan yang disalin hanya yang berkategori "Rutin" dan akan menggunakan nominal yang sama dengan tahun sebelumnya.</li>
                        <li>Jika ada penyesuaian nominal untuk kelas tertentu, nominal akan disesuaikan otomatis.</li>
                        <li>Tagihan yang sudah ada tidak akan digandakan - sistem akan melewati tagihan yang sudah dibuat.</li>
                        <li>Untuk tagihan rutin bulanan, sistem akan membuat 12 tagihan (Juli-Juni) sesuai tahun ajaran.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyRoutineForm() {
    return {
        form: {
            target_year_id: '',
            source_year_id: ''
        },
        preview: {
            show: false,
            data: {}
        },
        loading: false,

        get canSubmit() {
            return this.form.target_year_id && this.preview.show;
        },

        async getPreview() {
            if (!this.form.target_year_id) {
                this.preview.show = false;
                return;
            }

            try {
                this.loading = true;
                const response = await fetch('{{ route("keuangan.tunggakan.preview-copy-routine") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.form)
                });

                const result = await response.json();
                
                if (result.success) {
                    this.preview.data = result.data;
                    this.preview.show = true;
                } else {
                    this.preview.show = false;
                    alert(result.message);
                }
            } catch (error) {
                console.error('Error getting preview:', error);
                this.preview.show = false;
            } finally {
                this.loading = false;
            }
        },

        async submitForm() {
            if (!this.canSubmit) return;

            if (!confirm('Apakah Anda yakin ingin menyalin tagihan rutin? Proses ini akan membuat tagihan baru dan tidak dapat dibatalkan.')) {
                return;
            }

            try {
                this.loading = true;
                
                const formData = new FormData();
                formData.append('target_year_id', this.form.target_year_id);
                if (this.form.source_year_id) {
                    formData.append('source_year_id', this.form.source_year_id);
                }
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                const response = await fetch('{{ route("keuangan.tunggakan.copy-routine") }}', {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Terjadi kesalahan saat memproses permintaan.');
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                alert('Terjadi kesalahan saat memproses permintaan.');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection
