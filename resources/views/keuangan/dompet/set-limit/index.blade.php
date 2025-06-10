@extends('layouts.admin')

@section('content')
<script>
    function showBulkEditModal() {
        document.getElementById('bulkEditModal').classList.remove('hidden');
        loadFilterOptions();
    }

    function hideBulkEditModal() {
        document.getElementById('bulkEditModal').classList.add('hidden');
        // Reset form
        document.getElementById('bulkNewLimit').value = '';
        document.getElementById('bulkKelas').value = '';
        document.getElementById('bulkStatus').value = '';
        document.getElementById('selectAllSantri').checked = false;
        // Re-enable dropdowns
        document.getElementById('bulkKelas').disabled = false;
        document.getElementById('bulkStatus').disabled = false;
        updatePreview();
    }

    function loadFilterOptions() {
        const rows = document.querySelectorAll('tbody tr');
        const kelasSet = new Set();
        
        rows.forEach(row => {
            const kelasCell = row.querySelector('td:nth-child(4) span');
            if (kelasCell && kelasCell.textContent.trim() !== '-') {
                kelasSet.add(kelasCell.textContent.trim());
            }
        });

        const kelasSelect = document.getElementById('bulkKelas');
        kelasSelect.innerHTML = '<option value="">Semua Kelas</option>';
        [...kelasSet].sort().forEach(kelas => {
            const option = document.createElement('option');
            option.value = kelas;
            option.textContent = kelas;
            kelasSelect.appendChild(option);
        });

        updatePreview();
    }

    function updatePreview() {
        const selectAll = document.getElementById('selectAllSantri').checked;
        const kelas = document.getElementById('bulkKelas').value;
        const status = document.getElementById('bulkStatus').value;
        const previewContainer = document.getElementById('previewContainer');
        
        // Disable/enable filter dropdowns based on selectAll
        document.getElementById('bulkKelas').disabled = selectAll;
        document.getElementById('bulkStatus').disabled = selectAll;
        
        const rows = document.querySelectorAll('tbody tr');
        let filteredCount = 0;
        let previewHTML = '';

        rows.forEach(row => {
            let include = selectAll;
            
            if (!selectAll) {
                include = true;
                
                // Filter by kelas
                if (kelas) {
                    const kelasCell = row.querySelector('td:nth-child(4) span');
                    const rowKelas = kelasCell ? kelasCell.textContent.trim() : '';
                    if (rowKelas !== kelas) include = false;
                }
                
                // Filter by status
                if (status) {
                    const statusCell = row.querySelector('td:nth-child(6) span');
                    const isActive = statusCell && statusCell.textContent.includes('Aktif');
                    if (status === 'active' && !isActive) include = false;
                    if (status === 'inactive' && isActive) include = false;
                }
            }
            
            if (include) {
                filteredCount++;
                const nama = row.querySelector('td:nth-child(2)').textContent.trim();
                const nis = row.querySelector('td:nth-child(3)').textContent.trim();
                const currentLimit = row.querySelector('.limit-input').value;
                
                previewHTML += `
                    <div class="flex justify-between items-center p-3 border-b border-gray-100 last:border-b-0">
                        <div>
                            <div class="font-medium text-sm">${nama}</div>
                            <div class="text-xs text-gray-500">${nis}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-500">Limit Saat Ini:</div>
                            <div class="text-sm font-medium">Rp ${parseInt(currentLimit || 0).toLocaleString('id-ID')}</div>
                        </div>
                    </div>
                `;
            }
        });

        if (filteredCount === 0) {
            previewHTML = '<div class="p-4 text-center text-gray-500">Tidak ada santri yang sesuai dengan filter</div>';
        }

        previewContainer.innerHTML = previewHTML;
        document.getElementById('previewCount').textContent = filteredCount + ' santri';
        document.getElementById('updateButton').textContent = `Update ${filteredCount} Santri`;
        document.getElementById('updateButton').disabled = filteredCount === 0;
    }

    async function processBulkEdit() {
        const newLimit = document.getElementById('bulkNewLimit').value;
        if (!newLimit || newLimit <= 0) {
            alert('Masukkan limit harian yang valid');
            return;
        }

        const selectAll = document.getElementById('selectAllSantri').checked;
        const kelas = document.getElementById('bulkKelas').value;
        const status = document.getElementById('bulkStatus').value;
        
        // Get filtered dompet IDs
        const rows = document.querySelectorAll('tbody tr');
        const dompetIds = [];

        rows.forEach(row => {
            let include = selectAll;
            
            if (!selectAll) {
                include = true;
                
                if (kelas) {
                    const kelasCell = row.querySelector('td:nth-child(4) span');
                    const rowKelas = kelasCell ? kelasCell.textContent.trim() : '';
                    if (rowKelas !== kelas) include = false;
                }
                
                if (status) {
                    const statusCell = row.querySelector('td:nth-child(6) span');
                    const isActive = statusCell && statusCell.textContent.includes('Aktif');
                    if (status === 'active' && !isActive) include = false;
                    if (status === 'inactive' && isActive) include = false;
                }
            }
            
            if (include) {
                const limitInput = row.querySelector('.limit-input');
                const dompetId = limitInput ? limitInput.getAttribute('data-dompet-id') : null;
                
                if (dompetId) {
                    dompetIds.push(parseInt(dompetId));
                }
            }
        });

        if (dompetIds.length === 0) {
            alert('Tidak ada santri yang akan diubah');
            return;
        }

        const filterText = selectAll ? 'SEMUA SANTRI' : `${dompetIds.length} santri yang dipilih`;
        const confirmation = confirm(`Apakah Anda yakin ingin mengubah limit harian untuk ${filterText} menjadi Rp ${parseInt(newLimit).toLocaleString('id-ID')}?`);
        if (!confirmation) return;

        try {
            const button = document.getElementById('updateButton');
            button.textContent = 'Memproses...';
            button.disabled = true;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const url = '{{ route("keuangan.dompet.set-limit.bulk-update") }}';

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    dompet_ids: dompetIds,
                    new_limit: parseFloat(newLimit)
                })
            });

            if (!response.ok) {
                throw new Error(`Server error: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                dompetIds.forEach(dompetId => {
                    const input = document.querySelector(`[data-dompet-id="${dompetId}"]`);
                    if (input) {
                        input.value = newLimit;
                        input.dispatchEvent(new Event('change'));
                    }
                });

                hideBulkEditModal();
                showAlert('success', `Berhasil mengubah limit harian untuk ${data.updated_count} santri`);
            } else {
                showAlert('error', 'Gagal mengubah limit harian: ' + (data.message || 'Terjadi kesalahan pada server'));
            }
        } catch (error) {
            showAlert('error', 'Gagal mengubah limit harian: ' + error.message);
        } finally {
            const button = document.getElementById('updateButton');
            if (button) {
                button.textContent = 'Update Santri';
                button.disabled = false;
            }
        }
    }

    function showAlert(type, message) {
        // Create alert element
        const alertDiv = document.createElement('div');
        alertDiv.className = `fixed top-4 right-4 z-50 p-4 rounded shadow-lg ${type === 'success' ? 'bg-green-100 border-l-4 border-green-500 text-green-700' : 'bg-red-100 border-l-4 border-red-500 text-red-700'}`;
        alertDiv.innerHTML = `
            <div class="flex items-center">
                <span class="material-icons-outlined mr-2">${type === 'success' ? 'check_circle' : 'error'}</span>
                <p>${message}</p>
            </div>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Remove after 3 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('bulkKelas')?.addEventListener('change', updatePreview);
        document.getElementById('bulkStatus')?.addEventListener('change', updatePreview);
        document.getElementById('selectAllSantri')?.addEventListener('change', updatePreview);
    });
