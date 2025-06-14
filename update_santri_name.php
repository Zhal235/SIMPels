<?php

// Update nama santri yang kosong
use Illuminate\Support\Facades\DB;

DB::table('santris')->where('id', 203)->update([
    'nama_santri' => 'Ahmad Santri'
]);

echo "✅ Nama santri updated to 'Ahmad Santri'\n";

// Verify update
$santri = DB::table('santris')->where('id', 203)->first();
echo "Verified - ID: {$santri->id}, Nama: [{$santri->nama_santri}]\n";
