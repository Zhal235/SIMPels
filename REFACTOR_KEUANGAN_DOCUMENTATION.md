# Dokumentasi Refactor Keuangan Controllers

## Overview
Refactor ini bertujuan untuk mengurangi duplikasi kode antara `BukuKasController` dan `JenisBukuKasController` dengan menggunakan:

1. **BaseKeuanganController** - Base controller untuk shared logic
2. **KeuanganManagementTrait** - Trait untuk shared model functionality
3. **KeuanganService** - Service layer untuk business logic

## Struktur Baru

### 1. BaseKeuanganController
File: `app/Http/Controllers/BaseKeuanganController.php`

**Fitur yang disediakan:**
- `applyCommonFilters()` - Filter umum (status, tanggal)
- `applySearch()` - Search functionality untuk multiple columns
- `validateInput()` - Centralized validation
- `successResponse()` - Consistent success response format
- `errorResponse()` - Consistent error response format
- `jsonResponse()` - Smart JSON/view response handler

**Cara Penggunaan:**
```php
class YourController extends BaseKeuanganController
{
    public function index(Request $request)
    {
        $query = Model::query();
        
        // Apply common filters (status, date_from, date_to)
        $query = $this->applyCommonFilters($query, $request);
        
        // Apply search
        $query = $this->applySearch($query, $request->search, ['nama', 'kode']);
        
        $data = $query->paginate(10);
        return view('your.view', compact('data'));
    }
    
    public function store(Request $request)
    {
        $validated = $this->validateInput($request, [
            'nama' => 'required|string|max:255'
        ]);
        
        Model::create($validated);
        
        return $this->successResponse(
            $request, 
            'Data berhasil disimpan!',
            'your.route.index'
        );
    }
}
```

### 2. KeuanganManagementTrait
File: `app/Traits/KeuanganManagementTrait.php`

**Fitur yang disediakan:**
- `scopeActive()` - Scope untuk data aktif
- `scopeInactive()` - Scope untuk data tidak aktif
- `scopeByStatus()` - Scope dengan parameter status
- `scopeCreatedBetween()` - Scope untuk filter tanggal
- `getStatusLabelAttribute()` - Accessor untuk label status
- `getFormattedCreatedAtAttribute()` - Accessor untuk tanggal terformat

**Cara Penggunaan:**
```php
use App\Traits\KeuanganManagementTrait;

class YourModel extends Model
{
    use KeuanganManagementTrait;
    
    // Model ini otomatis memiliki semua scope dan accessor dari trait
}

// Penggunaan scope:
YourModel::active()->get();
YourModel::byStatus(true)->get();
YourModel::createdBetween('2024-01-01', '2024-12-31')->get();
```

### 3. KeuanganService
File: `app/Services/KeuanganService.php`

**Fitur yang disediakan:**
- `getBukuKasStatistics()` - Statistik Buku Kas
- `getJenisKasUsageCount()` - Usage count untuk Jenis Kas
- `canDeleteBukuKas()` / `canDeleteJenisKas()` - Validation sebelum delete
- `getBukuKasForDropdown()` / `getJenisKasForDropdown()` - Data untuk dropdown
- `validateBukuKasRules()` / `validateJenisKasRules()` - Business rules validation
- `getFinancialSummary()` - Summary data keuangan
- `createTransaksiKas()` - Create transaksi dengan update saldo

**Cara Penggunaan:**
```php
class YourController extends BaseKeuanganController
{
    protected $keuanganService;

    public function __construct(KeuanganService $keuanganService)
    {
        $this->keuanganService = $keuanganService;
    }
    
    public function index()
    {
        $statistics = $this->keuanganService->getBukuKasStatistics();
        $summary = $this->keuanganService->getFinancialSummary();
        // ...
    }
    
    public function destroy($id)
    {
        $model = Model::findOrFail($id);
        $deleteCheck = $this->keuanganService->canDeleteBukuKas($model);
        
        if (!$deleteCheck['can_delete']) {
            return $this->errorResponse(
                implode(', ', $deleteCheck['messages']),
                400
            );
        }
        
        $model->delete();
        return $this->successJsonResponse('Data berhasil dihapus!');
    }
}
```

## Benefits Setelah Refactor

### 1. Reduced Code Duplication
- **Sebelum**: Setiap controller memiliki logic sendiri untuk filtering, search, validation, response
- **Setelah**: Logic umum dipindahkan ke base controller dan trait

### 2. Consistent Response Format
- **Sebelum**: Response format berbeda-beda antar controller
- **Setelah**: Semua response menggunakan format yang konsisten

### 3. Centralized Business Logic
- **Sebelum**: Business rules tersebar di berbagai controller
- **Setelah**: Business logic terpusat di service layer

### 4. Improved Maintainability
- **Sebelum**: Perubahan logic harus dilakukan di multiple files
- **Setelah**: Perubahan cukup dilakukan di satu tempat

### 5. Better Testing
- **Sebelum**: Sulit untuk test karena logic tersebar
- **Setelah**: Business logic terpisah dan mudah di-test

## Migration Guide

Untuk menggunakan struktur baru ini pada controller lain:

1. **Extend BaseKeuanganController**:
   ```php
   class NewController extends BaseKeuanganController
   ```

2. **Use KeuanganManagementTrait di Model**:
   ```php
   use App\Traits\KeuanganManagementTrait;
   class NewModel extends Model {
       use KeuanganManagementTrait;
   }
   ```

3. **Inject KeuanganService**:
   ```php
   public function __construct(KeuanganService $keuanganService)
   {
       $this->keuanganService = $keuanganService;
   }
   ```

4. **Replace duplicate logic dengan method dari base controller**

## Performance Impact

- **Positive**: Reduced memory usage karena eliminasi duplicate code
- **Neutral**: Dependency injection overhead minimal
- **Positive**: Better caching possibilities dengan centralized logic

## Future Improvements

1. **Add Caching Layer** di service untuk queries yang sering dipakai
2. **Add Event Listeners** untuk audit logging
3. **Add Interface** untuk service layer supaya lebih testable
4. **Add Validation Rules Class** untuk rules yang complex

## Testing

Pastikan untuk test:
1. Controller methods masih berfungsi dengan benar
2. Response format tetap konsisten
3. Business rules validation masih berjalan
4. Error handling masih proper

```bash
# Run tests untuk memastikan tidak ada breaking changes
php artisan test --filter=Keuangan
```
