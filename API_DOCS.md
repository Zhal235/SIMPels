# API Dokumentasi untuk PWA Wali Santri SIMPels

## Pengantar

Dokumentasi ini berisi informasi tentang API yang disediakan untuk PWA (Progressive Web App) Wali Santri yang terhubung dengan aplikasi SIMPels (Sistem Informasi Manajemen Pesantren). API ini menyediakan akses ke data santri, tagihan, transaksi, keringanan, asrama, dan perizinan.

## Base URL

```
https://yourdomain.com/api/wali-santri
```

## Autentikasi

API ini menggunakan Laravel Sanctum untuk autentikasi. Token akan diberikan setelah login dan harus disertakan dalam header setiap permintaan API yang memerlukan autentikasi.

### Header Autentikasi

```
Authorization: Bearer {your_token}
```

## Endpoint Publik

### 1. Login

- **URL**: `/login`
- **Metode**: `POST`
- **Deskripsi**: Mengautentikasi pengguna dan memberikan token.
- **Body**:
  ```json
  {
    "email": "wali@example.com",
    "password": "password",
    "device_name": "Mobile App" // Opsional
  }
  ```
- **Respons Sukses**:
  ```json
  {
    "success": true,
    "token": "1|abcdef123456...",
    "user": {
      "id": 1,
      "name": "Nama Wali",
      "email": "wali@example.com"
    },
    "santri": [
      {
        "id": 1,
        "nama": "Nama Santri",
        "nis": "123456",
        "kelas": "VII A",
        "asrama": "Asrama Putra 1",
        "foto": "https://yourdomain.com/storage/santri/foto.jpg"
      }
    ]
  }
  ```

### 2. Registrasi

- **URL**: `/register`
- **Metode**: `POST`
- **Deskripsi**: Mendaftarkan wali santri baru.
- **Body**:
  ```json
  {
    "name": "Nama Wali",
    "email": "wali@example.com",
    "password": "password",
    "password_confirmation": "password",
    "phone": "081234567890",
    "santri_nis": "123456"
  }
  ```
- **Respons Sukses**:
  ```json
  {
    "success": true,
    "message": "Pendaftaran berhasil",
    "token": "1|abcdef123456...",
    "user": {
      "id": 1,
      "name": "Nama Wali",
      "email": "wali@example.com"
    }
  }
  ```

## Endpoint Terproteksi (Memerlukan Autentikasi)

### 1. Informasi Pengguna

- **URL**: `/user`
- **Metode**: `GET`
- **Deskripsi**: Mendapatkan informasi pengguna dan santri terkait.
- **Respons Sukses**:
  ```json
  {
    "success": true,
    "user": {
      "id": 1,
      "name": "Nama Wali",
      "email": "wali@example.com"
    },
    "santri": [
      {
        "id": 1,
        "nama": "Nama Santri",
        "nis": "123456",
        "kelas": "VII A",
        "asrama": "Asrama Putra 1",
        "foto": "https://yourdomain.com/storage/santri/foto.jpg",
        "jenis_kelamin": "L",
        "status": "aktif"
      }
    ]
  }
  ```

### 2. Logout

- **URL**: `/logout`
- **Metode**: `POST`
- **Deskripsi**: Menghapus token autentikasi.
- **Respons Sukses**:
  ```json
  {
    "success": true,
    "message": "Logged out successfully"
  }
  ```

### 3. Daftar Santri

