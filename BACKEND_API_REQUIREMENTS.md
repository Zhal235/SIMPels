# PWA Wali Santri - Backend API Requirements

## 📋 Overview

PWA Wali Santri sudah 100% ready dan terintegrasi dengan sistem SIMPels. Namun, beberapa endpoint API masih missing di backend Laravel. Dokumen ini berisi spesifikasi lengkap untuk implementasi endpoint yang dibutuhkan.

## 🚨 Current Issues

1. **Missing API Endpoints** - Endpoint untuk data santri, tagihan, dan perizinan belum diimplementasikan
2. **Database Relations** - Relasi antara `wali_santri` dan `santri` perlu diperbaiki
3. **Data Integration** - PWA tidak bisa menampilkan data real karena endpoint belum ada

## 🎯 Required API Endpoints

### 1. GET /api/wali-santri/santri

**Purpose:** Mengambil daftar santri yang dimiliki oleh wali santri yang sedang login

**Authentication:** Bearer Token (JWT)

**Response Format:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nama": "Ahmad Abdullah",
      "nis": "2024001",
      "nisn": "0123456789",
      "kelas": "XII IPA 1",
      "jurusan": "IPA",
      "asrama": "Al-Khulafa",
      "kamar": "A-12",
      "status": "aktif",
      "tempat_lahir": "Jakarta",
      "tanggal_lahir": "2006-05-15",
      "jenis_kelamin": "L",
      "agama": "Islam",
      "alamat": "Jl. Merdeka No. 123, Jakarta",
      "tahun_masuk": "2021",
      "musyrif": "Ustadz Abdullah",
      "hp_musyrif": "081234567890",
      "nama_ayah": "Budi Abdullah",
      "pekerjaan_ayah": "Wiraswasta",
      "hp_ayah": "081234567891",
      "nama_ibu": "Siti Aminah",
      "pekerjaan_ibu": "Ibu Rumah Tangga",
      "hp_ibu": "081234567892",
      "golongan_darah": "A",
      "tinggi_badan": 165,
      "berat_badan": 55
    }
  ]
}
```

**Implementation Notes:**
- Filter santri berdasarkan `wali_santri_id`
- Return array kosong jika wali tidak punya santri
- Include semua field yang dibutuhkan PWA

---

### 2. GET /api/wali-santri/tagihan

**Purpose:** Mengambil daftar tagihan santri untuk wali yang sedang login

**Authentication:** Bearer Token (JWT)

**Query Parameters (Optional):**
- `santri_id` - Filter berdasarkan santri tertentu
- `status` - Filter berdasarkan status tagihan (lunas, belum_lunas, sebagian)
- `periode` - Filter berdasarkan periode

**Response Format:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "santri_id": 1,
      "santri_nama": "Ahmad Abdullah",
      "jenis_tagihan": "SPP",
      "periode": "Desember 2024",
      "jumlah": 500000,
      "sudah_bayar": 300000,
      "sisa_tagihan": 200000,
      "status": "sebagian",
      "jatuh_tempo": "2024-12-10",
      "keterangan": "Sumbangan Pembinaan Pendidikan",
      "histori_pembayaran": [
        {
          "id": 1,
          "jumlah": 300000,
          "tanggal": "2024-12-08",
          "metode": "Transfer Bank",
          "keterangan": "Pembayaran sebagian SPP"
        }
      ]
    }
  ],
  "summary": {
    "total_tagihan": 800000,
    "sudah_bayar": 300000,
    "belum_bayar": 500000,
    "jumlah_tagihan_tertunggak": 2
  }
}
```

---

### 3. GET /api/wali-santri/perizinan

**Purpose:** Mengambil daftar perizinan santri untuk wali yang sedang login

**Authentication:** Bearer Token (JWT)

**Query Parameters (Optional):**
- `santri_id` - Filter berdasarkan santri tertentu
- `status` - Filter berdasarkan status (menunggu, disetujui, ditolak)

