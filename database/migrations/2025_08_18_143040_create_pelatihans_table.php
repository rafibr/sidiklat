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
        Schema::create('pelatihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->onDelete('cascade');
            $table->string('nama_pelatihan'); // dari field 'diklat'
            $table->string('jenis_pelatihan'); // dari field 'jenis'
            $table->string('penyelenggara');
            $table->string('tempat')->nullable();
            $table->string('tanggal_mulai'); // String karena format bervariasi dalam JSON
            $table->string('tanggal_selesai'); // String karena format bervariasi dalam JSON
            $table->integer('jp')->default(0);
            $table->string('status')->default('selesai'); // selesai, sedang_berjalan, akan_datang
            $table->string('sertifikat_path')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelatihans');
    }
};
