# SISTEM TUNGGAKAN SANTRI - DOKUMENTASI PENGEMBANGAN

## Ringkasan Peningkatan

Sistem tunggakan santri di SIMPelS telah berhasil ditingkatkan dengan fitur-fitur baru untuk mengatasi masalah:

1. **Tunggakan tahun ajaran sebelumnya tetap muncul di detail santri, namun default-nya disembunyikan dan bisa ditampilkan dengan tombol toggle.**
2. **Pada tahun ajaran baru, tagihan rutin tidak perlu diinput ulang (tagihan rutin berlaku dari awal masuk sampai keluar, kecuali ada kenaikan).**

## Fitur yang Telah Diimplementasi

### 1. Toggle Tunggakan Tahun Sebelumnya

**Lokasi**: `resources/views/keuangan/tunggakan/detail.blade.php`

**Fitur:**
- Menampilkan ringkasan total tunggakan tahun berjalan dan tahun sebelumnya
- Menampilkan tabel tagihan tahun berjalan secara default
- Tombol toggle (Alpine.js) untuk menampilkan/menyembunyikan tabel tagihan tahun-tahun sebelumnya
- Tabel tagihan tahun sebelumnya mencakup kolom tahun ajaran untuk identifikasi yang jelas

**Controller**: `app/Http/Controllers/TunggakanController.php`
- Method `detail()` mengambil tagihan tahun berjalan dan tahun sebelumnya secara terpisah
- Menghitung total tunggakan per periode

### 2. Otomatisasi Tagihan Rutin Lintas Tahun Ajaran

**Service**: `app/Services/TagihanService.php`

**Fitur:**
- `copyRoutineTagihanToNewYear()`: Menyalin tagihan rutin dari tahun ajaran sebelumnya ke tahun ajaran baru
- `generateTagihanForSantri()`: Generate tagihan untuk santri spesifik
- `determineNominalForSantri()`: Menentukan nominal berdasarkan kelas jika berlaku
- `getOutstandingTagihanSummary()`: Ringkasan tunggakan santri

**Command**: `app/Console/Commands/CopyRoutineTagihanToNewYear.php`

**Fitur:**
- Command line interface untuk menyalin tagihan rutin: `php artisan tagihan:copy-routine`
- Support parameter `--target-year-id`, `--source-year-id`, `--confirm`
- Validasi dan preview sebelum eksekusi

### 3. Interface Web untuk Manajemen Otomatisasi

**View**: `resources/views/keuangan/tunggakan/automation.blade.php`

**Fitur:**
- Dashboard untuk mengelola otomatisasi tagihan rutin
- Form untuk menyalin tagihan rutin antar tahun ajaran
- Preview sistem yang menampilkan estimasi tagihan yang akan dibuat
- Statistik sistem (santri aktif, jenis tagihan rutin)
- AJAX-based form dengan validation

**Controller Methods:**
- `automationManagement()`: Menampilkan halaman manajemen
- `copyRoutineTagihan()`: Eksekusi penyalinan tagihan
- `previewCopyRoutineTagihan()`: Preview estimasi penyalinan

### 4. Routes Baru

**File**: `routes/tunggakan.php`

```php
Route::get('/automation', [TunggakanController::class, 'automationManagement'])->name('automation');
Route::post('/copy-routine', [TunggakanController::class, 'copyRoutineTagihan'])->name('copy-routine');
Route::post('/preview-copy-routine', [TunggakanController::class, 'previewCopyRoutineTagihan'])->name('preview-copy-routine');
```

## Cara Penggunaan

### 1. Melihat Toggle Tunggakan Tahun Sebelumnya

1. Masuk ke menu **Keuangan > Tunggakan > Santri Aktif**
2. Klik pada nama santri untuk melihat detail
3. Di halaman detail, akan tampil:
   - Ringkasan total tunggakan (tahun ini + tahun sebelumnya)
   - Tabel tagihan tahun berjalan (default ditampilkan)
   - Tombol **"Tampilkan Tagihan Tahun Sebelumnya"** untuk toggle

### 2. Otomatisasi Tagihan Rutin

#### Via Web Interface:

1. Masuk ke menu **Keuangan > Tunggakan > Santri Aktif**
2. Klik tombol **"Otomatisasi"** di kanan atas
3. Pada halaman otomatisasi:
   - Pilih tahun ajaran tujuan
   - Pilih tahun ajaran sumber (opsional, default: tahun sebelumnya)
   - Sistem akan menampilkan preview estimasi
   - Klik **"Salin Tagihan Rutin"** untuk eksekusi

#### Via Command Line:

```bash
# Menyalin dari tahun sebelumnya ke tahun aktif
php artisan tagihan:copy-routine --confirm

# Menyalin dari tahun tertentu ke tahun tertentu
php artisan tagihan:copy-routine --source-year-id=1 --target-year-id=2 --confirm

# Preview terlebih dahulu (tanpa --confirm)
php artisan tagihan:copy-routine --target-year-id=2
```

## Logika Sistem

### 1. Identifikasi Tagihan Rutin

Sistem mengidentifikasi tagihan rutin berdasarkan:
- `kategori_tagihan = 'Rutin'`
- Tagihan ini akan berlanjut setiap tahun ajaran kecuali ada perubahan

### 2. Penyalinan Tagihan

