<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('jenis_pelatihans', function (Blueprint $table) {
			$table->id();
			$table->string('kode')->nullable();
			$table->string('nama');
			$table->string('kategori')->nullable();
			$table->text('deskripsi')->nullable();
			$table->string('target_peserta')->nullable();
			$table->string('durasi_standar')->nullable();
			$table->boolean('sertifikasi')->default(false);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('jenis_pelatihans');
	}
};