**Response Format:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "santri_id": 1,
      "santri_nama": "Ahmad Abdullah",
      "jenis_izin": "pulang",
      "keperluan": "Acara keluarga",
      "tanggal_mulai": "2024-12-20",
      "tanggal_selesai": "2024-12-22",
      "durasi_hari": 3,
      "status": "disetujui",
      "keterangan": "Menghadiri pernikahan saudara",
      "catatan_admin": "Disetujui, harap kembali tepat waktu",
      "lampiran": null,
      "created_at": "2024-12-15T10:00:00Z",
      "updated_at": "2024-12-16T14:30:00Z"
    }
  ]
}
```

---

### 4. POST /api/wali-santri/perizinan

**Purpose:** Membuat perizinan baru

**Authentication:** Bearer Token (JWT)

**Request Body:**
```json
{
  "santri_id": 1,
  "jenis_izin": "sakit",
  "keperluan": "Berobat ke dokter",
  "tanggal_mulai": "2024-12-18",
  "tanggal_selesai": "2024-12-18",
  "keterangan": "Demam tinggi, perlu pemeriksaan dokter",
  "lampiran": "file_upload_optional"
}
```

**Response Format:**
```json
{
  "success": true,
  "message": "Perizinan berhasil diajukan",
  "data": {
    "id": 2,
    "santri_id": 1,
    "jenis_izin": "sakit",
    "keperluan": "Berobat ke dokter",
    "tanggal_mulai": "2024-12-18",
    "tanggal_selesai": "2024-12-18",
    "status": "menunggu",
    "keterangan": "Demam tinggi, perlu pemeriksaan dokter",
    "created_at": "2024-12-17T08:30:00Z"
  }
}
```

---

### 5. PUT /api/wali-santri/perizinan/{id}

**Purpose:** Update perizinan yang statusnya masih "menunggu"

**Authentication:** Bearer Token (JWT)

**Request Body:** Same as POST

**Response Format:** Same as POST

---

### 6. DELETE /api/wali-santri/perizinan/{id}

**Purpose:** Hapus perizinan yang statusnya masih "menunggu"

**Authentication:** Bearer Token (JWT)

**Response Format:**
```json
{
  "success": true,
  "message": "Perizinan berhasil dihapus"
}
```

---

## 🗄️ Database Schema Requirements

### Tables Structure

#### 1. wali_santri (existing)
```sql
- id (primary key)
- name
- email
- password
- phone
- address
- created_at
- updated_at
```

#### 2. santri (update required)
```sql
- id (primary key)
- wali_santri_id (foreign key) -- ADD THIS
- nama
- nis
- nisn
- kelas
- jurusan
- asrama
- kamar
- status
- tempat_lahir
- tanggal_lahir
- jenis_kelamin
- agama
- alamat
- tahun_masuk
- musyrif
- hp_musyrif
- nama_ayah
- pekerjaan_ayah
- hp_ayah
- nama_ibu
- pekerjaan_ibu
- hp_ibu
- golongan_darah
- tinggi_badan
- berat_badan
- created_at
- updated_at
```

#### 3. tagihan (update required)
```sql
- id (primary key)
- santri_id (foreign key)
- jenis_tagihan
- periode
- jumlah
- sudah_bayar
- status (lunas, belum_lunas, sebagian)
- jatuh_tempo
- keterangan
- created_at
- updated_at
```

#### 4. pembayaran_tagihan
```sql
- id (primary key)
- tagihan_id (foreign key)
- jumlah
- tanggal
- metode
- keterangan
- created_at
- updated_at
```

#### 5. perizinan
```sql
- id (primary key)
- santri_id (foreign key)
- jenis_izin (pulang, sakit, keperluan_lain)
- keperluan
- tanggal_mulai
- tanggal_selesai
- status (menunggu, disetujui, ditolak)
- keterangan
- catatan_admin
- lampiran
- created_at
- updated_at
```

---

## 🔗 Model Relations

### WaliSantri Model
```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class WaliSantri extends Authenticatable implements JWTSubject
{
    protected $table = 'wali_santri';
    
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'address'
    ];
    
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    // JWT methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    
    public function getJWTCustomClaims()
    {
        return [];
    }
    
    // Relations
    public function santri()
    {
        return $this->hasMany(Santri::class, 'wali_santri_id');
    }
    
    public function tagihan()
    {
        return $this->hasManyThrough(Tagihan::class, Santri::class, 'wali_santri_id', 'santri_id');
    }
    
    public function perizinan()
    {
        return $this->hasManyThrough(Perizinan::class, Santri::class, 'wali_santri_id', 'santri_id');
    }
}
```

### Santri Model
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
    protected $table = 'santri';
    
    protected $fillable = [
        'wali_santri_id', 'nama', 'nis', 'nisn', 'kelas', 'jurusan',
        'asrama', 'kamar', 'status', 'tempat_lahir', 'tanggal_lahir',
        'jenis_kelamin', 'agama', 'alamat', 'tahun_masuk', 'musyrif',
        'hp_musyrif', 'nama_ayah', 'pekerjaan_ayah', 'hp_ayah',
        'nama_ibu', 'pekerjaan_ibu', 'hp_ibu', 'golongan_darah',
        'tinggi_badan', 'berat_badan'
    ];
    
    protected $dates = ['tanggal_lahir'];
    
    // Relations
    public function waliSantri()
    {
        return $this->belongsTo(WaliSantri::class, 'wali_santri_id');
    }
    
    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'santri_id');
    }
    
    public function perizinan()
    {
        return $this->hasMany(Perizinan::class, 'santri_id');
    }
}
```

---

## 🎛️ Controller Implementation

