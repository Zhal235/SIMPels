<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=simpels_db', 'root', '');
    $pdo->exec('DROP TABLE IF EXISTS keringanan_tagihans');
    echo "Tabel keringanan_tagihans berhasil dihapus\n";
} catch (Exception $e) {
    echo $e->getMessage();
}
