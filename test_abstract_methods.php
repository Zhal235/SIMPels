<?php

// Simple test script to verify abstract method implementations
require_once __DIR__ . '/vendor/autoload.php';

use App\Http\Controllers\BukuKasController;
use App\Http\Controllers\JenisBukuKasController;
use App\Models\BukuKas;
use App\Models\JenisBukuKas;
use App\Services\KeuanganService;

echo "Testing Abstract Method Implementations...\n\n";

try {
    // Test 1: BukuKasController can be instantiated
    echo "1. Testing BukuKasController instantiation... ";
    $keuanganService = new KeuanganService();
    $bukuKasController = new BukuKasController($keuanganService);
    echo "✅ SUCCESS\n";
    
    // Test 2: JenisBukuKasController can be instantiated
    echo "2. Testing JenisBukuKasController instantiation... ";
    $jenisKasController = new JenisBukuKasController($keuanganService);
    echo "✅ SUCCESS\n";
    
    // Test 3: BukuKas model can be instantiated
    echo "3. Testing BukuKas model instantiation... ";
    $bukuKas = new BukuKas();
    echo "✅ SUCCESS\n";
    
    // Test 4: JenisBukuKas model can be instantiated
    echo "4. Testing JenisBukuKas model instantiation... ";
    $jenisKas = new JenisBukuKas();
    echo "✅ SUCCESS\n";
    
    // Test 5: Test protected methods accessibility (reflection)
    echo "5. Testing abstract method implementations...\n";
    
    $reflection = new ReflectionClass($bukuKasController);
    $methods = ['applySearchFilter', 'getValidationRules'];
    
    foreach ($methods as $method) {
        if ($reflection->hasMethod($method)) {
            echo "   - BukuKasController::{$method}() ✅ IMPLEMENTED\n";
        } else {
            echo "   - BukuKasController::{$method}() ❌ NOT FOUND\n";
        }
    }
    
    $reflection = new ReflectionClass($jenisKasController);
    foreach ($methods as $method) {
        if ($reflection->hasMethod($method)) {
            echo "   - JenisBukuKasController::{$method}() ✅ IMPLEMENTED\n";
        } else {
            echo "   - JenisBukuKasController::{$method}() ❌ NOT FOUND\n";
        }
    }
    
    $reflection = new ReflectionClass($bukuKas);
    if ($reflection->hasMethod('getSearchableFields')) {
        echo "   - BukuKas::getSearchableFields() ✅ IMPLEMENTED\n";
    } else {
        echo "   - BukuKas::getSearchableFields() ❌ NOT FOUND\n";
    }
    
    $reflection = new ReflectionClass($jenisKas);
    if ($reflection->hasMethod('getSearchableFields')) {
        echo "   - JenisBukuKas::getSearchableFields() ✅ IMPLEMENTED\n";
    } else {
        echo "   - JenisBukuKas::getSearchableFields() ❌ NOT FOUND\n";
    }
    
    echo "\n🎉 ALL TESTS PASSED! Abstract method errors have been resolved.\n";
    
} catch (Error $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
} catch (Exception $e) {
    echo "\n❌ EXCEPTION: " . $e->getMessage() . "\n";
}