- **URL**: `/santri`
- **Metode**: `GET`
- **Deskripsi**: Mendapatkan daftar santri yang terkait dengan wali.
- **Respons Sukses**:
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "nama": "Nama Santri",
        "nis": "123456",
        "kelas": "VII A",
        "asrama": "Asrama Putra 1",
        "foto": "https://yourdomain.com/storage/santri/foto.jpg",
        "jenis_kelamin": "L",
        "status": "aktif"
      }
    ]
  }
  ```

### 4. Detail Santri

- **URL**: `/santri/{id}`
- **Metode**: `GET`
- **Deskripsi**: Mendapatkan detail santri.
- **Parameter Path**: `id` - ID santri
- **Respons Sukses**:
  ```json
  {
    "success": true,
    "data": {
      "id": 1,
      "nis": "123456",
      "nama_santri": "Nama Santri",
      "jenis_kelamin": "L",
      "tempat_lahir": "Jakarta",
      "tanggal_lahir": "2010-01-01",
      "foto": "https://yourdomain.com/storage/santri/foto.jpg",
      "status": "aktif",
      "pendidikan": {
        "kelas": "VII A"
      },
      "asrama": {
        "nama": "Asrama Putra 1"
      },
      "orangtua": {
        "nama_ayah": "Nama Ayah",
        "pekerjaan_ayah": "PNS",
        "hp_ayah": "081234567890",
        "nama_ibu": "Nama Ibu",
        "pekerjaan_ibu": "Guru",
        "hp_ibu": "081234567891"
      },
      "alamat": {
        "alamat": "Jl. Contoh No. 123",
        "desa": "Desa Contoh",
        "kecamatan": "Kecamatan Contoh",
        "kabupaten": "Kabupaten Contoh",
        "provinsi": "Provinsi Contoh",
        "kode_pos": "12345"
      },
      "informasi_lainnya": {
        "no_bpjs": "1234567890",
        "no_kip": "1234567890",
        "no_pkh": "1234567890"
      }
    }
  }
  ```

### 5. Daftar Tagihan

- **URL**: `/tagihan`
- **Metode**: `GET`
- **Deskripsi**: Mendapatkan daftar tagihan santri.
- **Parameter Query**: `santri_id` - ID santri (opsional, jika tidak diisi akan menampilkan semua tagihan dari semua santri yang terkait)
- **Respons Sukses**:
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "santri": {
          "id": 1,
          "nama": "Nama Santri",
          "nis": "123456"
        },
        "jenis_tagihan": {
          "id": 1,
          "nama": "SPP",
          "kategori": "Rutin",
          "is_bulanan": true
        },
        "bulan": "2025-06",
        "bulan_tahun": "Juni 2025",
        "nominal_tagihan": 500000,
        "nominal_dibayar": 300000,
        "nominal_keringanan": 0,
        "sisa_tagihan": 200000,
        "status_pembayaran": "sebagian",
        "tanggal_jatuh_tempo": "2025-06-10",
        "is_jatuh_tempo": false,
        "persentase_pembayaran": 60
      }
    ]
  }
  ```

### 6. Detail Tagihan

- **URL**: `/tagihan/{id}`
- **Metode**: `GET`
- **Deskripsi**: Mendapatkan detail tagihan tertentu.
- **Parameter Path**: `id` - ID tagihan
- **Respons Sukses**:
  ```json
  {
    "success": true,
    "data": {
      "id": 1,
      "santri": {
        "id": 1,
        "nama": "Nama Santri",
        "nis": "123456"
      },
      "jenis_tagihan": {
        "id": 1,
        "nama": "SPP",
        "kategori": "Rutin",
        "deskripsi": "Sumbangan Pembinaan Pendidikan",
        "is_bulanan": true
      },
      "tahun_ajaran": {
        "id": 1,
        "nama": "2025/2026"
      },
      "bulan": "2025-06",
      "bulan_tahun": "Juni 2025",
      "nominal_tagihan": 500000,
      "nominal_dibayar": 300000,
      "nominal_keringanan": 0,
      "sisa_tagihan": 200000,
      "status_pembayaran": "sebagian",
      "tanggal_jatuh_tempo": "2025-06-10",
      "is_jatuh_tempo": false,
      "persentase_pembayaran": 60,
      "transaksi": [
        {
          "id": 1,
          "nominal": 300000,
          "tanggal": "2025-06-05",
          "keterangan": "Pembayaran SPP Juni 2025",
          "tipe_pembayaran": "transfer"
        }
      ]
    }
  }
  ```

