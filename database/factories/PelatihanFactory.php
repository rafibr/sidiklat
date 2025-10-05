<?php

namespace Database\Factories;

use App\Models\JenisPelatihan;
use App\Models\Pegawai;
use App\Models\Pelatihan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Pelatihan>
 */
class PelatihanFactory extends Factory
{
    protected $model = Pelatihan::class;

    public function definition(): array
    {
        $start = Carbon::instance($this->faker->dateTimeBetween('-2 years', '+2 years'));
        $end = (clone $start)->addDays($this->faker->numberBetween(1, 5));

        return [
            'pegawai_id' => Pegawai::factory(),
            'nama_pelatihan' => 'Pelatihan ' . $this->faker->unique()->words(2, true),
            'jenis_pelatihan_id' => JenisPelatihan::factory(),
            'penyelenggara' => $this->faker->company(),
            'tempat' => $this->faker->city(),
            'tanggal_mulai' => $start->toDateString(),
            'tanggal_selesai' => $end->toDateString(),
            'jp' => $this->faker->numberBetween(8, 60),
            'status' => $this->faker->randomElement(['selesai', 'sedang_berjalan', 'akan_datang']),
            'deskripsi' => $this->faker->sentence(),
        ];
    }

    public function forYear(int $year): self
    {
        $start = Carbon::create($year, $this->faker->numberBetween(1, 12), $this->faker->numberBetween(1, 20));
        $end = (clone $start)->addDays($this->faker->numberBetween(1, 5));

        return $this->state(fn () => [
            'tanggal_mulai' => $start->toDateString(),
            'tanggal_selesai' => $end->toDateString(),
        ]);
    }
}

