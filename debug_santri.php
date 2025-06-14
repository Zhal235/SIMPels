<?php

$user = App\Models\User::where('email', 'wali@test.com')->first();
echo "User ID: " . $user->id . PHP_EOL;
echo "User email: " . $user->email . PHP_EOL;
echo "User roles: " . $user->roles->pluck('name')->join(', ') . PHP_EOL;

$santris = App\Models\Santri::where('email_orangtua', 'wali@test.com')->get();
echo "Santris by email_orangtua: " . $santris->count() . PHP_EOL;
foreach($santris as $santri) {
    echo "  - " . $santri->nama . " (ID: " . $santri->id . ")" . PHP_EOL;
}

$waliSantris = App\Models\WaliSantri::where('user_id', $user->id)->get();
echo "WaliSantri records: " . $waliSantris->count() . PHP_EOL;
foreach($waliSantris as $ws) {
    echo "  - " . $ws->name . " (user_id: " . $ws->user_id . ")" . PHP_EOL;
}