### 7. Ringkasan Tagihan

- **URL**: `/tagihan/summary`
- **Metode**: `GET`
- **Deskripsi**: Mendapatkan ringkasan tagihan santri.
- **Parameter Query**: `santri_id` - ID santri (opsional)
- **Respons Sukses**:
  ```json
  {
    "success": true,
    "data": [
      {
        "santri": {
          "id": 1,
          "nama": "Nama Santri",
          "nis": "123456"
        },
        "summary_rutin": {
          "total_tagihan": 6000000,
          "total_dibayar": 3000000,
          "total_keringanan": 0,
          "sisa_tagihan": 3000000,
          "jumlah_tagihan": 12,
          "jumlah_lunas": 6
        },
        "summary_insidentil": {
          "total_tagihan": 2000000,
          "total_dibayar": 1000000,
          "total_keringanan": 0,
          "sisa_tagihan": 1000000,
          "jumlah_tagihan": 4,
          "jumlah_lunas": 2
        },
        "summary_total": {
          "total_tagihan": 8000000,
          "total_dibayar": 4000000,
          "total_keringanan": 0,
          "sisa_tagihan": 4000000,
          "jumlah_tagihan": 16,
          "jumlah_lunas": 8
        },
        "tahun_ajaran": {
          "id": 1,
          "nama": "2025/2026"
        }
      }
    ]
  }
  ```

### 8. Daftar Tunggakan

- **URL**: `/tagihan/tunggakan`
- **Metode**: `GET`
- **Deskripsi**: Mendapatkan daftar tunggakan santri.
- **Parameter Query**: `santri_id` - ID santri (opsional)
- **Respons Sukses**:
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "santri": {
          "id": 1,
          "nama": "Nama Santri",
          "nis": "123456"
        },
        "jenis_tagihan": {
          "id": 1,
          "nama": "SPP",
          "kategori": "Rutin"
        },
        "tahun_ajaran": {
          "id": 1,
          "nama": "2025/2026"
        },
        "bulan": "2025-05",
        "bulan_tahun": "Mei 2025",
        "nominal_tagihan": 500000,
        "nominal_dibayar": 0,
        "nominal_keringanan": 0,
        "sisa_tagihan": 500000,
        "tanggal_jatuh_tempo": "2025-05-10"
      }
    ]
  }
  ```

### 9. Detail Tunggakan

- **URL**: `/tagihan/tunggakan/{id}`
- **Metode**: `GET`
- **Deskripsi**: Mendapatkan detail tunggakan tertentu.
- **Parameter Path**: `id` - ID tagihan
- **Respons Sukses**: Sama seperti endpoint Detail Tagihan (`/tagihan/{id}`)

### 10. Daftar Transaksi

- **URL**: `/transaksi`
- **Metode**: `GET`
- **Deskripsi**: Mendapatkan daftar transaksi pembayaran santri.
- **Parameter Query**: `santri_id` - ID santri (opsional)
- **Respons Sukses**:
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "santri": {
          "id": 1,
          "nama": "Nama Santri",
          "nis": "123456"
        },
        "jenis_tagihan": {
          "id": 1,
          "nama": "SPP",
          "kategori": "Rutin"
        },
        "bulan": "2025-06",
        "bulan_tahun": "Juni 2025",
        "tagihan_santri_id": 1,
        "tahun_ajaran": {
          "id": 1,
          "nama": "2025/2026"
        },
        "nominal": 300000,
        "tanggal": "2025-06-05",
        "keterangan": "Pembayaran SPP Juni 2025",
        "tipe_pembayaran": "transfer"
      }
    ]
  }
  ```

### 11. Detail Transaksi

