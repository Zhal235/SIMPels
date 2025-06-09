@extends('layouts.admin')

@section('title', 'Tambah Transaksi Kas')

@section('content')
<div class="container-fluid px-6 py-4">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">
            @if($jenis === 'pemasukan')
                Tambah Pemasukan Kas
            @elseif($jenis === 'pengeluaran')
                Tambah Pengeluaran Kas
            @else
                Tambah Transfer Kas
            @endif
        </h2>
        <p class="text-slate-600 mt-1">
            @if($jenis === 'pemasukan')
                Catat transaksi pemasukan kas baru
            @elseif($jenis === 'pengeluaran')
                Catat transaksi pengeluaran kas baru
            @else
                Catat transfer antar buku kas
            @endif
        </p>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <form action="{{ route('keuangan.transaksi-kas.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
            
            <input type="hidden" name="jenis_transaksi" value="{{ $jenis }}">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Buku Kas Sumber -->
                <div>
                    <label for="buku_kas_id" class="block mb-1 text-sm font-medium text-slate-700">
                        @if($jenis === 'transfer')
                            Buku Kas Sumber
                        @else
                            Buku Kas
                        @endif
                        <span class="text-red-500">*</span>
                    </label>
                    <select id="buku_kas_id" name="buku_kas_id" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        <option value="">Pilih Buku Kas</option>
                        @foreach($bukuKasList as $bukuKas)
                        <option value="{{ $bukuKas->id }}" data-saldo="{{ $bukuKas->saldo_saat_ini }}">
                            {{ $bukuKas->nama_kas }} - Rp {{ number_format($bukuKas->saldo_saat_ini, 0, ',', '.') }}
                        </option>
                        @endforeach
                    </select>
                    @error('buku_kas_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    
                    <div id="saldo_info" class="mt-1 text-xs text-slate-500"></div>
                </div>
                
                <!-- Buku Kas Tujuan (for transfer only) -->
                @if($jenis === 'transfer')
                <div>
                    <label for="buku_kas_tujuan_id" class="block mb-1 text-sm font-medium text-slate-700">
                        Buku Kas Tujuan
                        <span class="text-red-500">*</span>
                    </label>
                    <select id="buku_kas_tujuan_id" name="buku_kas_tujuan_id" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        <option value="">Pilih Buku Kas Tujuan</option>
                        @foreach($bukuKasList as $bukuKas)
                        <option value="{{ $bukuKas->id }}">
                            {{ $bukuKas->nama_kas }} - Rp {{ number_format($bukuKas->saldo_saat_ini, 0, ',', '.') }}
                        </option>
                        @endforeach
                    </select>
                    @error('buku_kas_tujuan_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                @endif
                
                <!-- Kategori - Input hidden untuk menyimpan nilai kategori yang dipilih -->
                <div class="hidden">
                    <input type="hidden" id="kategori" name="kategori" value="">
                    <input type="hidden" id="selected_category" value="">
                    @error('kategori')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Kategori yang dipilih -->
                <div class="{{ $jenis !== 'transfer' ? 'md:col-span-2' : '' }}">
                    <label class="block mb-1 text-sm font-medium text-slate-700">
                        Kategori Terpilih
                        <span class="text-red-500">*</span>
                    </label>
                    <div id="selected-category-display" class="p-3 border border-slate-300 rounded-md bg-slate-50">
                        <p class="text-slate-500 text-sm">Belum ada kategori dipilih. Silakan pilih dari panel Kelola Kategori.</p>
                    </div>
                </div>
                
                <!-- Jumlah -->
                <div class="md:col-span-2">
                    <label for="jumlah_display" class="block mb-1 text-sm font-medium text-slate-700">
                        Jumlah (Rp)
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <span class="text-slate-500">Rp</span>
                        </div>
                        <input type="text" id="jumlah_display" 
                               class="block w-full pl-12 pr-12 rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               placeholder="0" 
                               value="{{ old('jumlah') }}" 
                               oninput="formatCurrency(this)" 
                               required>
                        <input type="hidden" id="jumlah" name="jumlah" value="{{ old('jumlah') }}">
                    </div>
                    @error('jumlah')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Tanggal Transaksi -->
                <div>
                    <label for="tanggal_transaksi" class="block mb-1 text-sm font-medium text-slate-700">
                        Tanggal Transaksi
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="tanggal_transaksi" name="tanggal_transaksi" 
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                           value="{{ old('tanggal_transaksi', now()->format('Y-m-d')) }}" 
                           required>
                    @error('tanggal_transaksi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Metode Pembayaran -->
                <div>
                    <label for="metode_pembayaran" class="block mb-1 text-sm font-medium text-slate-700">
                        Metode Transaksi
                        <span class="text-red-500">*</span>
                    </label>
                    <select id="metode_pembayaran" name="metode_pembayaran" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        <option value="">Pilih Metode</option>
                        <option value="Tunai">Tunai</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="QRIS">QRIS</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                    @error('metode_pembayaran')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- No Referensi -->
                <div>
                    <label for="no_referensi" class="block mb-1 text-sm font-medium text-slate-700">
                        No. Referensi 
                        <span class="text-slate-500 text-xs font-normal">(Opsional)</span>
                    </label>
                    <input type="text" id="no_referensi" name="no_referensi" 
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                           placeholder="Nomor cek, transfer, dll" 
                           value="{{ old('no_referensi') }}">
                    @error('no_referensi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                @if($jenis === 'pengeluaran')
                <!-- Nama Pemohon -->
                <div>
                    <label for="nama_pemohon" class="block mb-1 text-sm font-medium text-slate-700">
                        Nama Pemohon
                        <span class="text-slate-500 text-xs font-normal">(Opsional)</span>
                    </label>
                    <input type="text" id="nama_pemohon" name="nama_pemohon" 
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                           placeholder="Nama orang yang mengajukan pengeluaran" 
                           value="{{ old('nama_pemohon') }}">
                    @error('nama_pemohon')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                @endif
                
                <!-- Bukti Transaksi -->
                <div class="md:col-span-2">
                    <label for="bukti_transaksi" class="block mb-1 text-sm font-medium text-slate-700">
                        Bukti Transaksi 
                        <span class="text-slate-500 text-xs font-normal">(Opsional)</span>
                    </label>
                    <input type="file" id="bukti_transaksi" name="bukti_transaksi" 
                           class="block w-full text-sm text-slate-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-md file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-blue-50 file:text-blue-700
                                  hover:file:bg-blue-100"
                           accept="image/jpeg,image/png,image/jpg,application/pdf">
                    <p class="mt-1 text-xs text-slate-500">Upload file JPG, PNG atau PDF (max 2MB)</p>
                    @error('bukti_transaksi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Keterangan -->
                <div class="md:col-span-2">
                    <label for="keterangan" class="block mb-1 text-sm font-medium text-slate-700">
                        Keterangan 
                        <span class="text-slate-500 text-xs font-normal">(Opsional)</span>
                    </label>
                    <textarea id="keterangan" name="keterangan" rows="3" 
                              class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                              placeholder="Rincian atau catatan tambahan...">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('keuangan.transaksi-kas.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-md transition">
                    Batal
                </a>
                <button type="submit" id="submitBtn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
                    Simpan Transaksi
                </button>
            </div>
                </form>
                
                <!-- Modal untuk peringatan kategori tidak dipilih -->
                <div id="kategori-warning-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                    <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
                        <div class="flex items-center space-x-2 text-amber-500 mb-4">
                            <span class="material-icons-outlined">warning</span>
                            <h3 class="text-lg font-medium">Kategori Belum Dipilih</h3>
                        </div>
                        <p class="text-slate-600 mb-6">Silahkan pilih kategori terlebih dahulu dari panel Kelola Kategori.</p>
                        <div class="flex justify-end">
                            <button type="button" onclick="closeKategoriWarning()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-md transition">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Category Management Panel on the right -->
            <div class="lg:col-span-1">
                <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                    <h3 class="text-lg font-semibold mb-4 pb-2 border-b border-slate-200 flex items-center justify-between">
                        <span>Kelola Kategori</span>
                        <button type="button" id="btn-add-category" class="text-blue-600 hover:text-blue-800 flex items-center gap-1 text-sm">
                            <span class="material-icons-outlined text-sm">add_circle</span>
                            Tambah
                        </button>
                    </h3>
                    
                    <!-- Add/Edit Category Form -->
                    <form id="categoryForm" class="mb-4 hidden border-b border-slate-200 pb-4">
                        <input type="hidden" id="category_id" value="">
                        <div class="mb-3">
                            <label for="nama_kategori" class="block mb-1 text-sm font-medium text-slate-700">Nama Kategori <span class="text-red-500">*</span></label>
                            <input type="text" id="nama_kategori" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Nama kategori" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="block mb-1 text-sm font-medium text-slate-700">Deskripsi</label>
                            <textarea id="deskripsi" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" rows="2" placeholder="Deskripsi kategori"></textarea>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button" id="btn-cancel-category" class="px-3 py-1 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-md transition text-sm">
                                Batal
                            </button>
                            <button type="submit" id="btn-save-category" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition flex items-center gap-1 text-sm">
                                <span class="material-icons-outlined text-sm">save</span>
                                Simpan
                            </button>
                        </div>
                    </form>
                    
                    <!-- Categories List -->
                    <div id="categories-list" class="space-y-2 max-h-96 overflow-y-auto pr-1">
                        <div class="text-sm text-slate-600 mb-2">Pilih kategori dengan mengklik salah satu kategori di bawah ini:</div>
                        <div id="categories-container" class="space-y-2">
                            <p class="text-center text-sm text-slate-500 py-3">Memuat kategori...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Format currency on input
        const jumlahInput = document.getElementById('jumlah_display');
        
        // Category management
        initCategoryManagement();
        loadCategories();
        
        // Form validation
        const mainForm = document.querySelector('form[action*="transaksi-kas"]');
        
        if (mainForm) {
            mainForm.addEventListener('submit', function(e) {
                const selectedCategory = document.getElementById('selected_category').value;
                
                if (!selectedCategory) {
                    e.preventDefault();
                    document.getElementById('kategori-warning-modal').classList.remove('hidden');
                    return false;
                }
                
                // Pastikan kategori juga diset di input hidden yang asli
                const kategoriInput = document.getElementById('kategori');
                if (kategoriInput) {
                    kategoriInput.value = selectedCategory;
                }
            });
        }
        
        if (jumlahInput) {
            jumlahInput.addEventListener('input', function() {
                formatCurrency(this);
            });
        }
        
        // Handle buku kas selection
        const bukuKasSelect = document.getElementById('buku_kas_id');
        const bukuKasTujuanSelect = document.getElementById('buku_kas_tujuan_id');
        const saldoInfo = document.getElementById('saldo_info');
        const jenisTransaksi = '{{ $jenis }}';
        
        if (bukuKasSelect) {
            bukuKasSelect.addEventListener('change', function() {
                updateSaldoInfo();
                
                if (bukuKasTujuanSelect) {
                    // Disable the same option in destination dropdown
                    const selectedValue = this.value;
                    
                    Array.from(bukuKasTujuanSelect.options).forEach(option => {
                        option.disabled = (option.value === selectedValue && option.value !== '');
                    });
                }
            });
        }
        
        if (bukuKasTujuanSelect) {
            bukuKasTujuanSelect.addEventListener('change', function() {
                const selectedValue = this.value;
                
                // Disable the same option in source dropdown
                if (bukuKasSelect) {
                    Array.from(bukuKasSelect.options).forEach(option => {
                        option.disabled = (option.value === selectedValue && option.value !== '');
                    });
                }
            });
        }
        
        function updateSaldoInfo() {
            if (!bukuKasSelect || !saldoInfo) return;
            
            const selectedOption = bukuKasSelect.options[bukuKasSelect.selectedIndex];
            if (selectedOption && selectedOption.value !== '') {
                const saldo = selectedOption.getAttribute('data-saldo');
                if (saldo) {
                    const formattedSaldo = new Intl.NumberFormat('id-ID').format(saldo);
                    
                    if (jenisTransaksi === 'pengeluaran' || jenisTransaksi === 'transfer') {
                        saldoInfo.textContent = `Saldo tersedia: Rp ${formattedSaldo}`;
                        saldoInfo.classList.add('text-blue-600');
                    } else {
                        saldoInfo.textContent = '';
                    }
                }
            } else {
                saldoInfo.textContent = '';
            }
        }
        
        // Initial update
        if (bukuKasSelect && bukuKasSelect.value) {
            updateSaldoInfo();
        }
    });
    
    function formatCurrency(input) {
        // Remove non-digits
        let value = input.value.replace(/\D/g, '');
        
        // Update hidden input with raw value
        const hiddenInput = document.getElementById('jumlah');
        if (hiddenInput) {
            hiddenInput.value = value;
        }
        
        // Format with thousand separator for display
        if (value !== '' && value !== '0') {
            // Format angka dengan pemisah ribuan
            const formattedValue = new Intl.NumberFormat('id-ID').format(parseInt(value));
            input.value = formattedValue;
        } else {
            input.value = '';
        }
    }
    
    // Category Management Functions
    function initCategoryManagement() {
        const addButton = document.getElementById('btn-add-category');
        const cancelButton = document.getElementById('btn-cancel-category');
        const categoryForm = document.getElementById('categoryForm');
        
        // Add category button click
        addButton.addEventListener('click', function() {
            resetCategoryForm();
            categoryForm.classList.remove('hidden');
        });
        
        // Cancel button click
        cancelButton.addEventListener('click', function() {
            categoryForm.classList.add('hidden');
        });
        
        // Form submission
        categoryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveCategoryData();
        });
    }
    
    function resetCategoryForm() {
        const categoryForm = document.getElementById('categoryForm');
        const categoryId = document.getElementById('category_id');
        const nameInput = document.getElementById('nama_kategori');
        const descInput = document.getElementById('deskripsi');
        
        categoryId.value = '';
        nameInput.value = '';
        descInput.value = '';
    }
    
    function loadCategories() {
        const categoriesList = document.getElementById('categories-list');
        const jenisTransaksi = '{{ $jenis }}';
        
        console.log('Loading categories for jenis:', jenisTransaksi);
        
        // AJAX request to fetch categories
        fetch(`/api/keuangan/categories?jenis=${jenisTransaksi}`)
            .then(response => response.json())
            .then(data => {
                console.log('API response:', data);
                if (data.success && data.data.length > 0) {
                    let html = '';
                    data.data.forEach(category => {
                        html += `
                        <div class="bg-white p-3 rounded border border-slate-200 shadow-sm hover:bg-slate-50 cursor-pointer" 
                             onclick="selectCategory('${category.nama_kategori}', ${category.id})">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium text-slate-800">${category.nama_kategori}</h4>
                                    <p class="text-xs text-slate-500">${category.deskripsi || 'Tidak ada deskripsi'}</p>
                                </div>
                                <div class="flex space-x-1">
                                    <button onclick="editCategory(${category.id}); event.stopPropagation();" class="text-blue-600 hover:text-blue-800" title="Edit">
                                        <span class="material-icons-outlined text-sm">edit</span>
                                    </button>
                                    <button onclick="deleteCategory(${category.id}); event.stopPropagation();" class="text-red-600 hover:text-red-800" title="Hapus">
                                        <span class="material-icons-outlined text-sm">delete</span>
                                    </button>
                                </div>
                            </div>
                        </div>`;
                    });
                    
                    const categoriesContainer = document.getElementById('categories-container') || categoriesList;
                    categoriesContainer.innerHTML = html;
                } else {
                    const categoriesContainer = document.getElementById('categories-container') || categoriesList;
                    categoriesContainer.innerHTML = `<p class="text-center text-sm text-slate-500 py-3">Belum ada kategori tersimpan</p>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                categoriesList.innerHTML = `<p class="text-center text-sm text-red-500 py-3">Gagal memuat data kategori</p>`;
            });
    }
    
    function saveCategoryData() {
        const categoryId = document.getElementById('category_id').value;
        const nameInput = document.getElementById('nama_kategori').value;
        const descInput = document.getElementById('deskripsi').value;
        const categoryForm = document.getElementById('categoryForm');
        const jenisTransaksi = '{{ $jenis }}';
        
        const url = categoryId ? `/api/keuangan/categories/${categoryId}` : '/api/keuangan/categories';
        const method = categoryId ? 'PUT' : 'POST';
        
        const data = {
            nama_kategori: nameInput,
            deskripsi: descInput,
            jenis_transaksi: jenisTransaksi
        };
        
        // CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // AJAX request to save/update category
        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Success message
                alert(categoryId ? 'Kategori berhasil diperbarui' : 'Kategori berhasil ditambahkan');
                categoryForm.classList.add('hidden');
                
                // Reload categories list
                loadCategories();
                
                // Update dropdown if needed
                updateCategoryDropdown();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan kategori');
        });
    }
    
    function editCategory(id) {
        fetch(`/api/keuangan/categories/${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const categoryForm = document.getElementById('categoryForm');
                    const categoryId = document.getElementById('category_id');
                    const nameInput = document.getElementById('nama_kategori');
                    const descInput = document.getElementById('deskripsi');
                    
                    categoryId.value = data.data.id;
                    nameInput.value = data.data.nama_kategori;
                    descInput.value = data.data.deskripsi || '';
                    
                    categoryForm.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal memuat data kategori');
            });
    }
    
    function deleteCategory(id) {
        if (confirm('Yakin ingin menghapus kategori ini?')) {
            // CSRF token
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch(`/api/keuangan/categories/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Kategori berhasil dihapus');
                    loadCategories();
                    updateCategoryDropdown();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal menghapus kategori');
            });
        }
    }
    
    function updateCategoryDropdown() {
        const kategoriSelect = document.getElementById('kategori');
        if (!kategoriSelect) return;
        
        // Store currently selected value
        const currentValue = kategoriSelect.value;
        
        // Get transaction type
        const jenisTransaksi = '{{ $jenis }}';
        
        fetch(`/api/keuangan/categories?jenis=${jenisTransaksi}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear existing options except the first placeholder
                    while (kategoriSelect.options.length > 1) {
                        kategoriSelect.remove(1);
                    }
                    
                    // Add new options
                    data.data.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.nama_kategori;
                        option.textContent = category.nama_kategori;
                        kategoriSelect.appendChild(option);
                    });
                    
                    // Restore selected value if possible
                    if (currentValue) {
                        kategoriSelect.value = currentValue;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
    
    function selectCategory(categoryName, categoryId) {
        // Set nilai kategori di kedua input
        document.getElementById('selected_category').value = categoryName;
        const kategoriInput = document.getElementById('kategori');
        if (kategoriInput) {
            kategoriInput.value = categoryName;
        }
        
        // Update tampilan kategori terpilih
        const selectedDisplay = document.getElementById('selected-category-display');
        selectedDisplay.innerHTML = `
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-medium text-slate-800">${categoryName}</p>
                </div>
                <div>
                    <button type="button" onclick="clearSelectedCategory()" class="text-slate-400 hover:text-slate-600" title="Hapus Pilihan">
                        <span class="material-icons-outlined text-sm">close</span>
                    </button>
                </div>
            </div>
        `;
        selectedDisplay.classList.remove('bg-slate-50');
        selectedDisplay.classList.add('bg-green-50', 'border-green-200');
        
        // Highlight kategori yang dipilih
        const categoryItems = document.querySelectorAll('#categories-container > div');
        categoryItems.forEach(item => {
            item.classList.remove('bg-green-50', 'border-green-300');
            item.classList.add('bg-white', 'border-slate-200');
        });
        
        // Cari dan highlight yang dipilih
        const selectedItem = Array.from(categoryItems).find(item => {
            return item.textContent.includes(categoryName);
        });
        
        if (selectedItem) {
            selectedItem.classList.remove('bg-white', 'border-slate-200');
            selectedItem.classList.add('bg-green-50', 'border-green-300');
        }
    }
    
    function clearSelectedCategory() {
        document.getElementById('selected_category').value = '';
        const kategoriInput = document.getElementById('kategori');
        if (kategoriInput) {
            kategoriInput.value = '';
        }
        
        const selectedDisplay = document.getElementById('selected-category-display');
        selectedDisplay.innerHTML = `<p class="text-slate-500 text-sm">Belum ada kategori dipilih. Silakan pilih dari panel Kelola Kategori.</p>`;
        selectedDisplay.classList.remove('bg-green-50', 'border-green-200');
        selectedDisplay.classList.add('bg-slate-50');
        
        // Reset highlight
        const categoryItems = document.querySelectorAll('#categories-container > div');
        categoryItems.forEach(item => {
            item.classList.remove('bg-green-50', 'border-green-300');
            item.classList.add('bg-white', 'border-slate-200');
        });
    }
    
    function closeKategoriWarning() {
        document.getElementById('kategori-warning-modal').classList.add('hidden');
    }
</script>
@endpush
