<?php

// Test untuk memverifikasi pemisahan data antara tab Rutin dan Tunggakan

echo "=== TEST PEMISAHAN DATA TAB RUTIN DAN TUNGGAKAN ===\n\n";

// Test dengan santri ID 1
$santriId = 1;
$today = date('Y-m-d');

echo "Testing dengan Santri ID: $santriId\n";
echo "Tanggal hari ini: $today\n\n";

// Simulasi URL endpoints
$routinUrl = "http://127.0.0.1:8000/keuangan/pembayaran-santri/data/$santriId";
$tunggakanUrl = "http://127.0.0.1:8000/keuangan/pembayaran-santri/tunggakan/$santriId";

echo "URL untuk Tab Rutin: $routinUrl\n";
echo "URL untuk Tab Tunggakan: $tunggakanUrl\n\n";

echo "Silakan test manual di browser:\n";
echo "1. Buka halaman pembayaran santri\n";
echo "2. Pilih santri dengan ID $santriId\n";
echo "3. Cek tab 'Tagihan Rutin' - seharusnya hanya menampilkan tagihan tahun aktif yang belum jatuh tempo\n";
echo "4. Cek tab 'Tunggakan' - seharusnya menampilkan tagihan yang sudah jatuh tempo atau dari tahun sebelumnya\n\n";

echo "Expected behavior:\n";
echo "- Tab Rutin: Tagihan tahun 2024/2025 yang tanggal_jatuh_tempo >= $today atau tanggal_jatuh_tempo = NULL\n";
echo "- Tab Tunggakan: Tagihan yang tanggal_jatuh_tempo < $today atau dari tahun ajaran sebelumnya\n";

?>
