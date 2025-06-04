@extends('layouts.app')

@section('title', 'Tetapkan Pembayaran Santri')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tetapkan Pembayaran Santri</h3>
                    <div class="card-tools">
                        <a href="{{ route('pembayaran-santri-management.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Step 1: Select Jenis Pembayaran -->
                    <form method="GET" id="selectJenisPembayaranForm">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="jenis_pembayaran_id" class="form-label">Pilih Jenis Pembayaran <span class="text-danger">*</span></label>
                                <select name="jenis_pembayaran_id" id="jenis_pembayaran_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">-- Pilih Jenis Pembayaran --</option>
                                    @foreach($jenisTagihans as $jp)
                                        <option value="{{ $jp->id }}" {{ request('jenis_pembayaran_id') == $jp->id ? 'selected' : '' }}>
                                            {{ $jp->nama }} 
                                            ({{ ucfirst($jp->kategori_pembayaran) }}{{ $jp->is_bulanan ? ' - Bulanan' : '' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if($selectedJenisPembayaran)
                                <div class="col-md-6">
                                    <div class="alert alert-info">
                                        <strong>{{ $selectedJenisPembayaran->nama }}</strong><br>
                                        <small>
                                            Kategori: {{ ucfirst($selectedJenisPembayaran->kategori_pembayaran) }}<br>
                                            @if($selectedJenisPembayaran->is_bulanan)
                                                Pembayaran Bulanan<br>
                                            @endif
                                            Tipe: {{ ucfirst($selectedJenisPembayaran->tipe_pembayaran) }}<br>
                                            @if($selectedJenisPembayaran->tipe_pembayaran === 'kelas')
                                                Mode: {{ ucfirst($selectedJenisPembayaran->mode_santri) }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </form>
                    
                    @if($selectedJenisPembayaran && $santris->count() > 0)
                        <!-- Step 2: Assign to Students -->
                        <form action="{{ route('pembayaran-santri-management.store') }}" method="POST" id="assignmentForm">
                            @csrf
                            <input type="hidden" name="jenis_pembayaran_id" value="{{ $selectedJenisPembayaran->id }}">
                            
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5>Daftar Santri ({{ $santris->count() }} santri)</h5>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-info" onclick="setAllNominal()">
                                                <i class="fas fa-calculator"></i> Set Nominal Semua
                                            </button>
                                            <button type="button" class="btn btn-sm btn-success" onclick="selectAll()">
                                                <i class="fas fa-check-square"></i> Pilih Semua
                                            </button>
                                            <button type="button" class="btn btn-sm btn-warning" onclick="unselectAll()">
                                                <i class="fas fa-square"></i> Batal Pilih
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="5%">
                                                <input type="checkbox" id="selectAllCheckbox" onchange="toggleAll()">
                                            </th>
                                            <th>Santri</th>
                                            <th>Kelas</th>
                                            <th width="20%">Nominal Tagihan</th>
                                            @if($selectedJenisPembayaran->is_bulanan)
                                                <th width="15%">Bulan</th>
                                            @endif
                                            <th width="25%">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($santris as $index => $santri)
                                            <tr class="santri-row">
                                                <td>
                                                    <input type="checkbox" name="selected_santris[]" value="{{ $santri->id }}" 
                                                           class="santri-checkbox" onchange="toggleRow(this)">
                                                </td>
                                                <td>
                                                    <strong>{{ $santri->nama_santri }}</strong><br>
                                                    <small class="text-muted">NIS: {{ $santri->nis }}</small>
                                                </td>
                                                <td>
                                                    @if($santri->kelasAnggota)
                                                        {{ $santri->kelasAnggota->kelas->nama_kelas }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="hidden" name="assignments[{{ $index }}][santri_id]" value="{{ $santri->id }}">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Rp</span>
                                                        </div>
                                                        <input type="number" 
                                                               name="assignments[{{ $index }}][nominal_tagihan]" 
                                                               class="form-control nominal-input" 
                                                               value="{{ $selectedJenisPembayaran->nominal ?? 0 }}" 
                                                               min="0" 
                                                               step="1000"
                                                               disabled>
                                                    </div>
                                                </td>
                                                @if($selectedJenisPembayaran->is_bulanan)
                                                    <td>
                                                        <select name="assignments[{{ $index }}][bulan_pembayaran][]" 
                                                                class="form-control" multiple disabled>
                                                            @foreach($selectedJenisPembayaran->bulan_pembayaran_list as $bulan)
                                                                <option value="{{ $bulan }}" selected>
                                                                    {{ DateTime::createFromFormat('!m', $bulan)->format('F') }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <small class="text-muted">Bulan pembayaran</small>
                                                    </td>
                                                @endif
                                                <td>
                                                    <textarea name="assignments[{{ $index }}][keterangan]" 
                                                              class="form-control" 
                                                              rows="2" 
                                                              placeholder="Keterangan (opsional)" 
                                                              disabled></textarea>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <span id="selectedCount">0</span> santri dipilih
                                        </div>
                                        <div>
                                            <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                                <i class="fas fa-save"></i> Simpan Penetapan Pembayaran
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                    @elseif($selectedJenisPembayaran && $santris->count() == 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Tidak ada santri yang tersedia</strong><br>
                            Semua santri untuk jenis pembayaran ini sudah memiliki penetapan pembayaran, atau tidak ada santri yang sesuai dengan konfigurasi jenis pembayaran.
                        </div>
                        
                        <!-- Bulk Assign Option -->
                        @if($selectedJenisPembayaran->tipe_pembayaran === 'kelas')
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title">Penetapan Otomatis (Bulk Assign)</h5>
                                </div>
                                <div class="card-body">
                                    <p>Anda dapat melakukan penetapan otomatis berdasarkan konfigurasi jenis pembayaran ini.</p>
                                    
                                    <form action="{{ route('pembayaran-santri-management.bulk-assign') }}" method="POST" 
                                          onsubmit="return confirm('Yakin ingin melakukan penetapan otomatis untuk semua santri yang sesuai?')">
                                        @csrf
                                        <input type="hidden" name="jenis_pembayaran_id" value="{{ $selectedJenisPembayaran->id }}">
                                        
                                        <div class="form-check mb-3">
                                            <input type="checkbox" name="use_default_nominal" value="1" class="form-check-input" id="useDefaultNominal" checked>
                                            <label class="form-check-label" for="useDefaultNominal">
                                                Gunakan nominal default (Rp {{ number_format($selectedJenisPembayaran->nominal ?? 0, 0, ',', '.') }})
                                            </label>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-magic"></i> Lakukan Penetapan Otomatis
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                        
                    @elseif(!$selectedJenisPembayaran)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Silakan pilih jenis pembayaran terlebih dahulu untuk melihat daftar santri yang dapat ditetapkan.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Set Nominal Semua -->
<div class="modal fade" id="setNominalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set Nominal untuk Semua Santri</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="bulkNominal">Nominal Tagihan</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input type="number" id="bulkNominal" class="form-control" 
                               value="{{ $selectedJenisPembayaran->nominal ?? 0 }}" 
                               min="0" step="1000">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="applyBulkNominal()">Terapkan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleRow(checkbox) {
    const row = checkbox.closest('tr');
    const inputs = row.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        if (input !== checkbox) {
            input.disabled = !checkbox.checked;
        }
    });
    
    updateSelectedCount();
    updateSubmitButton();
}

function toggleAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const santriCheckboxes = document.querySelectorAll('.santri-checkbox');
    
    santriCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
        toggleRow(checkbox);
    });
}

function selectAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const santriCheckboxes = document.querySelectorAll('.santri-checkbox');
    
    selectAllCheckbox.checked = true;
    santriCheckboxes.forEach(checkbox => {
        checkbox.checked = true;
        toggleRow(checkbox);
    });
}

function unselectAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const santriCheckboxes = document.querySelectorAll('.santri-checkbox');
    
    selectAllCheckbox.checked = false;
    santriCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
        toggleRow(checkbox);
    });
}

