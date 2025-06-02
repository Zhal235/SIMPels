<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kelas>
 */
class KelasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode' => $this->faker->unique()->bothify('KLS-???###'),
            'nama' => $this->faker->unique()->numerify('Kelas ##'),
            'tingkat' => $this->faker->randomElement(['I', 'II', 'III', 'IV', 'V', 'VI']),
            'wali_kelas' => User::factory(), // Merujuk ke ID user
        ];
    }
}
