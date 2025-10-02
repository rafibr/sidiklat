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
		Schema::create('pegawai_jp_targets', function (Blueprint $table) {
			$table->id();
			$table->foreignId('pegawai_id')->constrained('pegawais')->onDelete('cascade');
			$table->year('tahun');
			$table->integer('jp_target')->default(20);
			$table->integer('jp_tercapai')->default(0);
			$table->timestamps();

			// Ensure one record per pegawai per year
			$table->unique(['pegawai_id', 'tahun']);
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('pegawai_jp_targets');
	}
};
