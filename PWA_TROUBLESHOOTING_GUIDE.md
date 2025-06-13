# Panduan Pemecahan Masalah PWA Wali Santri

Dokumen ini berisi langkah-langkah untuk menyelesaikan masalah umum yang mungkin terjadi saat mengembangkan atau menggunakan PWA Wali Santri.

## 1. Masalah CORS (Cross-Origin Resource Sharing)

Jika PWA tidak dapat mengakses API dengan error CORS, lakukan langkah-langkah berikut:

### Di Backend (Laravel):

1. **Pastikan middleware CORS sudah terpasang**:
   - Periksa file `app/Http/Kernel.php` dan pastikan middleware CORS sudah terdaftar di `$middleware` global.
   - Middleware yang perlu ada: `\Illuminate\Http\Middleware\HandleCors::class` dan `\App\Http\Middleware\Cors::class`.

2. **Periksa konfigurasi CORS**:
   - Pastikan file `config/cors.php` mengizinkan origin, method, dan header yang dibutuhkan.
   - Setting yang direkomendasikan untuk pengembangan:
     ```php
     'allowed_origins' => ['*'],
     'allowed_methods' => ['*'],
     'allowed_headers' => ['*'],
     'supports_credentials' => true,
     ```

3. **Tambahkan route OPTIONS untuk preflight request**:
   - Pastikan route berikut sudah ada di `routes/api.php`:
     ```php
     Route::options('/{any}', function() {
         return response('', 200)
             ->header('Access-Control-Allow-Origin', '*')
             ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
             ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN');
     })->where('any', '.*');
     ```

4. **Restart server Laravel**:
   ```
   php artisan cache:clear
   php artisan config:clear
   php artisan serve
   ```

### Di Frontend (PWA):

1. **Periksa URL API**:
   - Pastikan URL API yang digunakan di PWA sesuai dengan URL server Laravel.
   - Contoh pengaturan di `.env` PWA:
     ```
     VITE_API_BASE_URL=http://localhost:8000/api
     ```

2. **Tambahkan header di request**:
   - Pastikan request API dari PWA menyertakan header yang diperlukan:
     ```js
     fetch(`${apiUrl}/endpoint`, {
       method: 'POST',
       headers: {
         'Content-Type': 'application/json',
         'Accept': 'application/json',
         'Authorization': `Bearer ${token}` // Jika diperlukan
       },
       body: JSON.stringify(data)
     })
     ```

3. **Gunakan Axios dengan konfigurasi yang tepat**:
   ```js
   // Konfigurasi Axios
   const api = axios.create({
     baseURL: import.meta.env.VITE_API_BASE_URL,
     withCredentials: true, // Jika menggunakan cookies
     headers: {
       'Content-Type': 'application/json',
       'Accept': 'application/json'
     }
   });

   // Tambahkan interceptor untuk token
   api.interceptors.request.use(config => {
     const token = localStorage.getItem('token');
     if (token) {
       config.headers.Authorization = `Bearer ${token}`;
     }
     return config;
   });
   ```

## 2. Masalah Autentikasi

Jika PWA tidak dapat login atau autentikasi gagal:

1. **Periksa token di localStorage**:
   - Buka DevTools > Application > Storage > Local Storage
   - Pastikan token tersimpan dengan benar

2. **Periksa format token pada header Authorization**:
   - Format yang benar: `Bearer {token}`
   - Pastikan ada spasi setelah kata "Bearer"

3. **Periksa konfigurasi Sanctum**:
   - File `config/sanctum.php` harus memiliki domain PWA di `stateful` domains
   - Jika menggunakan domain berbeda untuk PWA dan API, tambahkan domain PWA ke `stateful`

4. **Uji API secara langsung**:
   - Gunakan Postman untuk menguji API secara langsung
   - Bandingkan respons dari Postman dengan respons yang diterima PWA

## 3. Masalah Terkait Data

Jika data dari API tidak muncul di PWA:

1. **Periksa struktur respons API**:
   - Gunakan DevTools > Network untuk melihat respons API
   - Pastikan struktur data sesuai dengan yang diharapkan oleh komponen PWA

2. **Periksa transformasi data di controller API**:
   - Pastikan controller mengembalikan format data yang konsisten
   - Tambahkan log di controller untuk memantau permintaan dan respons

3. **Verifikasi relasi model di API**:
   - Pastikan relasi antar model sudah benar (misalnya Santri ke Kelas)
   - Gunakan Tinker untuk menguji relasi secara langsung

## 4. Masalah Terkait Deployment

Jika masalah terjadi saat deployment ke server produksi:

1. **Konfigurasi server produksi**:
   - Pastikan server memiliki mod_headers diaktifkan (untuk Apache)
   - Periksa file .htaccess untuk aturan CORS

2. **Domain dan SSL**:
   - Pastikan kedua aplikasi (API dan PWA) menggunakan protokol yang sama (HTTP atau HTTPS)
   - Jika salah satu menggunakan HTTPS, pastikan keduanya menggunakan HTTPS

3. **Firewall dan proxy**:
   - Periksa apakah firewall memblokir request cross-origin
   - Jika menggunakan reverse proxy, pastikan header CORS diteruskan dengan benar

## 5. Debug dan Log

Untuk melakukan debugging:

1. **Tambahkan log di backend**:
   ```php
   \Log::info('Request data:', $request->all());
   \Log::info('Response data:', $response->getData(true));
   ```

2. **Tambahkan log di frontend**:
   ```js
   console.log('API Request:', { url, method, data });
   console.log('API Response:', response);
   ```

3. **Gunakan Network Monitor di DevTools**:
   - Pilih tab Network dan filter request XHR/Fetch
   - Periksa header request dan response

4. **Gunakan Symfony Debug Toolbar** (untuk development di Laravel):
   - Aktifkan debug toolbar untuk melihat query database dan request API

## Kontak Dukungan

Jika masalah masih berlanjut setelah mencoba langkah-langkah di atas, silakan hubungi tim pengembang di:
- Email: support@simpels.com
- Telepon: 021-1234567
