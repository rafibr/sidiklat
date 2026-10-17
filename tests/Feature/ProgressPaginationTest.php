<?php

namespace Tests\Feature;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProgressPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_pagination_keeps_year_unit_and_search_filters(): void
    {
        $this->withoutVite();
        $user = User::factory()->create();
        Pegawai::factory()->count(11)->create([
            'nama_lengkap' => 'Pengujian Pegawai',
            'unit_kerja' => 'Sekretariat',
        ]);

        $this->actingAs($user)
            ->get('/progress?year=2025&unit_kerja=Sekretariat&search=Pengujian')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Progress/Index')
                ->has('pegawais.data', 10)
                ->where('pegawais.total', 11)
                ->where('pegawais.next_page_url', function ($url): bool {
                    parse_str(parse_url($url, PHP_URL_QUERY), $query);

                    return ($query['year'] ?? null) === '2025'
                        && ($query['unit_kerja'] ?? null) === 'Sekretariat'
                        && ($query['search'] ?? null) === 'Pengujian'
                        && ($query['page'] ?? null) === '2';
                }));
    }
}
