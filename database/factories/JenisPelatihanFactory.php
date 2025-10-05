<?php

namespace Database\Factories;

use App\Models\JenisPelatihan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<JenisPelatihan>
 */
class JenisPelatihanFactory extends Factory
{
    protected $model = JenisPelatihan::class;

    public function definition(): array
    {
        return [
            'kode' => Str::upper($this->faker->bothify('JP-###')),
            'nama' => 'Pelatihan ' . $this->faker->unique()->word(),
            'kategori' => $this->faker->randomElement(['Teknis', 'Manajerial', 'Fungsional']),
            'deskripsi' => $this->faker->sentence(),
            'target_peserta' => $this->faker->randomElement(['ASN', 'PTT', 'Campuran']),
            'durasi_standar' => $this->faker->numberBetween(8, 60),
            'sertifikasi' => $this->faker->boolean(),
        ];
    }
}

