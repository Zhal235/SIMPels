<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asrama>
 */
class AsramaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode' => $this->faker->unique()->bothify('ASR-???###'),
            'nama' => $this->faker->unique()->word() . ' Asrama',
            'wali_asrama' => $this->faker->name(), // Sesuai migrasi, ini adalah string nama
        ];
    }
}
