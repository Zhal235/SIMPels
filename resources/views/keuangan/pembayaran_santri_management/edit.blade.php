@extends('layouts.app')

@section('title', 'Edit Pembayaran Santri')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Pembayaran Santri</h3>
                    <div class="card-tools">
                        <a href="{{ route('pembayaran-santri-management.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Informasi Santri</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="30%">Nama Santri</th>
                                            <td>: <strong>{{ $pembayaranSantri->santri->nama_santri }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>NIS</th>
                                            <td>: {{ $pembayaranSantri->santri->nis }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kelas</th>
                                            <td>: {{ $pembayaranSantri->santri->kelasAnggota->kelas->nama_kelas ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Informasi Pembayaran</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="30%">Jenis Pembayaran</th>
                                            <td>: <strong>{{ $pembayaranSantri->jenisPembayaran->nama }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Kategori</th>
                                            <td>: {{ ucfirst($pembayaranSantri->jenisPembayaran->kategori_pembayaran) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tahun Ajaran</th>
                                            <td>: {{ $pembayaranSantri->tahunAjaran->nama }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status Pembayaran</th>
                                            <td>: 
                                                @if($pembayaranSantri->status_pembayaran == 'lunas')
                                                    <span class="badge badge-success">Lunas</span>
                                                @elseif($pembayaranSantri->status_pembayaran == 'sebagian')
                                                    <span class="badge badge-warning">Sebagian ({{ number_format($pembayaranSantri->persentase_pembayaran, 1) }}%)</span>
                                                @else
                                                    <span class="badge badge-danger">Belum Bayar</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <form action="{{ route('pembayaran-santri-management.update', $pembayaranSantri->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nominal_tagihan">Nominal Tagihan <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="number" 
                                               name="nominal_tagihan" 
                                               id="nominal_tagihan" 
                                               class="form-control @error('nominal_tagihan') is-invalid @enderror" 
                                               value="{{ old('nominal_tagihan', $pembayaranSantri->nominal_tagihan) }}" 
                                               min="0" 
                                               step="1000" 
                                               required>
                                    </div>
                                    @error('nominal_tagihan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    @if($pembayaranSantri->nominal_dibayar > 0)
                                        <small class="text-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Perhatian: Santri sudah membayar sebesar Rp {{ number_format($pembayaranSantri->nominal_dibayar, 0, ',', '.') }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="aktif" {{ old('status', $pembayaranSantri->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="nonaktif" {{ old('status', $pembayaranSantri->status) == 'nonaktif' ? 'selected' : '' }}>Non-aktif</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        @if($pembayaranSantri->jenisPembayaran->is_bulanan)
                            <div class="form-group">
                                <label for="bulan_pembayaran">Bulan Pembayaran</label>
                                <select name="bulan_pembayaran[]" id="bulan_pembayaran" class="form-control @error('bulan_pembayaran') is-invalid @enderror" multiple>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ in_array($i, $pembayaranSantri->bulan_pembayaran ?? []) ? 'selected' : '' }}>
                                            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                                @error('bulan_pembayaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Tahan tombol Ctrl untuk memilih beberapa bulan</small>
                            </div>
                        @endif
                        
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3">{{ old('keterangan', $pembayaranSantri->keterangan) }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection