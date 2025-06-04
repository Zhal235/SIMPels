@extends('layouts.app')

@section('title', 'Kelola Pembayaran Santri')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Kelola Pembayaran Santri</h3>
                    <div>
                        <a href="{{ route('pembayaran-santri-management.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tetapkan Pembayaran
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="jenis_pembayaran_id" class="form-control">
                                    <option value="">Semua Jenis Pembayaran</option>
                                    @foreach($jenisTagihans as $jp)
                                        <option value="{{ $jp->id }}" {{ request('jenis_pembayaran_id') == $jp->id ? 'selected' : '' }}>
                                            {{ $jp->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="kelas_id" class="form-control">
                                    <option value="">Semua Kelas</option>
                                    @foreach($kelas as $k)
                                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Cari nama santri atau NIS..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-info btn-block">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    @if($pembayaranSantris->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Santri</th>
                                        <th>Kelas</th>
                                        <th>Jenis Tagihan</th>
                                        <th>Nominal Tagihan</th>
                                        <th>Nominal Dibayar</th>
                                        <th>Sisa Tagihan</th>
                                        <th>Status Bayar</th>
                                        <th>Bulan</th>
                                        <th>Status</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pembayaranSantris as $index => $ps)
                                        <tr>
                                            <td>{{ $pembayaranSantris->firstItem() + $index }}</td>
                                            <td>
                                                <strong>{{ $ps->santri->nama_santri }}</strong><br>
                                                <small class="text-muted">NIS: {{ $ps->santri->nis }}</small>
                                            </td>
                                            <td>
                                                @if($ps->santri->kelasAnggota)
                                                    {{ $ps->santri->kelasAnggota->kelas->nama_kelas }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $ps->jenisPembayaran->nama }}</strong><br>
                                                <small class="text-muted">
                                                    {{ ucfirst($ps->jenisPembayaran->kategori_pembayaran) }}
                                                    @if($ps->jenisPembayaran->is_bulanan)
                                                        - Bulanan
                                                    @endif
                                                </small>
                                            </td>
                                            <td class="text-right">
                                                <strong>Rp {{ number_format($ps->nominal_tagihan, 0, ',', '.') }}</strong>
                                            </td>
                                            <td class="text-right">
                                                Rp {{ number_format($ps->nominal_dibayar, 0, ',', '.') }}
                                            </td>
                                            <td class="text-right">
                                                <strong class="{{ $ps->sisa_tagihan > 0 ? 'text-danger' : 'text-success' }}">
                                                    Rp {{ number_format($ps->sisa_tagihan, 0, ',', '.') }}
                                                </strong>
                                            </td>
                                            <td>
                                                @if($ps->status_pembayaran == 'lunas')
                                                    <span class="badge badge-success">Lunas</span>
                                                @elseif($ps->status_pembayaran == 'sebagian')
                                                    <span class="badge badge-warning">Sebagian</span>
                                                @else
                                                    <span class="badge badge-danger">Belum Bayar</span>
                                                @endif
                                                
                                                @if($ps->persentase_pembayaran > 0)
                                                    <br><small>({{ number_format($ps->persentase_pembayaran, 1) }}%)</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($ps->bulan_pembayaran)
                                                    <small>{{ $ps->bulan_names }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($ps->status == 'aktif')
                                                    <span class="badge badge-success">Aktif</span>
                                                @else
                                                    <span class="badge badge-secondary">Non-aktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('pembayaran-santri-management.edit', $ps->id) }}" 
                                                       class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <form action="{{ route('pembayaran-santri-management.destroy', $ps->id) }}" 
                                                          method="POST" class="d-inline" 
                                                          onsubmit="return confirm('Yakin ingin menghapus data pembayaran ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Menampilkan {{ $pembayaranSantris->firstItem() }} - {{ $pembayaranSantris->lastItem() }} 
                                dari {{ $pembayaranSantris->total() }} data
                            </div>
                            <div>
                                {{ $pembayaranSantris->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada data pembayaran santri</h5>
                            <p class="text-muted">Klik tombol "Tetapkan Pembayaran" untuk mulai menetapkan pembayaran kepada santri</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
        @foreach($errors->all() as $error)
            {{ $error }}<br>
        @endforeach
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
@endif
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endpush

<h1 class="text-3xl font-bold text-gray-800 flex items-center">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zM14 6a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h8zM6 10a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2H8a2 2 0 01-2-2v-2z" clip-rule="evenodd" />
    </svg>
    Manajemen Tagihan Santri
</h1>
<p class="text-sm text-gray-500 mt-1">Kelola pembayaran tagihan santri berdasarkan jenis tagihan.</p>
@foreach ($pembayaranSantris as $pembayaranSantri)
    <tr>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $pembayaranSantri->santri->nama_lengkap }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $pembayaranSantri->jenisTagihan->nama }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $pembayaranSantri->tahunAjaran->nama_tahun_ajaran }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $pembayaranSantri->bulan }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($pembayaranSantri->nominal, 0, ',', '.') }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $pembayaranSantri->status }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            <a href="{{ route('pembayaran-santri-management.edit', $pembayaranSantri->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
            <form action="{{ route('pembayaran-santri-management.destroy', $pembayaranSantri->id) }}" method="POST" class="inline-block">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus data tagihan ini?')">Hapus</button>
            </form>
        </td>
    </tr>
@endforeach
// ... existing code ...