- **URL**: `/transaksi/{id}`
- **Metode**: `GET`
- **Deskripsi**: Mendapatkan detail transaksi tertentu.
- **Parameter Path**: `id` - ID transaksi
- **Respons Sukses**:
  ```json
  {
    "success": true,
    "data": {
      "id": 1,
      "santri": {
        "id": 1,
        "nama": "Nama Santri",
        "nis": "123456"
      },
      "tagihan_santri_id": 1,
      "tahun_ajaran": {
        "id": 1,
        "nama": "2025/2026"
      },
      "nominal": 300000,
      "tanggal": "2025-06-05",
      "keterangan": "Pembayaran SPP Juni 2025",
      "tipe_pembayaran": "transfer",
      "tagihan": {
        "id": 1,
        "jenis_tagihan": {
          "id": 1,
          "nama": "SPP",
          "kategori": "Rutin",
          "deskripsi": "Sumbangan Pembinaan Pendidikan"
        },
        "bulan": "2025-06",
        "bulan_tahun": "Juni 2025",
        "nominal_tagihan": 500000,
        "nominal_dibayar": 300000,
        "nominal_keringanan": 0,
        "sisa_tagihan": 200000,
        "status_pembayaran": "sebagian"
      }
    }
  }
  ```

### 12. Daftar Keringanan

- **URL**: `/keringanan`
- **Metode**: `GET`
- **Deskripsi**: Mendapatkan daftar keringanan tagihan santri.
- **Parameter Query**: `santri_id` - ID santri (opsional)
- **Respons Sukses**:
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "santri": {
          "id": 1,
          "nama": "Nama Santri",
          "nis": "123456"
        },
        "jenis_keringanan": "potongan_persen",
        "nilai_potongan": 50,
        "keterangan": "Beasiswa prestasi",
        "status": "aktif",
        "tanggal_mulai": "2025-06-01",
        "tanggal_selesai": "2026-06-01",
        "tahun_ajaran": {
          "id": 1,
          "nama": "2025/2026"
        },
        "jenis_tagihan": {
          "id": 1,
          "nama": "SPP",
          "deskripsi": "Sumbangan Pembinaan Pendidikan"
        }
      }
    ]
  }
  ```

### 13. Detail Keringanan

- **URL**: `/keringanan/{id}`
- **Metode**: `GET`
- **Deskripsi**: Mendapatkan detail keringanan tertentu.
- **Parameter Path**: `id` - ID keringanan
- **Respons Sukses**:
  ```json
  {
    "success": true,
    "data": {
      "id": 1,
      "santri": {
        "id": 1,
        "nama": "Nama Santri",
        "nis": "123456"
      },
      "jenis_keringanan": "potongan_persen",
      "nilai_potongan": 50,
      "keterangan": "Beasiswa prestasi",
      "status": "aktif",
      "tanggal_mulai": "2025-06-01",
      "tanggal_selesai": "2026-06-01",
      "tahun_ajaran": {
        "id": 1,
        "nama": "2025/2026"
      },
      "jenis_tagihan": {
        "id": 1,
        "nama": "SPP",
        "deskripsi": "Sumbangan Pembinaan Pendidikan"
      },
      "tagihan_affected": [
        {
          "id": 1,
          "jenis_tagihan": "SPP",
          "bulan_tahun": "Juni 2025",
          "nominal_tagihan": 500000,
          "nominal_keringanan": 250000,
          "nominal_harus_dibayar": 250000,
          "status_pembayaran": "belum_bayar"
        },
        {
          "id": 2,
          "jenis_tagihan": "SPP",
          "bulan_tahun": "Juli 2025",
          "nominal_tagihan": 500000,
          "nominal_keringanan": 250000,
          "nominal_harus_dibayar": 250000,
          "status_pembayaran": "belum_bayar"
        }
      ]
    }
  }
  ```

### 14. Informasi Akademik

- **URL**: `/akademik/info`
- **Metode**: `GET`
- **Deskripsi**: Mendapatkan informasi akademik santri.
- **Parameter Query**: `santri_id` - ID santri (opsional)
- **Respons Sukses**:
  ```json
  {
    "success": true,
    "tahun_ajaran": {
      "id": 1,
      "nama": "2025/2026",
      "tahun_mulai": 2025,
      "tahun_selesai": 2026
    },
    "data": [
      {
        "santri_id": 1,
        "santri_nama": "Nama Santri",
        "santri_nis": "123456",
        "kelas_aktif": {
          "id": 1,
          "nama": "VII A",
          "tingkat": 7
        },
        "kelas_history": [
          {
            "id": 1,
            "kelas": {
              "id": 1,
              "nama": "VII A",
              "tingkat": 7
            },
            "tanggal_masuk": "2025-07-15",
            "tanggal_keluar": null,
            "status": "Aktif"
          }
        ]
      }
    ]
  }
  ```

### 15. Informasi Asrama

- **URL**: `/asrama/info`
- **Metode**: `GET`
- **Deskripsi**: Mendapatkan informasi asrama santri.
- **Parameter Query**: `santri_id` - ID santri (opsional)
- **Respons Sukses**:
  ```json
  {
    "success": true,
    "data": [
      {
        "santri_id": 1,
        "santri_nama": "Nama Santri",
        "santri_nis": "123456",
        "asrama_info": {
          "id": 1,
          "kode": "PA-1",
          "nama": "Asrama Putra 1",
          "jenis_asrama": "putra",
          "kapasitas": 40,
          "tanggal_masuk": "2025-07-15",
          "tanggal_keluar": null,
          "status": "Aktif"
        }
      }
    ]
  }
  ```

### 16. Daftar Perizinan

- **URL**: `/perizinan`
- **Metode**: `GET`
- **Deskripsi**: Mendapatkan daftar perizinan santri.
- **Parameter Query**: `santri_id` - ID santri (opsional)
- **Respons Sukses**:
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "santri": {
          "id": 1,
          "nama": "Nama Santri",
          "nis": "123456"
        },
        "jenis_izin": "sakit",
        "tanggal_mulai": "2025-06-10",
        "tanggal_selesai": "2025-06-12",
        "keterangan": "Sakit demam",
        "status": "disetujui",
        "diajukan_pada": "2025-06-09 10:00:00"
      }
    ]
  }
  ```

