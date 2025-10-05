<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Models\PegawaiJpTarget;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        $pegawaiList = [
            [
                'nip' => '198701012010011001',
                'nama_lengkap' => 'Rika Putri, S.Sos',
                'pangkat_golongan' => 'III/c - Penata',
                'jabatan' => 'Analis Sumber Daya Manusia Aparatur',
                'unit_kerja' => 'Bidang Pembinaan dan Pengembangan Kompetensi',
                'status' => 'ASN',
                'tanggal_pengangkatan' => '2010-01-01',
                'keterangan' => 'Koordinator program pengembangan kompetensi.',
                'email' => 'rika.putri@banjarbarukota.go.id',
                'telepon' => '081234567890',
                'jp_target' => 80,
            ],
            [
                'nip' => '199002052015031002',
                'nama_lengkap' => 'Dedi Pratama, S.Kom',
                'pangkat_golongan' => 'III/b - Penata Muda Tk. I',
                'jabatan' => 'Pengelola Sistem Informasi',
                'unit_kerja' => 'Bidang Data dan Informasi',
                'status' => 'ASN',
                'tanggal_pengangkatan' => '2015-03-01',
                'keterangan' => 'Penanggung jawab aplikasi Sidiklat.',
                'email' => 'dedi.pratama@banjarbarukota.go.id',
                'telepon' => '081298765432',
                'jp_target' => 60,
            ],
            [
                'nip' => null,
                'nama_lengkap' => 'Siti Rahmawati',
                'pangkat_golongan' => null,
                'jabatan' => 'Tenaga Pendukung Administrasi',
                'unit_kerja' => 'Sekretariat',
                'status' => 'PTT',
                'tanggal_pengangkatan' => '2021-07-15',
                'keterangan' => 'Membantu proses administrasi pelatihan.',
                'email' => 'siti.rahmawati@banjarbarukota.go.id',
                'telepon' => '082112223344',
                'jp_target' => 40,
            ],
        ];

        $currentYear = (int) now()->format('Y');

        foreach ($pegawaiList as $data) {
            $pegawai = Pegawai::updateOrCreate(
                ['nama_lengkap' => $data['nama_lengkap']],
                [
                    'nip' => $data['nip'],
                    'pangkat_golongan' => $data['pangkat_golongan'],
                    'jabatan' => $data['jabatan'],
                    'unit_kerja' => $data['unit_kerja'],
                    'status' => $data['status'],
                    'tanggal_pengangkatan' => Carbon::parse($data['tanggal_pengangkatan']),
                    'keterangan' => $data['keterangan'],
                    'jp_target' => $data['jp_target'],
                    'jp_tercapai' => 0,
                    'email' => $data['email'],
                    'telepon' => $data['telepon'],
                ]
            );

            foreach ([$currentYear - 1, $currentYear] as $year) {
                PegawaiJpTarget::updateOrCreate(
                    [
                        'pegawai_id' => $pegawai->id,
                        'tahun' => $year,
                    ],
                    [
                        'jp_target' => $data['jp_target'],
                        'jp_tercapai' => 0,
                    ]
                );
            }
        }
    }
}
