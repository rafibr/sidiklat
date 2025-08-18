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
		Schema::create('pegawais', function (Blueprint $table) {
			$table->id();
			$table->string('nip')->nullable(); // Beberapa pegawai kontrak tidak ada NIP
			$table->string('nama_lengkap');
			$table->string('pangkat_golongan')->nullable();
			$table->string('jabatan');
			$table->string('unit_kerja');
			$table->string('status'); // ASN, PTT, T. Kontrak
			$table->date('tanggal_pengangkatan')->nullable();
			$table->text('keterangan')->nullable();
			$table->integer('jp_target')->default(0);
			$table->integer('jp_tercapai')->default(0);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('pegawais');
	}
};
