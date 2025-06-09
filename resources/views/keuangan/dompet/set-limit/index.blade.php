@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Set Limit Dompet Santri</h1>
            <p class="text-gray-600 mt-2">Atur batas transaksi harian untuk aplikasi EPOS khusus santri</p>
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let changedFields = {};
    
    // Detect changes in limit inputs
    document.querySelectorAll('.limit-input').forEach(input => {
        input.addEventListener('input', function() {
            const dompetId = this.getAttribute('data-dompet-id');
            const field = this.getAttribute('data-field');
            const value = this.value;
            
            // Mark as changed
            this.classList.add('changed');
            
            // Store changed value
            if (!changedFields[dompetId]) {
                changedFields[dompetId] = {};
            }
            changedFields[dompetId][field] = value;
            
            // Show save button
            const saveBtn = document.querySelector(`.save-limit-btn[data-dompet-id="${dompetId}"]`);
            if (saveBtn) {
                saveBtn.style.display = 'block';
            }
        });
    });
    
    // Save limit changes
    document.querySelectorAll('.save-limit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const dompetId = this.getAttribute('data-dompet-id');
            const data = changedFields[dompetId];
            
            if (!data) {
                return;
            }
            
            // Show loading modal
            document.getElementById('loadingModal').classList.remove('hidden');
            
            fetch(`{{ url('keuangan/dompet/set-limit') }}/${dompetId}/update-limit`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    limit_harian: data.limit_harian
                })
            })
            .then(response => response.json())
            .then(data => {
                // Hide loading modal
                document.getElementById('loadingModal').classList.add('hidden');
                
                if (data.success) {
                    // Remove changed styling
                    document.querySelectorAll(`.limit-input[data-dompet-id="${dompetId}"]`).forEach(input => {
                        input.classList.remove('changed');
                    });
                    
                    // Hide save button
                    const saveBtn = document.querySelector(`.save-limit-btn[data-dompet-id="${dompetId}"]`);
                    if (saveBtn) {
                        saveBtn.style.display = 'none';
                    }
                    
                    // Clear changed fields
                    delete changedFields[dompetId];
                    
                    // Update status if needed
                    const statusBadge = document.querySelector(`#row-${dompetId} .bg-gray-100`);
                    if (statusBadge) {
                        statusBadge.classList.remove('bg-gray-100', 'text-gray-800');
                        statusBadge.classList.add('bg-green-100', 'text-green-800');
                        statusBadge.textContent = 'Aktif';
                    }
                    
                    // Show success message
                    showAlert('success', data.message);
                } else {
                    showAlert('error', data.message || 'Terjadi kesalahan saat menyimpan data.');
                }
            })
            .catch(error => {
                document.getElementById('loadingModal').classList.add('hidden');
                showAlert('error', 'Terjadi kesalahan saat menyimpan data.');
                console.error('Error:', error);
            });
        });
    });
    
    // Toggle status limit
    document.querySelectorAll('.toggle-status-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const limitId = this.getAttribute('data-limit-id');
            const currentStatus = this.getAttribute('data-current-status') === '1';
            
            fetch(`{{ url('keuangan/dompet/set-limit') }}/${limitId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update button icon and data
                    const newStatus = data.is_active;
                    this.setAttribute('data-current-status', newStatus ? '1' : '0');
                    
                    const icon = this.querySelector('.material-icons-outlined');
                    if (newStatus) {
                        icon.textContent = 'toggle_on';
                        // Update status badge
                        const row = this.closest('tr');
                        const statusBadge = row.querySelector('.bg-gray-100, .bg-green-100');
                        if (statusBadge) {
                            statusBadge.classList.remove('bg-gray-100', 'text-gray-800');
                            statusBadge.classList.add('bg-green-100', 'text-green-800');
                            statusBadge.textContent = 'Aktif';
                        }
                    } else {
                        icon.textContent = 'toggle_off';
                        // Update status badge
                        const row = this.closest('tr');
                        const statusBadge = row.querySelector('.bg-green-100, .bg-gray-100');
                        if (statusBadge) {
                            statusBadge.classList.remove('bg-green-100', 'text-green-800');
                            statusBadge.classList.add('bg-gray-100', 'text-gray-800');
                            statusBadge.textContent = 'Belum Diatur';
                        }
                    }
                    
                    showAlert('success', data.message);
                } else {
                    showAlert('error', data.message || 'Terjadi kesalahan saat mengubah status.');
                }
            })
            .catch(error => {
                showAlert('error', 'Terjadi kesalahan saat mengubah status.');
                console.error('Error:', error);
            });
        });
    });
    
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
});
</script>
@endpush
