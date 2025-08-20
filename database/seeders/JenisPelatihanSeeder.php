<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\JenisPelatihan;

class JenisPelatihanSeeder extends Seeder
{
	public function run(): void
	{
		$json = File::get(base_path('jenis-pelatihan.json'));
		$data = json_decode($json, true);

		foreach ($data['jenis_pelatihan'] as $item) {
			JenisPelatihan::updateOrCreate([
				'kode' => $item['kode'] ?? null
			], [
				'nama' => $item['nama'] ?? 'Unknown',
				'kategori' => $item['kategori'] ?? null,
				'deskripsi' => $item['deskripsi'] ?? null,
				'target_peserta' => $item['target_peserta'] ?? null,
				'durasi_standar' => $item['durasi_standar'] ?? null,
				'sertifikasi' => !empty($item['sertifikasi'])
			]);
		}
	}
}
