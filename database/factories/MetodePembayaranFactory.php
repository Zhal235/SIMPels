<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MetodePembayaran>
 */
class MetodePembayaranFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_metode' => $this->faker->unique()->word(),
            'nomor_rekening' => $this->faker->optional()->bankAccountNumber(),
            'pemilik_rekening' => $this->faker->optional()->name(),
            'status' => $this->faker->randomElement(['aktif', 'nonaktif']),
        ];
    }
}
