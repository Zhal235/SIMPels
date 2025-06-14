<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->boot();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING SANTRI DATA ===" . PHP_EOL;
$santris = DB::table('santris')->where('nama_santri', 'Ahmad Santri')->get();
foreach ($santris as $santri) {
    echo "ID: " . $santri->id . PHP_EOL;
    echo "Nama: " . $santri->nama_santri . PHP_EOL;
    echo "NIS: " . $santri->nis . PHP_EOL;
    echo "User ID: " . ($santri->user_id ?? 'NULL') . PHP_EOL;
    echo "Wali Santri ID: " . ($santri->wali_santri_id ?? 'NULL') . PHP_EOL;
    echo "---" . PHP_EOL;
}

echo "=== CHECKING USER ID 2 ===" . PHP_EOL;
$user = DB::table('users')->where('id', 2)->first();
if ($user) {
    echo "User ID: " . $user->id . PHP_EOL;
    echo "Email: " . $user->email . PHP_EOL;
    echo "Name: " . $user->name . PHP_EOL;
}

echo "=== CHECKING RELASI ===" . PHP_EOL;
$santrisForUser2 = DB::table('santris')->where('user_id', 2)->orWhere('wali_santri_id', 2)->get();
echo "Santri yang terhubung dengan user ID 2: " . count($santrisForUser2) . PHP_EOL;
foreach ($santrisForUser2 as $santri) {
    echo "- " . $santri->nama_santri . " (user_id: " . ($santri->user_id ?? 'NULL') . ", wali_santri_id: " . ($santri->wali_santri_id ?? 'NULL') . ")" . PHP_EOL;
}