</script>
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Set Limit Dompet Santri</h1>
            <p class="text-gray-600 mt-2">Atur batas transaksi harian untuk aplikasi EPOS khusus santri</p>
        </div>
        <div class="flex items-center gap-2 mt-4 sm:mt-0">
            <button onclick="showBulkEditModal()" 
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Bulk Edit Limit
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
        <div class="flex items-center">
            <span class="material-icons-outlined mr-2">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
        <div class="flex items-center">
            <span class="material-icons-outlined mr-2">error</span>
            <p>{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Filter Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-3">
            <h3 class="font-medium text-gray-700">Filter & Pencarian</h3>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('keuangan.dompet.set-limit.index') }}" class="flex flex-col lg:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari Santri</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <span class="material-icons-outlined text-gray-400">search</span>
                        </div>
                        <input type="text" name="search" id="search" 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Nama santri, NIS..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="w-full lg:w-48">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                        <span class="material-icons-outlined text-sm mr-2">search</span>
                        Cari
                    </button>
                    <a href="{{ route('keuangan.dompet.set-limit.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                        <span class="material-icons-outlined text-sm mr-2">refresh</span>
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-3">
            <h3 class="font-medium text-gray-700">Daftar Limit Harian Dompet Santri</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-12">No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Santri</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">NIS</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Kelas</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Limit Harian</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($dompets as $index => $item)
                        <tr class="hover:bg-gray-50 transition-colors duration-200" id="row-{{ $item->id }}">
                            <!-- No -->
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">{{ $dompets->firstItem() + $index }}</td>
                            
                            <!-- Nama Santri -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                {{ $item->santri->nama_santri }}
                                @if(!$item->is_active)
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Dompet Nonaktif</span>
                                @endif
                            </td>
                            
                            <!-- NIS -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                {{ $item->santri->nis }}
                            </td>
                            
                            <!-- Kelas -->
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                @if($item->santri->kelasRelasi && $item->santri->kelasRelasi->count() > 0)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $item->santri->kelasRelasi->pluck('nama')->join(', ') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            
                            <!-- Limit Harian -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <span class="text-gray-400 text-sm">Rp</span>
                                        </div>
                                        <input type="number" class="limit-input pl-8 pr-3 py-1 w-32 text-center text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                               data-dompet-id="{{ $item->id }}" 
                                               data-field="limit_harian"
                                               value="{{ $item->dompetLimit->limit_harian ?? 0 }}" 
                                               min="0" step="5000"
                                               placeholder="0">
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">per hari</div>
                            </td>
                            
                            <!-- Status Limit -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($item->dompetLimit && $item->dompetLimit->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Belum Diatur</span>
                                @endif
                            </td>
                            
                            <!-- Aksi -->
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                <div class="flex items-center justify-center space-x-2">
                                    <button type="button" class="save-limit-btn text-green-600 hover:text-green-900 focus:outline-none" 
                                            data-dompet-id="{{ $item->id }}" style="display: none;">
                                        <span class="material-icons-outlined">save</span>
                                    </button>
                                    @if($item->dompetLimit)
                                        <button type="button" class="toggle-status-btn text-blue-600 hover:text-blue-900 focus:outline-none" 
                                                data-limit-id="{{ $item->dompetLimit->id }}"
                                                data-current-status="{{ $item->dompetLimit->is_active }}"
                                                title="Toggle Status">
                                            @if($item->dompetLimit->is_active)
                                                <span class="material-icons-outlined">toggle_on</span>
                                            @else
                                                <span class="material-icons-outlined">toggle_off</span>
                                            @endif
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="material-icons-outlined text-4xl text-gray-300 mb-2">account_balance_wallet</span>
                                    <h3 class="text-lg font-medium text-gray-700 mb-1">Tidak ada data</h3>
                                    <p class="text-sm text-gray-500">Belum ada dompet santri yang terdaftar atau sesuai dengan filter yang dipilih</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($dompets->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <p class="text-sm text-gray-700">
                        Menampilkan <span class="font-medium">{{ $dompets->firstItem() }}</span> hingga 
                        <span class="font-medium">{{ $dompets->lastItem() }}</span> dari 
                        <span class="font-medium">{{ $dompets->total() }}</span> santri
                    </p>
                </div>
                <div>
                    {{ $dompets->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Loading Modal -->
<div class="fixed inset-0 z-50 overflow-y-auto hidden" id="loadingModal" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full">
            <div class="p-6 text-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
                <div class="text-sm text-gray-600">Menyimpan perubahan...</div>
            </div>
        </div>
    </div>
</div>

<!-- Add custom CSS for changed inputs -->
<style>
.limit-input.changed {
    border-color: #f59e0b;
    background-color: #fffbeb;
    box-shadow: 0 0 0 1px #f59e0b;
}
</style>

<!-- Bulk Edit Modal -->
<div id="bulkEditModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <!-- Modal Header -->
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Bulk Edit Limit Harian Dompet</h3>
            <button onclick="hideBulkEditModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="mt-4">
            <!-- Filter Section -->
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <h4 class="font-medium text-gray-900 mb-3">Filter Santri</h4>
                
                <!-- Pilih Semua Checkbox -->
                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" id="selectAllSantri" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm font-medium text-blue-800">✓ Pilih Semua Santri (Abaikan Filter di Bawah)</span>
                    </label>
                    <p class="text-xs text-blue-600 mt-1 ml-6">Centang untuk mengubah limit semua santri sekaligus</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Filter by Kelas -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                        <select id="bulkKelas" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="">Semua Kelas</option>
                        </select>
                    </div>

                    <!-- Filter by Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status Limit</label>
                        <select id="bulkStatus" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="">Semua Status</option>
                            <option value="active">Aktif</option>
                            <option value="inactive">Belum Diatur</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- New Limit Input -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Limit Harian Baru</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                    <input type="number" 
                           id="bulkNewLimit"
                           min="0" 
                           step="1000"
                           placeholder="0"
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <!-- Quick Limit Buttons -->
                <div class="mt-3 flex flex-wrap gap-2">
                    <button type="button" onclick="document.getElementById('bulkNewLimit').value = 50000" 
                            class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition-colors">
                        Rp 50.000
                    </button>
                    <button type="button" onclick="document.getElementById('bulkNewLimit').value = 100000" 
                            class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition-colors">
                        Rp 100.000
                    </button>
                    <button type="button" onclick="document.getElementById('bulkNewLimit').value = 150000" 
                            class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition-colors">
                        Rp 150.000
                    </button>
                    <button type="button" onclick="document.getElementById('bulkNewLimit').value = 200000" 
                            class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition-colors">
                        Rp 200.000
                    </button>
                    <button type="button" onclick="document.getElementById('bulkNewLimit').value = 500000" 
                            class="px-3 py-1 text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-md transition-colors">
                        Rp 500.000
                    </button>
                </div>
                
                <p class="text-xs text-gray-500 mt-2">Masukkan limit harian dalam rupiah (tanpa titik/koma) atau pilih dari tombol di atas</p>
            </div>

            <!-- Preview Section -->
            <div class="mb-4">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="font-medium text-gray-900">Preview Santri yang Akan Diubah</h4>
                    <span id="previewCount" class="text-sm text-gray-500">0 santri</span>
                </div>
                
                <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-md">
                    <div id="previewContainer">
                        <div class="p-4 text-center text-gray-500">Pilih filter untuk melihat preview</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex justify-end space-x-3 pt-4 border-t">
            <button onclick="hideBulkEditModal()" 
                    class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md transition-colors">
                Batal
            </button>
            <button id="updateButton" onclick="processBulkEdit()" 
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                Update Santri
            </button>
        </div>
    </div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