Proses penyalinan:
1. Ambil semua santri aktif
2. Cek apakah santri memiliki tagihan rutin di tahun sebelumnya
3. Untuk setiap jenis tagihan rutin:
   - Cek apakah tagihan sudah ada di tahun tujuan (skip jika ada)
   - Tentukan nominal berdasarkan kelas jika berlaku
   - Generate tagihan bulanan (12 bulan) atau tahunan sesuai jenis

### 3. Nominal Determination

Prioritas penentuan nominal:
1. Nominal dari `jenis_tagihan_kelas` jika ada dan `is_nominal_per_kelas = true`
2. Nominal dari tagihan tahun sebelumnya (untuk menjaga konsistensi jika ada penyesuaian)
3. Nominal default dari `jenis_tagihan`

## Keamanan dan Validasi

### 1. Validasi Input
- Tahun ajaran tujuan harus valid dan ada
- Tahun ajaran sumber (jika dipilih) harus valid
- Tidak boleh ada duplikasi tagihan

### 2. Transaction Safety
- Semua operasi database menggunakan transaction
- Rollback otomatis jika terjadi error
- Logging untuk audit trail

### 3. Permissions
- Fitur ini memerlukan autentikasi
- Access control sesuai role pengguna

## Monitoring dan Logging

### 1. Sistem Log
- `TagihanService` mencatat setiap operasi penyalinan
- Error logging untuk debugging
- Success metrics (jumlah tagihan yang disalin)

### 2. Preview System
- Estimasi jumlah tagihan yang akan dibuat
- Daftar santri yang terpengaruh
- Jenis tagihan rutin yang akan disalin

## Maintenance dan Troubleshooting

### 1. Command untuk Maintenance

```bash
# Generate tagihan santri (command yang sudah ada)
php artisan tagihan:generate --tahun-ajaran-id=2

# Copy routine tagihan
php artisan tagihan:copy-routine --target-year-id=2 --confirm

# Clear cache setelah perubahan
php artisan route:clear
php artisan config:clear
php artisan view:clear
```

### 2. Troubleshooting Common Issues

**Issue**: Tagihan tidak muncul di tahun baru
- **Solusi**: Jalankan `php artisan tagihan:copy-routine`

**Issue**: Nominal tidak sesuai dengan kelas
- **Solusi**: Periksa tabel `jenis_tagihan_kelas` dan `is_nominal_per_kelas`

**Issue**: Toggle tidak berfungsi
- **Solusi**: Pastikan Alpine.js loaded dan JavaScript tidak ada error

## Database Schema Changes

Tidak ada perubahan skema database yang diperlukan. Sistem menggunakan:
- `tagihan_santris` table (sudah ada)
- `jenis_tagihans` table (sudah ada)  
- `jenis_tagihan_kelas` table (sudah ada)
- `tahun_ajarans` table (sudah ada)

## Testing

### 1. Manual Testing

1. **Test Toggle Functionality**:
   - Buat santri dengan tagihan di 2 tahun ajaran berbeda
   - Akses detail tunggakan
   - Verifikasi toggle berfungsi

2. **Test Automation**:
   - Buat tahun ajaran baru
   - Gunakan interface web untuk copy routine tagihan
   - Verifikasi tagihan tersalin dengan benar

### 2. Command Testing

```bash
# Test command help
php artisan tagihan:copy-routine --help

# Test preview mode
php artisan tagihan:copy-routine --target-year-id=2

# Test actual copy
php artisan tagihan:copy-routine --target-year-id=2 --confirm
```

## Kesimpulan

Sistem tunggakan santri telah berhasil ditingkatkan dengan:

1. ✅ **Toggle tunggakan tahun sebelumnya** - Memungkinkan melihat riwayat tunggakan tanpa menggangu tampilan utama
2. ✅ **Otomatisasi tagihan rutin dengan modal popup** - Modal terintegrasi di halaman Jenis Tagihan untuk kemudahan akses
3. ✅ **Interface yang lebih streamlined** - Menghapus halaman terpisah untuk otomatisasi, menggunakan modal popup
4. ✅ **Command line tools** - Untuk automation dan maintenance (tetap tersedia)
5. ✅ **Proper validation dan safety** - Mencegah data corruption dan error

### Update Terbaru (Modal Popup Implementation):

**PERUBAHAN:**
- Dashboard otomatisasi telah dipindahkan dari halaman terpisah menjadi **modal popup** di halaman **Jenis Tagihan**
- Menghapus tombol otomatisasi dari halaman santri aktif, mutasi, dan alumni  
- Menghapus link otomatisasi dari sidebar menu tunggakan
- Modal popup menyediakan preview, filter kategori, dan konfirmasi sebelum eksekusi

**LOKASI AKSES BARU:**
- Menu: `Keuangan > Jenis Tagihan`
- Tombol: **"Otomatisasi Tagihan"** (hijau, dengan icon wrench)
- Format: Modal popup dengan preview dan konfirmasi

**KEUNTUNGAN MODAL POPUP:**
- Lebih user-friendly dan tidak memisahkan context
- Akses langsung dari halaman yang relevan (Jenis Tagihan)
- Preview real-time sebelum eksekusi
- Tidak perlu navigasi ke halaman terpisah

Sistem ini siap untuk production dan akan membantu mengurangi beban kerja staff keuangan dalam mengelola tagihan santri lintas tahun ajaran.

---

**Dibuat pada**: 6 Juni 2025
**Update Terakhir**: 6 Juni 2025 - Modal Popup Implementation
**Status**: Selesai dan siap production
**Testing**: Manual testing completed