### WaliSantriController
```php
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use App\Models\Tagihan;
use App\Models\Perizinan;
use Illuminate\Http\Request;

class WaliSantriController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:wali-santri');
    }
    
    public function getSantri()
    {
        $wali = auth('wali-santri')->user();
        $santri = $wali->santri()->get();
        
        return response()->json([
            'success' => true,
            'data' => $santri
        ]);
    }
    
    public function getTagihan(Request $request)
    {
        $wali = auth('wali-santri')->user();
        
        $query = $wali->tagihan()->with(['santri:id,nama']);
        
        if ($request->santri_id) {
            $query->where('santri_id', $request->santri_id);
        }
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        $tagihan = $query->get();
        
        // Calculate summary
        $summary = [
            'total_tagihan' => $tagihan->sum('jumlah'),
            'sudah_bayar' => $tagihan->sum('sudah_bayar'),
            'belum_bayar' => $tagihan->sum(function($t) {
                return $t->jumlah - $t->sudah_bayar;
            }),
            'jumlah_tagihan_tertunggak' => $tagihan->where('status', '!=', 'lunas')->count()
        ];
        
        return response()->json([
            'success' => true,
            'data' => $tagihan,
            'summary' => $summary
        ]);
    }
    
    public function getPerizinan(Request $request)
    {
        $wali = auth('wali-santri')->user();
        
        $query = $wali->perizinan()->with(['santri:id,nama']);
        
        if ($request->santri_id) {
            $query->where('santri_id', $request->santri_id);
        }
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        $perizinan = $query->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $perizinan
        ]);
    }
    
    public function createPerizinan(Request $request)
    {
        $request->validate([
            'santri_id' => 'required|exists:santri,id',
            'jenis_izin' => 'required|string',
            'keperluan' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'required|string'
        ]);
        
        $wali = auth('wali-santri')->user();
        
        // Verify santri belongs to this wali
        $santri = $wali->santri()->find($request->santri_id);
        if (!$santri) {
            return response()->json([
                'success' => false,
                'message' => 'Santri tidak ditemukan'
            ], 404);
        }
        
        $perizinan = Perizinan::create([
            'santri_id' => $request->santri_id,
            'jenis_izin' => $request->jenis_izin,
            'keperluan' => $request->keperluan,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'keterangan' => $request->keterangan,
            'status' => 'menunggu'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Perizinan berhasil diajukan',
            'data' => $perizinan
        ], 201);
    }
}
```

---

## 🛣️ Routes Configuration

### api.php
```php
<?php

use App\Http\Controllers\API\WaliSantriController;

Route::prefix('wali-santri')->group(function () {
    // Authentication routes (existing)
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    
    // Protected routes
    Route::middleware('auth:wali-santri')->group(function () {
        Route::get('user', [AuthController::class, 'user']);
        Route::post('logout', [AuthController::class, 'logout']);
        
        // Data endpoints
        Route::get('santri', [WaliSantriController::class, 'getSantri']);
        Route::get('tagihan', [WaliSantriController::class, 'getTagihan']);
        
        // Perizinan CRUD
        Route::get('perizinan', [WaliSantriController::class, 'getPerizinan']);
        Route::post('perizinan', [WaliSantriController::class, 'createPerizinan']);
        Route::put('perizinan/{id}', [WaliSantriController::class, 'updatePerizinan']);
        Route::delete('perizinan/{id}', [WaliSantriController::class, 'deletePerizinan']);
    });
});
```

---

## 🧪 Testing Guide

### 1. Test Authentication
```bash
POST /api/wali-santri/login
Content-Type: application/json

{
  "email": "wali@example.com",
  "password": "password123"
}
```

### 2. Test Data Endpoints
```bash
# Get santri data
GET /api/wali-santri/santri
Authorization: Bearer {token}

# Get tagihan data
GET /api/wali-santri/tagihan
Authorization: Bearer {token}

# Get perizinan data
GET /api/wali-santri/perizinan
Authorization: Bearer {token}
```

### 3. Test PWA Integration
1. Login ke PWA dengan credentials yang sama
2. Verify data santri muncul di dashboard
3. Check profile page menampilkan data yang benar
4. Test create perizinan functionality

---

## 🎯 Success Criteria

✅ **Backend Implementation Complete When:**
1. All API endpoints return expected response format
2. Database relations working properly
3. Authentication properly integrated
4. PWA can fetch and display real data
5. No more mock data shown in PWA

✅ **PWA Integration Success When:**
- Login dengan user A menampilkan santri A
- Login dengan user B menampilkan santri B  
- Tagihan dan perizinan sesuai dengan santri yang benar
- No more "Ahmad Santoso" mock data

---

## 📞 Support

Jika ada pertanyaan tentang implementasi ini, silakan hubungi tim frontend atau lihat dokumentasi PWA di repository.

**PWA Repository:** `PWA_WALSAN`  
**Current Status:** ✅ Frontend 100% Ready, ⏳ Backend API Required
