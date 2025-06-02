<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Santri>
 */
class SantriFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nis' => $this->faker->unique()->numerify('##########'), // Kolom wajib berdasarkan migrasi add_nis_to_santris_table_old
            'nama_santri' => $this->faker->name(),
            'nisn' => $this->faker->unique()->numerify('##########'), // Menggunakan nisn sesuai migrasi awal
            'nik_santri' => $this->faker->unique()->numerify('################'), // Opsional, bisa di-null-kan jika tidak selalu ada
            'jenis_kelamin' => $this->faker->randomElement(['L', 'P']),
            'tempat_lahir' => $this->faker->city(),
            'tanggal_lahir' => $this->faker->date(),
            'alamat' => $this->faker->address(),
            'nama_ayah' => $this->faker->name('male'),
            'nama_ibu' => $this->faker->name('female'),
            'hp_ayah' => $this->faker->phoneNumber(), // Menggunakan hp_ayah sebagai perwakilan telepon wali
            'kelas_id' => \App\Models\Kelas::factory()->create()->id, // Menggunakan factory untuk membuat dan mendapatkan ID
            'asrama_id' => \App\Models\Asrama::factory()->create()->id, // Menggunakan factory untuk membuat dan mendapatkan ID
            'status' => $this->faker->randomElement(['aktif', 'nonaktif', 'lulus', 'pindah']), // Sesuai dengan migrasi add_status_to_santris_table
            'foto' => null, // Sesuai dengan kolom 'foto' di migrasi
            // Kolom lain dari migrasi awal bisa ditambahkan jika diperlukan untuk test
            'provinsi' => $this->faker->state(),
            'kabupaten' => $this->faker->city(),
            'kecamatan' => $this->faker->citySuffix(), // Ini mungkin tidak akurat, hanya contoh
            'desa' => $this->faker->streetName(), // Ini mungkin tidak akurat, hanya contoh
        ];
    }
}
