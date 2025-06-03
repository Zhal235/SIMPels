<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KategoriKeuangan extends Model
{
    use HasFactory;

    protected $table = 'keuangan_kategoris'; // Ini akan diubah nanti jika diperlukan untuk JenisPembayaran

    protected $fillable = [
        'nama_kategori', // Akan menjadi nama jenis pembayaran, contoh: SPP, Uang Pangkal
        'deskripsi', // Bisa digunakan untuk kategori: Pembayaran Rutin / Insidental
        'nominal_tagihan' // Nominal yang harus dibayar
    ];

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'keuangan_kategori_id');
    }
}

/*
|--------------------------------------------------------------------------
| TODO: Buat model baru JenisPembayaran.php dan migrasinya.
| File ini (KategoriKeuangan.php) akan dimodifikasi atau dihapus tergantung kebutuhan setelah JenisPembayaran dibuat.
|--------------------------------------------------------------------------
*/
