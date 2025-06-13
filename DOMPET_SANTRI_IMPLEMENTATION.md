# Implementasi Fitur Dompet Santri untuk PWA Wali Santri

## Overview

Fitur Dompet Santri memungkinkan wali santri untuk memantau penggunaan dompet elektronik santri, melihat riwayat transaksi, dan mengatur limit harian pengeluaran. Implementasi ini menggunakan Vue 3, Axios, dan Pinia/Vuex untuk state management.

## Endpoints API

Gunakan endpoint-endpoint berikut yang tersedia di variabel lingkungan:

1. `${import.meta.env.VITE_API_BASE_URL}${import.meta.env.VITE_API_DOMPET_ENDPOINT}` - GET: untuk mendapatkan informasi dompet semua santri
2. `${import.meta.env.VITE_API_BASE_URL}${import.meta.env.VITE_API_DOMPET_DETAIL_ENDPOINT}/${santriId}` - GET: untuk mendapatkan detail dompet per santri
3. `${import.meta.env.VITE_API_BASE_URL}${import.meta.env.VITE_API_DOMPET_TRANSAKSI_ENDPOINT}/${santriId}` - GET: untuk mendapatkan riwayat transaksi dompet
4. `${import.meta.env.VITE_API_BASE_URL}${import.meta.env.VITE_API_DOMPET_SUMMARY_ENDPOINT}/${santriId}` - GET: untuk mendapatkan ringkasan dompet
5. `${import.meta.env.VITE_API_BASE_URL}${import.meta.env.VITE_API_DOMPET_LIMIT_ENDPOINT}/${santriId}` - PUT: untuk mengubah limit harian dompet

## Struktur Halaman dan Komponen

### Halaman

1. **DompetPage.vue**
   - Halaman utama yang menampilkan daftar dompet santri
   - Menampilkan kartu untuk setiap santri dengan informasi saldo dan status dompet
   - Tombol untuk melihat detail dompet santri

2. **DompetDetailPage.vue**
   - Halaman detail dompet santri
   - Informasi saldo dan status dompet
   - Ringkasan penggunaan (hari ini, minggu ini, bulan ini)
   - Pengaturan limit harian
   - Riwayat transaksi dengan fitur pagination dan filter berdasarkan tanggal

### Komponen

1. **DompetSummary.vue**
   - Komponen untuk menampilkan ringkasan dompet
   - Saldo saat ini
   - Statistik penggunaan
   - Grafik penggunaan (opsional)

2. **DompetLimitForm.vue**
   - Komponen untuk mengatur limit harian
   - Form input untuk limit harian
   - Validasi input (hanya angka, min 0)
   - Tombol simpan untuk mengirim request PUT

3. **DompetTransactionList.vue**
   - Komponen untuk menampilkan riwayat transaksi
   - Tabel/list transaksi dengan informasi tanggal, jenis, nominal, dan deskripsi
   - Pagination
   - Filter berdasarkan tanggal dan jenis transaksi

4. **DompetEmptyState.vue**
   - Komponen untuk menampilkan pesan ketika data dompet kosong atau belum diaktifkan

## Model Data (Contoh API Responses)

