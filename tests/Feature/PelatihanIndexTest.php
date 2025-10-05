<?php

namespace Tests\Feature;

use App\Models\JenisPelatihan;
use App\Models\Pelatihan;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class PelatihanIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_index_lists_pelatihans_with_related_data(): void
    {
        $user = User::factory()->create();

        $jenisTeknis = JenisPelatihan::factory()->create(['nama' => 'Teknis']);
        $jenisManajerial = JenisPelatihan::factory()->create(['nama' => 'Manajerial']);

        $pegawaiA = Pegawai::factory()->create(['nama_lengkap' => 'Ani Wijaya']);
        $pegawaiB = Pegawai::factory()->create(['nama_lengkap' => 'Budi Santoso']);

        Pelatihan::factory()
            ->for($pegawaiA)
            ->for($jenisTeknis)
            ->create([
                'nama_pelatihan' => 'Pelatihan A',
                'tanggal_mulai' => '2024-03-01',
                'tanggal_selesai' => '2024-03-05',
                'status' => 'selesai',
                'jp' => 24,
            ]);

        Pelatihan::factory()
            ->for($pegawaiB)
            ->for($jenisManajerial)
            ->create([
                'nama_pelatihan' => 'Pelatihan B',
                'tanggal_mulai' => '2023-07-10',
                'tanggal_selesai' => '2023-07-12',
                'status' => 'selesai',
                'jp' => 18,
            ]);

        $response = $this->actingAs($user)->get(route('pelatihan.index'));

        $response->assertInertia(function (AssertableInertia $page) use ($jenisTeknis, $jenisManajerial) {
            $page->component('Pelatihan/Index')
                ->where('total_pelatihans', 2)
                ->where('per_page', 25)
                ->has('pelatihans.data', 2)
                ->where('availableYears', function ($years) {
                    $normalized = is_array($years) ? $years : $years->toArray();

                    return $normalized === [2024, 2023];
                })
                ->where('pelatihans.data', function ($items) {
                    $names = collect($items)->pluck('nama_pelatihan');

                    return $names->contains('Pelatihan A') && $names->contains('Pelatihan B');
                })
                ->where('jenisPelatihan', function ($items) use ($jenisTeknis, $jenisManajerial) {
                    $ids = collect($items)->pluck('id');

                    return $ids->contains($jenisTeknis->id) && $ids->contains($jenisManajerial->id);
                });
        });
    }

    public function test_index_filters_by_year_and_jenis_name(): void
    {
        $user = User::factory()->create();

        $jenisTeknis = JenisPelatihan::factory()->create(['nama' => 'Teknis']);
        $jenisManajerial = JenisPelatihan::factory()->create(['nama' => 'Manajerial']);

        $pegawaiA = Pegawai::factory()->create(['nama_lengkap' => 'Ani Wijaya']);
        $pegawaiB = Pegawai::factory()->create(['nama_lengkap' => 'Budi Santoso']);

        Pelatihan::factory()
            ->for($pegawaiA)
            ->for($jenisTeknis)
            ->create([
                'nama_pelatihan' => 'Pelatihan Filter',
                'tanggal_mulai' => '2024-02-15',
                'tanggal_selesai' => '2024-02-20',
                'status' => 'selesai',
                'jp' => 32,
            ]);

        Pelatihan::factory()
            ->for($pegawaiB)
            ->for($jenisTeknis)
            ->create([
                'nama_pelatihan' => 'Pelatihan Lama',
                'tanggal_mulai' => '2023-05-01',
                'tanggal_selesai' => '2023-05-04',
                'status' => 'selesai',
            ]);

        Pelatihan::factory()
            ->for($pegawaiB)
            ->for($jenisManajerial)
            ->create([
                'nama_pelatihan' => 'Pelatihan Manajerial',
                'tanggal_mulai' => '2024-06-10',
                'tanggal_selesai' => '2024-06-13',
                'status' => 'selesai',
            ]);

        $response = $this->actingAs($user)->get(route('pelatihan.index', [
            'year' => 2024,
            'jenis' => 'Teknis',
        ]));

        $response->assertInertia(function (AssertableInertia $page) {
            $page->component('Pelatihan/Index')
                ->where('total_pelatihans', 1)
                ->has('pelatihans.data', 1)
                ->where('pelatihans.data.0.nama_pelatihan', 'Pelatihan Filter')
                ->where('pelatihans.data.0.pegawai.nama_lengkap', 'Ani Wijaya');
        });
    }

    public function test_index_search_matches_pegawai_name(): void
    {
        $user = User::factory()->create();

        $jenis = JenisPelatihan::factory()->create(['nama' => 'Teknis']);

        $pegawaiMatch = Pegawai::factory()->create(['nama_lengkap' => 'Siti Cari']);
        $pegawaiOther = Pegawai::factory()->create(['nama_lengkap' => 'Joko Lain']);

        Pelatihan::factory()
            ->for($pegawaiMatch)
            ->for($jenis)
            ->create([
                'nama_pelatihan' => 'Pelatihan Search',
                'tanggal_mulai' => '2024-04-01',
                'tanggal_selesai' => '2024-04-03',
                'status' => 'selesai',
            ]);

        Pelatihan::factory()
            ->for($pegawaiOther)
            ->for($jenis)
            ->create([
                'nama_pelatihan' => 'Pelatihan Other',
                'tanggal_mulai' => '2024-05-01',
                'tanggal_selesai' => '2024-05-03',
                'status' => 'selesai',
            ]);

        $response = $this->actingAs($user)->get(route('pelatihan.index', [
            'search' => 'Siti',
        ]));

        $response->assertInertia(function (AssertableInertia $page) {
            $page->component('Pelatihan/Index')
                ->where('total_pelatihans', 1)
                ->has('pelatihans.data', 1)
                ->where('pelatihans.data.0.pegawai.nama_lengkap', 'Siti Cari')
                ->where('pelatihans.data.0.nama_pelatihan', 'Pelatihan Search');
        });
    }
}

