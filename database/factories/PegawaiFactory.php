<?php

namespace Database\Factories;

use App\Models\Pegawai;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pegawai>
 */
class PegawaiFactory extends Factory
{
    protected $model = Pegawai::class;

    public function definition(): array
    {
        return [
            'nip' => $this->faker->optional()->numerify('19##############'),
            'nama_lengkap' => $this->faker->name(),
            'pangkat_golongan' => $this->faker->optional()->randomElement([
                'III/a - Penata Muda',
                'III/b - Penata Muda Tk. I',
                'III/c - Penata',
                'II/d - Pengatur Tk. I',
            ]),
            'jabatan' => $this->faker->jobTitle(),
            'unit_kerja' => $this->faker->randomElement([
                'Bidang Data dan Informasi',
                'Bidang Pembinaan dan Pengembangan Kompetensi',
                'Sekretariat',
            ]),
            'status' => $this->faker->randomElement(['ASN', 'PTT']),
            'tanggal_pengangkatan' => $this->faker->date(),
            'keterangan' => $this->faker->sentence(),
            'jp_target' => $this->faker->numberBetween(20, 100),
            'jp_tercapai' => 0,
            'email' => $this->faker->unique()->safeEmail(),
            'telepon' => $this->faker->phoneNumber(),
        ];
    }
}

