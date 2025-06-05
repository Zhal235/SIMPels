# Dokumentasi Halaman Tagihan Santri

## Deskripsi
Halaman "Tagihan Santri" adalah fitur monitoring untuk memantau implementasi tagihan ke seluruh santri berdasarkan jenis tagihan yang telah ditetapkan. Halaman ini merupakan sub menu dari modul Keuangan.

## Fitur Utama

### 1. Monitoring Implementasi Tagihan
- Melihat status implementasi tagihan untuk setiap santri
- Filter berdasarkan jenis tagihan spesifik atau semua jenis tagihan
- Statistik ringkasan (Total santri, Lengkap, Sebagian, Belum ada)

### 2. Filter dan Pencarian
- **Jenis Tagihan**: Filter berdasarkan jenis tagihan tertentu
- **Status Implementasi**: 
  - Untuk jenis tagihan spesifik: Sudah/Belum Ditetapkan
  - Untuk semua jenis tagihan: Lengkap (100%), Sebagian (1-99%), Belum Ada (0%)
- **Kelas**: Filter berdasarkan kelas santri
- **Asrama**: Filter berdasarkan asrama santri

### 3. Detail Modal
- Modal popup untuk melihat detail implementasi tagihan per santri
- Menampilkan semua jenis tagihan dengan status implementasi
- Informasi nominal, tanggal penetapan, dan keterangan

### 4. Export Excel
- Fitur export data ke format Excel (dalam pengembangan)
- Menggunakan filter yang sedang aktif

## Struktur File

### Controller
- **File**: `app/Http/Controllers/TagihanSantriController.php`
- **Method utama**:
  - `index()`: Menampilkan halaman utama dengan filter
  - `show($santriId)`: API endpoint untuk detail tagihan santri
  - `export()`: Export data ke Excel

### View
- **File**: `resources/views/keuangan/tagihan_santri/index.blade.php`
- **Fitur UI**:
  - Layout admin responsive
  - Alpine.js untuk interaktivitas
  - Modal detail dengan AJAX
  - Filter form dengan submit

### Routes
- **Prefix**: `keuangan/`
- **Middleware**: `auth`, `role:admin|bendahara`
- **Routes**:
  - `GET keuangan/tagihan-santri`: Halaman utama
  - `GET keuangan/tagihan-santri/{santriId}`: Detail santri (JSON)
  - `GET keuangan/tagihan-santri-export`: Export Excel

### Menu Sidebar
- **Lokasi**: Menu Keuangan > Tagihan Santri
- **Icon**: Material Icons `receipt_long`
- **Akses**: Admin dan Bendahara

## Model yang Digunakan
- `Santri`: Data santri aktif
- `JenisTagihan`: Jenis tagihan aktif untuk tahun ajaran
- `PembayaranSantri`: Implementasi tagihan ke santri
- `TahunAjaran`: Tahun ajaran aktif

## Dependencies
- Laravel Framework
- Alpine.js (untuk interaktivitas frontend)
- Tailwind CSS (untuk styling)
- Material Icons (untuk icon)

## Instalasi dan Setup
1. Route sudah terdaftar di `routes/web.php`
2. Controller sudah dibuat di `app/Http/Controllers/TagihanSantriController.php`
3. View sudah dibuat di `resources/views/keuangan/tagihan_santri/index.blade.php`
4. Menu sudah ditambahkan ke sidebar
5. Pastikan middleware dan role permission sudah dikonfigurasi

## Catatan Pengembangan
- Fitur export Excel masih dalam tahap pengembangan
- Dapat ditingkatkan dengan fitur bulk assign/remove tagihan
- Bisa ditambahkan notifikasi untuk santri yang belum lengkap tagihan
- Integrasi dengan sistem pembayaran untuk tracking real-time

## Testing
Akses halaman melalui:
```
http://your-domain/keuangan/tagihan-santri
```

Pastikan user sudah login dan memiliki role admin atau bendahara.
