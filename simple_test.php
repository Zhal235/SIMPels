<?php
echo "Testing Transaksi model table name:\n";
require 'vendor/autoload.php';

use App\Models\Transaksi;

$transaksi = new Transaksi();
echo "Table: " . $transaksi->getTable() . "\n";
echo "Done\n";
