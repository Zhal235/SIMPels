# Dokumentasi Seeder Keuangan

## Overview
Seeder untuk sistem keuangan telah diperbarui agar sesuai dengan jenis tagihan yang ada dalam sistem. Seeder ini mencakup JenisBukuKasSeeder dan BukuKasSeeder.

## Seeder yang Diperbarui

### 1. JenisBukuKasSeeder
**File:** `database/seeders/JenisBukuKasSeeder.php`

Menciptakan jenis-jenis kas yang sesuai dengan jenis tagihan:

#### Jenis Kas Berdasarkan Tagihan:
- **SPP** (Kode: SPP) - Kas untuk penerimaan SPP bulanan santri
- **Uang Gedung** (Kode: UG) - Kas untuk penerimaan uang gedung santri baru
- **Seragam** (Kode: SRG) - Kas untuk penerimaan biaya seragam santri

#### Jenis Kas Operasional:
- **Operasional** (Kode: OPS) - Kas untuk kegiatan operasional sehari-hari
- **Pembangunan** (Kode: PBG) - Kas untuk kegiatan pembangunan dan pengembangan infrastruktur
- **Insidental** (Kode: INS) - Kas untuk kegiatan insidental/tidak rutin
- **Cadangan Darurat** (Kode: CAD) - Kas cadangan untuk keperluan darurat

### 2. BukuKasSeeder
**File:** `database/seeders/BukuKasSeeder.php`

Menciptakan buku-buku kas yang terhubung dengan jenis kas melalui foreign key.

#### Buku Kas yang Dibuat:
1. **Kas SPP Santri** (SPP-001)
   - Saldo Awal: Rp 50,000,000
   - Saldo Saat Ini: Rp 45,000,000

2. **Kas Uang Gedung** (UG-001)
   - Saldo Awal: Rp 25,000,000
   - Saldo Saat Ini: Rp 20,000,000

3. **Kas Seragam Santri** (SRG-001)
   - Saldo Awal: Rp 10,000,000
   - Saldo Saat Ini: Rp 8,500,000

4. **Kas Operasional Harian** (OPS-001)
   - Saldo Awal: Rp 15,000,000
   - Saldo Saat Ini: Rp 12,000,000

5. **Kas Pembangunan Gedung** (PBG-001)
   - Saldo Awal: Rp 100,000,000
   - Saldo Saat Ini: Rp 85,000,000

6. **Kas Kegiatan Insidental** (INS-001)
   - Saldo Awal: Rp 5,000,000
   - Saldo Saat Ini: Rp 3,500,000

7. **Kas Cadangan Darurat** (CAD-001)
   - Saldo Awal: Rp 10,000,000
   - Saldo Saat Ini: Rp 10,000,000

## Fitur Seeder

### Pencegahan Duplikasi
- **JenisBukuKasSeeder**: Menggunakan `updateOrCreate()` berdasarkan nama jenis kas
- **BukuKasSeeder**: Menggunakan `updateOrCreate()` berdasarkan nama kas
- Dapat dijalankan berulang kali tanpa error duplikasi

### Relasi Database
- BukuKasSeeder memastikan foreign key `jenis_kas_id` terhubung dengan benar
- Validasi keberadaan jenis kas sebelum membuat buku kas

## Cara Menjalankan

### Menjalankan Seeder Individual
```bash
# Jalankan JenisBukuKasSeeder terlebih dahulu
php artisan db:seed --class=JenisBukuKasSeeder

# Kemudian jalankan BukuKasSeeder
php artisan db:seed --class=BukuKasSeeder
```

### Menjalankan Semua Seeder
```bash
php artisan db:seed
```

## Integrasi dengan Jenis Tagihan

### Kesesuaian dengan JenisTagihanSeeder
Seeder ini telah diselaraskan dengan `JenisTagihanSeeder` yang menciptakan:
- **SPP** - Tagihan bulanan dengan nominal berbeda per kelas
- **Uang Gedung** - Tagihan satu kali saat masuk (Rp 2,500,000)
- **Seragam** - Tagihan dengan nominal berbeda per kelas

### Mapping Tagihan ke Kas
Setiap jenis tagihan memiliki kas yang sesuai:
- Pembayaran SPP → Kas SPP Santri
- Pembayaran Uang Gedung → Kas Uang Gedung
- Pembayaran Seragam → Kas Seragam Santri

## Database Schema Dependency

### Urutan Eksekusi dalam DatabaseSeeder
```php
$this->call([
    // ... seeder lain
    JenisBukuKasSeeder::class,  // Harus dijalankan sebelum BukuKasSeeder
    JenisTagihanSeeder::class,
    BukuKasSeeder::class,       // Bergantung pada JenisBukuKasSeeder
    // ... seeder lain
]);
```

## Testing

Seeder telah diuji dengan:
- Syntax check menggunakan `php -l`
- Eksekusi seeder berhasil tanpa error
- Validasi data yang tercipta sesuai dengan relasi foreign key

## Maintenance

### Menambah Jenis Kas Baru
1. Tambahkan array baru di `JenisBukuKasSeeder`
2. Buat buku kas yang sesuai di `BukuKasSeeder`
3. Update dokumentasi ini

### Mengubah Saldo Default
Ubah nilai `saldo_awal` dan `saldo_saat_ini` di array `$bukuKas` dalam `BukuKasSeeder`.

Dokumentasi ini mencakup seluruh aspek seeder keuangan yang telah diperbarui untuk menyelaraskan dengan sistem tagihan pesantren.
