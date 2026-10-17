<?php

namespace Tests\Feature;

use App\Models\JenisPelatihan;
use App\Models\Pegawai;
use App\Models\Pelatihan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PelatihanSafetyTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('retainedCertificateFlags')]
    public function test_edit_retains_certificate_unless_removal_is_requested(array $flags): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('sertifikat/existing.pdf', 'original certificate');
        $pelatihan = Pelatihan::factory()->create(['sertifikat_path' => 'sertifikat/existing.pdf']);

        $this->actingAs(User::factory()->create())
            ->putJson(route('pelatihan.update', $pelatihan), $this->payload($pelatihan) + $flags)
            ->assertOk();

        Storage::disk('public')->assertExists('sertifikat/existing.pdf');
        $this->assertSame('sertifikat/existing.pdf', $pelatihan->fresh()->sertifikat_path);
    }

    public static function retainedCertificateFlags(): array
    {
        return [
            'not provided' => [[]],
            'false boolean' => [['replace_file' => false]],
            'zero string' => [['replace_file' => '0']],
            'false string' => [['replace_file' => 'false']],
        ];
    }

    public function test_edit_can_explicitly_remove_certificate(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('sertifikat/existing.pdf', 'original certificate');
        $pelatihan = Pelatihan::factory()->create(['sertifikat_path' => 'sertifikat/existing.pdf']);

        $this->actingAs(User::factory()->create())
            ->putJson(route('pelatihan.update', $pelatihan), $this->payload($pelatihan) + ['replace_file' => 'true'])
            ->assertOk();

        Storage::disk('public')->assertMissing('sertifikat/existing.pdf');
        $this->assertNull($pelatihan->fresh()->sertifikat_path);
    }

    public function test_edit_can_replace_certificate(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('sertifikat/existing.pdf', 'original certificate');
        $pelatihan = Pelatihan::factory()->create(['sertifikat_path' => 'sertifikat/existing.pdf']);
        $file = UploadedFile::fake()->create('replacement.pdf', 10, 'application/pdf');

        $this->actingAs(User::factory()->create())
            ->putJson(route('pelatihan.update', $pelatihan), $this->payload($pelatihan) + [
                'sertifikat' => $file,
                'replace_file' => 'true',
            ])
            ->assertOk();

        Storage::disk('public')->assertMissing('sertifikat/existing.pdf');
        Storage::disk('public')->assertExists($pelatihan->fresh()->sertifikat_path);
    }

    public function test_create_rejects_end_date_before_start_without_saving_file(): void
    {
        Storage::fake('public');
        $pelatihan = Pelatihan::factory()->make([
            'pegawai_id' => Pegawai::factory()->create()->id,
            'jenis_pelatihan_id' => JenisPelatihan::factory()->create()->id,
        ]);

        $this->actingAs(User::factory()->create())
            ->postJson(route('pelatihan.store'), array_replace($this->payload($pelatihan), [
                'tanggal_selesai' => '2026-04-09',
                'sertifikat' => UploadedFile::fake()->create('certificate.pdf', 10, 'application/pdf'),
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('tanggal_selesai');

        $this->assertDatabaseCount('pelatihans', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_update_rejects_end_date_before_start_and_preserves_certificate(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('sertifikat/existing.pdf', 'original certificate');
        $pelatihan = Pelatihan::factory()->create([
            'tanggal_mulai' => '2026-04-10',
            'tanggal_selesai' => '2026-04-12',
            'sertifikat_path' => 'sertifikat/existing.pdf',
        ]);

        $this->actingAs(User::factory()->create())
            ->putJson(route('pelatihan.update', $pelatihan), array_replace($this->payload($pelatihan), [
                'tanggal_selesai' => '2026-04-09',
                'replace_file' => 'true',
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('tanggal_selesai');

        $this->assertDatabaseHas('pelatihans', [
            'id' => $pelatihan->id,
            'tanggal_selesai' => '2026-04-12',
            'sertifikat_path' => 'sertifikat/existing.pdf',
        ]);
        Storage::disk('public')->assertExists('sertifikat/existing.pdf');
    }

    public function test_same_day_training_is_allowed(): void
    {
        $pelatihan = Pelatihan::factory()->make([
            'pegawai_id' => Pegawai::factory()->create()->id,
            'jenis_pelatihan_id' => JenisPelatihan::factory()->create()->id,
        ]);

        $this->actingAs(User::factory()->create())
            ->postJson(route('pelatihan.store'), array_replace($this->payload($pelatihan), [
                'tanggal_selesai' => '2026-04-10',
            ]))
            ->assertOk();

        $this->assertDatabaseCount('pelatihans', 1);
    }

    #[DataProvider('spreadsheetTextValues')]
    public function test_csv_exports_untrusted_text_literally_and_keeps_numeric_values(string $value, string $expected): void
    {
        $pegawai = Pegawai::factory()->create(['nama_lengkap' => $value, 'nip' => null]);
        // The legacy accessor caches names by ID across application instances.
        $jenis = JenisPelatihan::factory()->create(['id' => crc32($value), 'nama' => $value]);
        $pelatihan = Pelatihan::factory()->for($pegawai)->for($jenis)->create([
            'nama_pelatihan' => $value,
            'penyelenggara' => $value,
            'jp' => 24,
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->get(route('pelatihan.export', ['format' => 'csv']))
            ->assertOk();

        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $response->streamedContent());
        rewind($stream);
        fgetcsv($stream);
        $row = fgetcsv($stream);
        fclose($stream);
        $this->assertSame([(string) $pelatihan->id, $expected, $expected, $expected, $expected], array_slice($row, 0, 5));
        $this->assertSame('24', $row[7]);
    }

    public static function spreadsheetTextValues(): array
    {
        return [
            'equals formula' => ['=1+1', "'=1+1"],
            'plus formula' => ['+1+1', "'+1+1"],
            'minus formula' => ['-1+1', "'-1+1"],
            'at formula' => ['@SUM(1)', "'@SUM(1)"],
            'space before formula' => ['  =1+1', "'  =1+1"],
            'tab prefix' => ["\t1+1", "'\t1+1"],
            'newline prefix' => ["\n=1+1", "'\n=1+1"],
            'plain text' => ['Pelatihan, teknis "aman"', 'Pelatihan, teknis "aman"'],
        ];
    }

    private function payload(Pelatihan $pelatihan): array
    {
        return [
            'pegawai_id' => $pelatihan->pegawai_id,
            'nama_pelatihan' => $pelatihan->nama_pelatihan,
            'jenis_pelatihan_id' => $pelatihan->jenis_pelatihan_id,
            'penyelenggara' => $pelatihan->penyelenggara,
            'tanggal_mulai' => '2026-04-10',
            'tanggal_selesai' => '2026-04-12',
            'jp' => 24,
        ];
    }
}
