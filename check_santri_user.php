<?php

use Illuminate\Support\Facades\DB;

// Cek santri dengan ID 203
$santri = DB::table('santris')->where('id', 203)->first();
echo "Santri 203:\n";
echo "  ID: {$santri->id}\n";
echo "  Nama: [{$santri->nama_santri}]\n";
echo "  User ID: [{$santri->user_id}]\n";
echo "  Email Orangtua: [{$santri->email_orangtua}]\n";

// Cek user dengan ID 2
$user = DB::table('users')->where('id', 2)->first();
echo "\nUser 2:\n";
echo "  ID: {$user->id}\n";
echo "  Email: {$user->email}\n";

// Update user_id santri kalau NULL
if (!$santri->user_id) {
    DB::table('santris')->where('id', 203)->update(['user_id' => 2]);
    echo "\n✅ Updated santri user_id to 2\n";
} else {
    echo "\n✅ Santri user_id sudah correct\n";
}
