# Prompt untuk GitHub Copilot - Membuat PWA Wali Santri SIMPels

Berikut adalah prompt yang dapat Anda salin dan gunakan dengan GitHub Copilot di VSCode untuk membuat PWA Wali Santri secara langsung. Copilot akan membantu menghasilkan file dan kode yang diperlukan berdasarkan prompt ini.

## Prompt Dasar

```
Buatkan Progressive Web App (PWA) untuk sistem wali santri pesantren dengan Vue.js 3 dan Tailwind CSS yang terintegrasi dengan API SIMPels. PWA ini harus memiliki fitur:

1. Autentikasi (login/register) wali santri
2. Melihat profil dan data santri
3. Melihat tagihan dan histori pembayaran
4. Mengelola perizinan santri (buat, lihat, edit, hapus)

Gunakan struktur berikut:
- Vue 3 dengan Composition API
- Vue Router untuk navigasi
- Vuex untuk state management
- Axios untuk API calls
- Tailwind CSS untuk styling
- PWA setup dengan service worker untuk offline capability

API endpoint:
- Autentikasi: POST /api/wali-santri/login, POST /api/wali-santri/register
- User data: GET /api/wali-santri/user
- Santri: GET /api/wali-santri/santri
- Tagihan: GET /api/wali-santri/tagihan
- Perizinan: GET/POST/PUT/DELETE /api/wali-santri/perizinan

Buatkan struktur proyek, konfigurasi, dan implementasi komponen-komponen utama.
```

## Prompt Detail per Bagian

### Setup Project

```
Buatkan langkah-langkah untuk membuat project Vue 3 dengan PWA support. Termasuk:
1. Perintah untuk membuat project Vue baru dengan Vue CLI
2. Konfigurasi Tailwind CSS
3. Setup Vuex store dan Vue Router
4. Konfigurasi axios untuk API calls
5. Konfigurasi PWA dan service worker
```

### Autentikasi

```
Buatkan implementasi sistem autentikasi untuk PWA wali santri, dengan:
1. Login page dengan form email dan password
2. Register page dengan form untuk wali santri baru
3. Vuex store untuk menyimpan state autentikasi
4. API service untuk login, register, dan logout
5. Route guards untuk proteksi halaman
6. Penyimpanan token di localStorage dengan vuex-persistedstate
```

### Dashboard dan Profil Santri

```
Buatkan implementasi dashboard wali santri dan halaman profil santri, dengan:
1. Dashboard yang menampilkan ringkasan data santri, tagihan, dan perizinan
2. Halaman profil santri yang menampilkan data pribadi santri
3. Komponen untuk menampilkan data akademik
4. Komponen untuk menampilkan data asrama
5. Layout yang responsif untuk desktop dan mobile
```

### Tagihan dan Pembayaran

```
Buatkan implementasi halaman tagihan dan pembayaran untuk wali santri, dengan:
1. List tagihan dengan status pembayaran (sudah bayar, belum bayar, sebagian)
2. Detail tagihan yang menampilkan rincian tagihan dan histori pembayaran
3. Filter tagihan berdasarkan status dan periode
4. Komponen untuk menampilkan ringkasan tagihan (total, sudah dibayar, sisa)
5. Grafik sederhana untuk visualisasi pembayaran
```

### Perizinan

```
Buatkan implementasi sistem perizinan santri untuk wali santri, dengan:
1. List perizinan dengan status (menunggu, disetujui, ditolak)
2. Form untuk membuat perizinan baru dengan upload bukti (file)
3. Detail perizinan yang menampilkan rincian dan status
4. Fungsi untuk edit dan delete perizinan (hanya untuk status 'menunggu')
5. Filter perizinan berdasarkan status dan jenis izin
```

### UI Components

```
Buatkan komponen UI reusable untuk PWA wali santri dengan Tailwind CSS, dengan:
1. Navbar dan sidebar responsif
2. Card component untuk menampilkan data
3. Form inputs (text, select, date, file upload)
4. Button dengan variasi (primary, secondary, danger)
5. Modal dialog untuk konfirmasi
6. Loading spinner dan skeleton loader
7. Badge dan status indicator
8. Alert dan notification
```

### Service Worker dan Offline Support

```
Buatkan konfigurasi PWA dan service worker untuk offline support di PWA wali santri, dengan:
1. Manifest.json dengan konfigurasi lengkap
2. Service worker untuk caching API responses
3. Offline fallback page
4. Strategi caching untuk assets dan API calls
5. Notifikasi update saat versi baru tersedia
6. Handling installable PWA
```

### API Services

```
Buatkan struktur API services untuk PWA wali santri menggunakan axios, dengan:
1. Base API configuration dengan interceptors untuk handling token
2. Auth API service (login, register, logout)
3. Santri API service (get list, get detail)
4. Tagihan API service (get list, get detail, get summary)
5. Perizinan API service (get list, get detail, create, update, delete)
6. Error handling dan retry logic
```

## Tips Penggunaan

1. Salin prompt di atas sesuai bagian yang ingin Anda kerjakan
2. Buka VSCode dan buat folder proyek baru
3. Buka Command Palette (`Ctrl+Shift+P`) dan pilih "Copilot: Open Chat"
4. Paste prompt tersebut, tekan Enter
5. Copilot akan memberikan saran dan kode untuk implementasi
6. Untuk implementasi komponen spesifik, tambahkan detail lebih lanjut ke prompt

Contoh prompt untuk komponen spesifik:

```
Buatkan Vue component untuk halaman login dengan Tailwind CSS yang memiliki form dengan validasi input email dan password, loading state, dan error message handling. Component ini harus memanggil action login dari Vuex store.
```

Dengan menggunakan prompt ini, GitHub Copilot akan membantu Anda menghasilkan kode untuk PWA Wali Santri SIMPels secara langsung di VSCode.
