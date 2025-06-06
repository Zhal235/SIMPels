<?php

/**
 * Test script untuk memverifikasi fix edit jenis tagihan
 */

echo "=== TEST EDIT JENIS TAGIHAN ===\n\n";

// Test 1: Cek syntax controller
echo "1. Checking controller syntax...\n";
$result = shell_exec('php -l app/Http/Controllers/JenisTagihanController.php 2>&1');
if (strpos($result, 'No syntax errors') !== false) {
    echo "✓ Controller syntax OK\n";
} else {
    echo "✗ Controller syntax error: $result\n";
}

// Test 2: Cek apakah method edit ada dan accessible
echo "\n2. Checking controller method...\n";
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    
    $controller = new App\Http\Controllers\JenisTagihanController();
    $reflection = new ReflectionClass($controller);
    
    if ($reflection->hasMethod('edit')) {
        echo "✓ Edit method exists\n";
        
        $method = $reflection->getMethod('edit');
        if ($method->isPublic()) {
            echo "✓ Edit method is public\n";
        } else {
            echo "✗ Edit method is not public\n";
        }
    } else {
        echo "✗ Edit method not found\n";
    }
} catch (Exception $e) {
    echo "✗ Error checking method: " . $e->getMessage() . "\n";
}

// Test 3: Cek model relationships
echo "\n3. Checking model relationships...\n";
try {
    $jenisTagihan = new App\Models\JenisTagihan();
    $bukuKas = new App\Models\BukuKas();
    
    $reflection = new ReflectionClass($jenisTagihan);
    if ($reflection->hasMethod('bukuKas')) {
        echo "✓ JenisTagihan has bukuKas relationship\n";
    } else {
        echo "✗ JenisTagihan missing bukuKas relationship\n";
    }
    
    $reflection = new ReflectionClass($bukuKas);
    if ($reflection->hasMethod('jenisTagihan')) {
        echo "✓ BukuKas has jenisTagihan relationship\n";
    } else {
        echo "? BukuKas might not have reverse relationship (not critical)\n";
    }
} catch (Exception $e) {
    echo "✗ Error checking relationships: " . $e->getMessage() . "\n";
}

// Test 4: Cek database structure dan sample data
echo "\n4. Checking database structure and sample data...\n";
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    
    // Check jenis_tagihans table
    $columns = Illuminate\Support\Facades\DB::select('DESCRIBE jenis_tagihans');
    $columnNames = array_column($columns, 'Field');
    
    $requiredColumns = ['id', 'nama', 'buku_kas_id', 'kategori_tagihan', 'is_bulanan', 'nominal', 'is_nominal_per_kelas'];
    $missingColumns = array_diff($requiredColumns, $columnNames);
    
    if (empty($missingColumns)) {
        echo "✓ All required columns exist in jenis_tagihans\n";
    } else {
        echo "✗ Missing columns in jenis_tagihans: " . implode(', ', $missingColumns) . "\n";
    }
    
    // Check buku_kas table
    $columns = Illuminate\Support\Facades\DB::select('DESCRIBE buku_kas');
    $columnNames = array_column($columns, 'Field');
    
    $requiredColumns = ['id', 'nama_kas', 'kode_kas'];
    $missingColumns = array_diff($requiredColumns, $columnNames);
    
    if (empty($missingColumns)) {
        echo "✓ All required columns exist in buku_kas\n";
    } else {
        echo "✗ Missing columns in buku_kas: " . implode(', ', $missingColumns) . "\n";
    }
    
    // Check sample data
    $jenisTagihanCount = Illuminate\Support\Facades\DB::table('jenis_tagihans')->count();
    $bukuKasCount = Illuminate\Support\Facades\DB::table('buku_kas')->count();
    
    echo "✓ Found $jenisTagihanCount jenis tagihan records\n";
    echo "✓ Found $bukuKasCount buku kas records\n";
    
    // Check relationships
    $withRelation = Illuminate\Support\Facades\DB::table('jenis_tagihans')
        ->join('buku_kas', 'jenis_tagihans.buku_kas_id', '=', 'buku_kas.id')
        ->count();
    
    echo "✓ Found $withRelation jenis tagihan with buku kas relations\n";
    
} catch (Exception $e) {
    echo "✗ Error checking database: " . $e->getMessage() . "\n";
}

// Test 5: Cek view file
echo "\n5. Checking view file...\n";
$viewFile = 'resources/views/keuangan/jenis_tagihan/index.blade.php';
if (file_exists($viewFile)) {
    echo "✓ View file exists\n";
    
    $content = file_get_contents($viewFile);
    
    // Check for required JavaScript functions
    if (strpos($content, 'function openEditModal') !== false) {
        echo "✓ openEditModal function found\n";
    } else {
        echo "✗ openEditModal function not found\n";
    }
    
    if (strpos($content, 'function submitEditForm') !== false) {
        echo "✓ submitEditForm function found\n";
    } else {
        echo "✗ submitEditForm function not found\n";
    }
    
    // Check for edit modal HTML
    if (strpos($content, 'id="editModal"') !== false) {
        echo "✓ Edit modal HTML found\n";
    } else {
        echo "✗ Edit modal HTML not found\n";
    }
    
    // Check for buku kas dropdown update
    if (strpos($content, 'bukuKasList') !== false) {
        echo "✓ BukuKas list handling found\n";
    } else {
        echo "✗ BukuKas list handling not found\n";
    }
    
} else {
    echo "✗ View file not found\n";
}

echo "\n=== TEST COMPLETED ===\n";
echo "If all tests pass, the edit functionality should work correctly.\n";
echo "To test manually:\n";
echo "1. Open the Jenis Tagihan page\n";
echo "2. Click edit button on any row\n";
echo "3. Check browser console for debug logs\n";
echo "4. Verify that data loads correctly in the modal\n";
echo "5. Try submitting changes\n\n";
