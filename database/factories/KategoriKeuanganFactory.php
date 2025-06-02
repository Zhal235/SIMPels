<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KategoriKeuangan>
 */
class KategoriKeuanganFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_kategori' => $this->faker->unique()->words(2, true),
            'jenis_kategori' => $this->faker->randomElement(['pemasukan', 'pengeluaran']),
        ];
    }
}
