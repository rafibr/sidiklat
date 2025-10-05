<?php

namespace Database\Seeders;

use App\Models\JenisPelatihan;
use App\Models\Pegawai;
use App\Models\PegawaiJpTarget;
use App\Models\Pelatihan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PelatihanSeeder extends Seeder
{
    public function run(): void
    {
        $jenisByKode = JenisPelatihan::pluck('id', 'kode');
        $pegawaiByNama = Pegawai::pluck('id', 'nama_lengkap');

        $pelatihanList = [
            [
                'pegawai' => 'Rika Putri, S.Sos',
                'nama_pelatihan' => 'Pelatihan Kepemimpinan Administrator',
                'jenis_kode' => 'MP-01',
                'penyelenggara' => 'BKPP Kota Banjarbaru',
                'tempat' => 'Banjarbaru',
                'tanggal_mulai' => '2024-02-12',
                'tanggal_selesai' => '2024-02-16',
                'jp' => 32,
                'status' => 'selesai',
                'deskripsi' => 'Penguatan kompetensi kepemimpinan untuk pejabat administrator.',
            ],
            [
                'pegawai' => 'Dedi Pratama, S.Kom',
                'nama_pelatihan' => 'Pelatihan Pengembangan Aplikasi Pemerintahan',
                'jenis_kode' => 'PP-02',
                'penyelenggara' => 'Pusdiklat Kominfo',
                'tempat' => 'Yogyakarta',
                'tanggal_mulai' => '2024-04-01',
                'tanggal_selesai' => '2024-04-05',
                'jp' => 28,
                'status' => 'selesai',
                'deskripsi' => 'Pengembangan sistem informasi pemerintahan dan pelayanan publik.',
            ],
            [
                'pegawai' => 'Dedi Pratama, S.Kom',
                'nama_pelatihan' => 'Workshop Manajemen Proyek Digital',
                'jenis_kode' => null,
                'penyelenggara' => 'Komunitas Smart City',
                'tempat' => 'Jakarta',
                'tanggal_mulai' => '2023-11-20',
                'tanggal_selesai' => '2023-11-22',
                'jp' => 24,
                'status' => 'selesai',
                'deskripsi' => 'Workshop pengelolaan proyek transformasi digital pemerintahan.',
            ],
            [
                'pegawai' => 'Siti Rahmawati',
                'nama_pelatihan' => 'Bimbingan Teknis Administrasi Pelatihan',
                'jenis_kode' => 'PS-03',
                'penyelenggara' => 'Badan Pengembangan SDM Daerah',
                'tempat' => 'Banjarbaru',
                'tanggal_mulai' => '2024-03-10',
                'tanggal_selesai' => '2024-03-11',
                'jp' => 16,
                'status' => 'selesai',
                'deskripsi' => 'Penguatan kemampuan administrasi dukungan pelatihan.',
            ],
        ];

        foreach ($pelatihanList as $item) {
            $pegawaiId = $pegawaiByNama[$item['pegawai']] ?? null;

            if (!$pegawaiId) {
                continue;
            }

            $pelatihan = Pelatihan::updateOrCreate(
                [
                    'pegawai_id' => $pegawaiId,
                    'nama_pelatihan' => $item['nama_pelatihan'],
                ],
                [
                    'jenis_pelatihan_id' => $item['jenis_kode'] ? ($jenisByKode[$item['jenis_kode']] ?? null) : null,
                    'penyelenggara' => $item['penyelenggara'],
                    'tempat' => $item['tempat'],
                    'tanggal_mulai' => Carbon::parse($item['tanggal_mulai']),
                    'tanggal_selesai' => Carbon::parse($item['tanggal_selesai']),
                    'jp' => $item['jp'],
                    'status' => $item['status'],
                    'sertifikat_path' => null,
                    'deskripsi' => $item['deskripsi'],
                ]
            );

            $year = (int) Carbon::parse($item['tanggal_selesai'])->format('Y');

            $existingTarget = PegawaiJpTarget::where('pegawai_id', $pegawaiId)
                ->where('tahun', $year)
                ->first();

            $jpTarget = $existingTarget->jp_target ?? 20;
            $jpAchieved = ($existingTarget->jp_tercapai ?? 0) + $item['jp'];

            PegawaiJpTarget::updateOrCreate(
                [
                    'pegawai_id' => $pegawaiId,
                    'tahun' => $year,
                ],
                [
                    'jp_target' => $jpTarget,
                    'jp_tercapai' => $jpAchieved,
                ]
            );
        }

        // Refresh JP tercapai pada tabel pegawais berdasarkan total pelatihan
        $pegawaiCollection = Pegawai::with('pelatihans')->get();
        foreach ($pegawaiCollection as $pegawai) {
            $totalJp = $pegawai->pelatihans->sum('jp');
            $pegawai->update(['jp_tercapai' => $totalJp]);
        }
    }
}