### GET Dompet Info

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "santri_id": 123,
      "santri_nama": "Ahmad Santoso",
      "santri_nis": "S12345",
      "saldo": 250000,
      "status": "aktif",
      "last_update": "2025-06-13 08:30:00"
    }
  ]
}
```

### GET Dompet Detail

```json
{
  "success": true,
  "data": {
    "id": 1,
    "santri_id": 123,
    "santri_nama": "Ahmad Santoso",
    "santri_nis": "S12345",
    "saldo": 250000,
    "status": "aktif",
    "limits": [
      {
        "id": 1,
        "jenis": "harian",
        "nominal": 50000,
        "periode": "harian",
        "status": "aktif"
      }
    ],
    "last_update": "2025-06-13 08:30:00"
  }
}
```

### GET Transaksi Dompet

```json
{
  "success": true,
  "data": [
    {
      "id": 101,
      "tanggal": "2025-06-13 07:15:00",
      "jenis": "pengeluaran",
      "nominal": 15000,
      "deskripsi": "Pembelian di Kantin",
      "saldo_sebelum": 265000,
      "saldo_sesudah": 250000,
      "status": "sukses"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 20,
    "total": 98
  }
}
```

### PUT Update Limit Harian

**Request Body:**
```json
{
  "limit_harian": 50000
}
```

**Response:**
```json
{
  "success": true,
  "message": "Limit harian berhasil diperbarui",
  "data": {
    "id": 1,
    "jenis": "harian",
    "nominal": 50000,
    "periode": "harian",
    "status": "aktif"
  }
}
```

## State Management (Pinia Store)

Buat store untuk menyimpan data dompet dengan fitur berikut:

```js
// Struktur dasar store
export const useDompetStore = defineStore('dompet', {
  state: () => ({
    dompetList: [],
    currentDompet: null,
    transaksiList: [],
    loading: false,
    error: null,
    pagination: {
      currentPage: 1,
      totalPages: 1,
      perPage: 20,
      total: 0
    },
    filters: {
      startDate: null,
      endDate: null,
      jenis: null
    }
  }),
  
  getters: {
    // Getter untuk dompet, transaksi, dan statistik
  },
  
  actions: {
    // Actions untuk fetch data dan update limit
    async fetchDompetList() { /* ... */ },
    async fetchDompetDetail(santriId) { /* ... */ },
    async fetchTransaksi(santriId, page = 1, filters = {}) { /* ... */ },
    async updateLimitHarian(santriId, limitHarian) { /* ... */ }
  }
});
```

## Routes

Tambahkan routes berikut ke file router:

```js
const routes = [
  // ...existing routes
  {
    path: '/dompet',
    name: 'dompet',
    component: () => import('@/views/DompetPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/dompet/:id',
    name: 'dompet-detail',
    component: () => import('@/views/DompetDetailPage.vue'),
    meta: { requiresAuth: true },
    props: true
  }
];
```

## UI/UX

1. **Loading States**
   - Tampilkan skeleton loader saat data sedang dimuat
   - Gunakan loading spinner untuk operasi yang lebih kecil seperti update limit

2. **Feedback**
   - Tampilkan toast/snackbar untuk konfirmasi operasi berhasil
   - Tampilkan pesan error yang jelas dan informatif

3. **Responsivitas**
   - Pastikan UI responsif untuk semua ukuran layar
   - Pertimbangkan tampilan khusus mobile untuk tabel transaksi

## Error Handling

1. **Network Errors**
   - Handle timeout dan connection issues
   - Tampilkan retry option ketika gagal mengambil data

2. **API Errors**
   - Parse dan tampilkan pesan error dari API
   - Handle 401 (unauthorized) dengan redirect ke login

3. **Validasi**
   - Validasi input limit harian (hanya angka positif)
   - Tampilkan pesan error validasi yang jelas

## Filter dan Fitur Tambahan

1. **Filter Transaksi**
   - Implementasi date picker untuk filter transaksi berdasarkan rentang tanggal
   - Implementasi dropdown untuk filter berdasarkan jenis transaksi

2. **Grafik dan Visualisasi**
   - Gunakan Chart.js atau library serupa untuk visualisasi penggunaan dompet
   - Tampilkan grafik penggunaan harian, mingguan, dan bulanan

3. **Export Data**
   - Tambahkan fitur untuk export transaksi ke PDF atau Excel (opsional)

## Integrasi dengan Komponen Lain

1. **Navigasi**
   - Tambahkan item menu dompet santri ke navigation drawer
   - Gunakan badge untuk indikator notifikasi jika diperlukan

2. **Dashboard**
   - Tambahkan widget ringkasan dompet di dashboard wali santri
   - Link ke halaman detail dompet

## Testing

1. **Unit Tests**
   - Uji komponen Vue dengan Vue Testing Library atau Jest
   - Verifikasi rendering dan interaksi user

2. **API Mocking**
   - Gunakan MSW (Mock Service Worker) atau teknologi serupa untuk mock API
   - Uji skenario error dan loading states

## Deployment

1. **Environment Variables**
   - Pastikan semua variabel lingkungan untuk endpoint API sudah dikonfigurasi
   - Buat versi .env untuk staging dan production

## Catatan Implementasi

- Gunakan Composition API untuk pengembangan komponen Vue
- Manfaatkan fitur reactive() dan computed() untuk reaktivitas data
- Implementasi infinite scroll untuk daftar transaksi yang panjang (alternatif pagination)
- Simpan state filter di localStorage untuk persistensi