function updateSelectedCount() {
    const checkedBoxes = document.querySelectorAll('.santri-checkbox:checked');
    document.getElementById('selectedCount').textContent = checkedBoxes.length;
}

function updateSubmitButton() {
    const checkedBoxes = document.querySelectorAll('.santri-checkbox:checked');
    const submitBtn = document.getElementById('submitBtn');
    
    if (submitBtn) {
        submitBtn.disabled = checkedBoxes.length === 0;
    }
}

function setAllNominal() {
    $('#setNominalModal').modal('show');
}

function applyBulkNominal() {
    const bulkNominal = document.getElementById('bulkNominal').value;
    const nominalInputs = document.querySelectorAll('.nominal-input');
    
    nominalInputs.forEach(input => {
        if (!input.disabled) {
            input.value = bulkNominal;
        }
    });
    
    $('#setNominalModal').modal('hide');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount();
    updateSubmitButton();
});
</script>
@endpush

<h1 class="text-3xl font-bold text-gray-800 flex items-center">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zM14 6a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h8zM6 10a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2H8a2 2 0 01-2-2v-2z" clip-rule="evenodd" />
    </svg>
    Tambah Tagihan Santri
</h1>
<p class="text-sm text-gray-500 mt-1">Formulir untuk menambah tagihan santri berdasarkan jenis tagihan.</p>
<label for="jenis_tagihan_id" class="block text-sm font-medium text-gray-700">Jenis Tagihan</label>
<select id="jenis_tagihan_id" name="jenis_tagihan_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
    @foreach($jenisTagihans as $jenisTagihan)
        <option value="{{ $jenisTagihan->id }}" {{ old('jenis_tagihan_id') == $jenisTagihan->id ? 'selected' : '' }}>{{ $jenisTagihan->nama }}</option>
    @endforeach
</select>
</File>