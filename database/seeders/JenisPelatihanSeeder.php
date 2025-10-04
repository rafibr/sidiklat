<?php

namespace Database\Seeders;

use App\Models\JenisPelatihan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class JenisPelatihanSeeder extends Seeder
{
        public function run(): void
        {
                $jenisPelatihan = [];

                if (File::exists(base_path('jenis-pelatihan.json'))) {
                        $json = File::get(base_path('jenis-pelatihan.json'));
                        $data = json_decode($json, true);
                        $jenisPelatihan = $data['jenis_pelatihan'] ?? [];
                }

                if (empty($jenisPelatihan)) {
                        $jenisPelatihan = [
                                [
                                        'kode' => 'MP-01',
                                        'nama' => 'Manajemen Pemerintahan',
                                        'kategori' => 'Manajerial',
                                        'deskripsi' => 'Penguatan kompetensi manajemen pemerintahan daerah.',
                                        'target_peserta' => 'Pejabat administrator dan pengawas',
                                        'durasi_standar' => '24 JP',
                                        'sertifikasi' => true,
                                ],
                                [
                                        'kode' => 'PP-02',
                                        'nama' => 'Pelayanan Publik Adaptif',
                                        'kategori' => 'Pelayanan',
                                        'deskripsi' => 'Pelatihan inovasi layanan publik berbasis kebutuhan warga.',
                                        'target_peserta' => 'ASN frontliner dan analis kebijakan',
                                        'durasi_standar' => '32 JP',
                                        'sertifikasi' => true,
                                ],
                                [
                                        'kode' => 'PS-03',
                                        'nama' => 'Perencanaan Strategis Daerah',
                                        'kategori' => 'Perencanaan',
                                        'deskripsi' => 'Sinkronisasi dokumen perencanaan jangka menengah dan tahunan.',
                                        'target_peserta' => 'Perencana dan pejabat fungsional',
                                        'durasi_standar' => '28 JP',
                                        'sertifikasi' => false,
                                ],
                        ];
                }

                foreach ($jenisPelatihan as $item) {
                        JenisPelatihan::updateOrCreate([
                                'kode' => $item['kode'] ?? null,
                        ], [
                                'nama' => $item['nama'] ?? 'Unknown',
                                'kategori' => $item['kategori'] ?? null,
                                'deskripsi' => $item['deskripsi'] ?? null,
                                'target_peserta' => $item['target_peserta'] ?? null,
                                'durasi_standar' => $item['durasi_standar'] ?? null,
                                'sertifikasi' => !empty($item['sertifikasi']),
                        ]);
                }
        }
}
