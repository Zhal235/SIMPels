@extends('layouts.admin')

@section('title', 'Set Limit Dompet')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-credit-card mr-2"></i>
                        Pengaturan Limit Dompet
                    </h3>
                    <a href="{{ route('dompet.set-limit.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>
                        Tambah Limit Baru
                    </a>
                </div>

                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="jenis_pemilik">Jenis Pemilik</label>
                                    <select name="jenis_pemilik" id="jenis_pemilik" class="form-control">
                                        <option value="">Semua</option>
                                        <option value="santri" {{ request('jenis_pemilik') == 'santri' ? 'selected' : '' }}>Santri</option>
                                        <option value="asatidz" {{ request('jenis_pemilik') == 'asatidz' ? 'selected' : '' }}>Asatidz</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Semua</option>
                                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="search">Cari</label>
                                    <input type="text" name="search" id="search" class="form-control" 
                                           placeholder="Nama pemilik atau nomor dompet..." 
                                           value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="d-flex">
                                        <button type="submit" class="btn btn-info mr-2">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <a href="{{ route('dompet.set-limit.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Alert Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nomor Dompet</th>
                                    <th>Pemilik</th>
                                    <th>Jenis</th>
                                    <th>Limit Harian</th>
                                    <th>Limit Transaksi</th>
                                    <th>Status</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dompetLimits as $key => $limit)
                                    <tr>
                                        <td>{{ $dompetLimits->firstItem() + $key }}</td>
                                        <td>
                                            <strong>{{ $limit->dompet->nomor_dompet }}</strong>
                                        </td>
                                        <td>
                                            @if($limit->dompet->jenis_pemilik == 'santri')
                                                <div>
                                                    <strong>{{ $limit->dompet->santri->nama_santri ?? 'N/A' }}</strong><br>
                                                    <small class="text-muted">NIS: {{ $limit->dompet->santri->nis ?? 'N/A' }}</small>
                                                </div>
                                            @else
                                                <div>
                                                    <strong>{{ $limit->dompet->asatidz->name ?? 'N/A' }}</strong><br>
                                                    <small class="text-muted">Asatidz</small>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $limit->dompet->jenis_pemilik == 'santri' ? 'info' : 'warning' }}">
                                                {{ ucfirst($limit->dompet->jenis_pemilik) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-success font-weight-bold">
                                                Rp {{ $limit->formatted_limit_harian }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-primary font-weight-bold">
                                                Rp {{ $limit->formatted_limit_transaksi }}
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-toggle-status 
                                                {{ $limit->is_active ? 'btn-success' : 'btn-danger' }}"
                                                data-id="{{ $limit->id }}"
                                                data-status="{{ $limit->is_active }}">
                                                <i class="fas fa-{{ $limit->is_active ? 'check' : 'times' }}"></i>
                                                {{ $limit->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </button>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('dompet.set-limit.show', $limit) }}" 
                                                   class="btn btn-sm btn-info" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('dompet.set-limit.edit', $limit) }}" 
                                                   class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger btn-delete" 
                                                        data-id="{{ $limit->id }}" 
                                                        data-nama="{{ $limit->dompet->nama_pemilik }}" 
                                                        title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="py-4">
                                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">Belum ada pengaturan limit</h5>
                                                <p class="text-muted">Silakan tambah pengaturan limit baru</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    {{ $dompetLimits->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus pengaturan limit untuk <strong id="delete-nama"></strong>?</p>
                <small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form id="delete-form" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Delete confirmation
    $('.btn-delete').click(function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');
        
        $('#delete-nama').text(nama);
        $('#delete-form').attr('action', `{{ route('dompet.set-limit.index') }}/${id}`);
        $('#deleteModal').modal('show');
    });

    // Toggle status
    $('.btn-toggle-status').click(function() {
        const button = $(this);
        const id = button.data('id');
        const currentStatus = button.data('status');
        
        $.ajax({
            url: `{{ route('dompet.set-limit.index') }}/${id}/toggle-status`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            beforeSend: function() {
                button.prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    // Update button appearance
                    if (response.is_active) {
                        button.removeClass('btn-danger').addClass('btn-success');
                        button.html('<i class="fas fa-check"></i> Aktif');
                    } else {
                        button.removeClass('btn-success').addClass('btn-danger');
                        button.html('<i class="fas fa-times"></i> Nonaktif');
                    }
                    button.data('status', response.is_active);
                    
                    // Show success message
                    toastr.success(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Terjadi kesalahan saat mengubah status');
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });
});
</script>
@endpush
