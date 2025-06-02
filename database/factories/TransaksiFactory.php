<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Santri;
use App\Models\KategoriKeuangan;
use App\Models\MetodePembayaran;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaksi>
 */
class TransaksiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'santri_id' => Santri::factory(),
            'keuangan_kategori_id' => KategoriKeuangan::factory(),
            'keuangan_metode_id' => MetodePembayaran::factory(),
            'tipe_pembayaran' => $this->faker->randomElement(['tunai', 'transfer', 'rfid']),
            'nominal' => $this->faker->numberBetween(50000, 500000),
            'tanggal' => $this->faker->dateTimeThisMonth(),
            'keterangan' => $this->faker->sentence(),
            'user_id' => null, // Bisa diisi jika transaksi dicatat oleh user tertentu
        ];
    }
}