### 17. Buat Perizinan Baru

- **URL**: `/perizinan`
- **Metode**: `POST`
- **Deskripsi**: Membuat permintaan perizinan baru.
- **Body**:
  ```json
  {
    "santri_id": 1,
    "jenis_izin": "sakit", // sakit, pulang, keluar, kegiatan
    "tanggal_mulai": "2025-06-10",
    "tanggal_selesai": "2025-06-12",
    "keterangan": "Sakit demam",
    "bukti": "(file)" // Opsional, menggunakan form-data
  }
  ```
- **Respons Sukses**:
  ```json
  {
    "success": true,
    "message": "Perizinan berhasil diajukan",
    "data": {
      "id": 1,
      "santri_id": 1,
      "jenis_izin": "sakit",
      "tanggal_mulai": "2025-06-10",
      "tanggal_selesai": "2025-06-12",
      "keterangan": "Sakit demam",
      "status": "menunggu",
      "bukti": "https://yourdomain.com/storage/perizinan/bukti.jpg"
    }
  }
  ```

### 18. Detail Perizinan

- **URL**: `/perizinan/{id}`
- **Metode**: `GET`
- **Deskripsi**: Mendapatkan detail perizinan tertentu.
- **Parameter Path**: `id` - ID perizinan
- **Respons Sukses**:
  ```json
  {
    "success": true,
    "data": {
      "id": 1,
      "santri": {
        "id": 1,
        "nama": "Nama Santri",
        "nis": "123456"
      },
      "jenis_izin": "sakit",
      "tanggal_mulai": "2025-06-10",
      "tanggal_selesai": "2025-06-12",
      "lama_izin": 3,
      "keterangan": "Sakit demam",
      "status": "disetujui",
      "alasan_ditolak": null,
      "bukti": "https://yourdomain.com/storage/perizinan/bukti.jpg",
      "diajukan_pada": "2025-06-09 10:00:00",
      "diperbarui_pada": "2025-06-09 14:00:00",
      "disetujui_oleh": 2,
      "disetujui_pada": "2025-06-09 14:00:00"
    }
  }
  ```

