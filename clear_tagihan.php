<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TagihanSantri;

echo "Menghapus semua data tagihan santri...\n";
$deleted = TagihanSantri::count();
TagihanSantri::query()->delete();
echo "Berhasil menghapus {$deleted} data tagihan.\n";
