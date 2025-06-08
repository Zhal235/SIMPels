<?php

namespace Database\Factories;

use App\Models\BukuKas;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JenisTagihan>
 */
class JenisTagihanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->words(3, true) . ' Insidental',
            'deskripsi' => $this->faker->sentence(),
            'kategori_tagihan' => 'Insidental',
            'is_bulanan' => 0,
            'nominal' => $this->faker->numberBetween(50000, 500000),
            'is_nominal_per_kelas' => 0,
            'buku_kas_id' => 1, // Assuming we have at least one buku kas
            'tanggal_jatuh_tempo' => $this->faker->numberBetween(1, 31),
            'bulan_jatuh_tempo' => 0,
            'bulan_pembayaran' => ['1', '3', '6'], // Some sample months
            'target_type' => 'kelas',
            'target_kelas' => ['1', '2'], // Some sample kelas IDs
            'target_santri' => null,
            'tahun_ajaran_id' => 1, // Assuming we have at least one tahun ajaran
        ];
    }

    /**
     * Create an insidental tagihan for all santri
     */
    public function allSantri(): static
    {
        return $this->state(fn (array $attributes) => [
            'target_type' => 'all',
            'target_kelas' => null,
            'target_santri' => null,
        ]);
    }

    /**
     * Create an insidental tagihan for specific santri
     */
    public function santri(): static
    {
        return $this->state(fn (array $attributes) => [
            'target_type' => 'santri',
            'target_kelas' => null,
            'target_santri' => ['1', '2', '3'], // Some sample santri IDs
        ]);
    }
}