### 19. Update Perizinan

- **URL**: `/perizinan/{id}`
- **Metode**: `PUT`
- **Deskripsi**: Memperbarui permintaan perizinan (hanya untuk yang masih status "menunggu").
- **Parameter Path**: `id` - ID perizinan
- **Body**:
  ```json
  {
    "jenis_izin": "pulang",
    "tanggal_mulai": "2025-06-10",
    "tanggal_selesai": "2025-06-13",
    "keterangan": "Pulang karena ada acara keluarga",
    "bukti": "(file)" // Opsional, menggunakan form-data
  }
  ```
- **Respons Sukses**:
  ```json
  {
    "success": true,
    "message": "Perizinan berhasil diperbarui",
    "data": {
      "id": 1,
      "jenis_izin": "pulang",
      "tanggal_mulai": "2025-06-10",
      "tanggal_selesai": "2025-06-13",
      "keterangan": "Pulang karena ada acara keluarga",
      "status": "menunggu",
      "bukti": "https://yourdomain.com/storage/perizinan/bukti_baru.jpg"
    }
  }
  ```

### 20. Hapus Perizinan

- **URL**: `/perizinan/{id}`
- **Metode**: `DELETE`
- **Deskripsi**: Menghapus permintaan perizinan (hanya untuk yang masih status "menunggu").
- **Parameter Path**: `id` - ID perizinan
- **Respons Sukses**:
  ```json
  {
    "success": true,
    "message": "Perizinan berhasil dihapus"
  }
  ```

## Kode Status HTTP

- `200 OK`: Permintaan berhasil
- `201 Created`: Sumber daya berhasil dibuat
- `400 Bad Request`: Kesalahan pada permintaan
- `401 Unauthorized`: Autentikasi gagal
- `403 Forbidden`: Tidak memiliki izin
- `404 Not Found`: Sumber daya tidak ditemukan
- `422 Unprocessable Entity`: Validasi gagal
- `500 Internal Server Error`: Kesalahan server

## Fitur yang Belum Diimplementasikan

Beberapa fitur yang disebutkan dalam desain awal mungkin memerlukan pengembangan lebih lanjut:

1. **Akademik**: Detail nilai/rapor santri
2. **Akademik**: Jadwal pelajaran
3. **Kesehatan**: Riwayat kunjungan UKS/klinik pesantren
4. **Komunikasi**: Forum diskusi wali santri
5. **Kegiatan**: Kalender kegiatan dan dokumentasi kegiatan
6. **Pembayaran Online**: Integrasi payment gateway

Fitur-fitur ini akan diimplementasikan dalam fase pengembangan berikutnya.

## Catatan Pengembangan

1. Pastikan untuk menambahkan kolom `user_id` pada tabel `santris` yang merujuk ke tabel `users` untuk mengaitkan santri dengan akun wali santri.
   
2. Untuk fitur perizinan, perlu dibuat tabel baru `perizinan` karena belum ada dalam struktur database yang ada.

3. Pastikan peran (role) 'wali_santri' sudah didaftarkan dalam sistem permission yang digunakan oleh aplikasi.

4. Sebaiknya tambahkan kolom `email_orangtua` pada tabel `santris` untuk memudahkan relasi antara santri dengan akun wali santri.

**Catatan**: Untuk panduan pengembangan lebih detail tentang fitur-fitur yang belum ada di SIMPels, silakan lihat file `DEVELOPMENT_GUIDE.md`.
