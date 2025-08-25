<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelatihanController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\PegawaiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/progress', [ProgressController::class, 'index'])->name('progress');
Route::get('/pegawai/{pegawai}', [PegawaiController::class, 'show'])->name('pegawai.show');
Route::get('/pelatihan/comparison', [PelatihanController::class, 'comparison'])->name('pelatihan.comparison');
Route::get('/pelatihan/export', [PelatihanController::class, 'export'])->name('pelatihan.export');
Route::resource('pelatihan', PelatihanController::class);

// Route untuk mengakses file sertifikat
Route::get('/storage/sertifikat/{filename}', function ($filename) {
	$path = 'sertifikat/' . $filename;

	if (!Storage::disk('public')->exists($path)) {
		abort(404);
	}

	$file = Storage::disk('public')->get($path);
	$fullPath = Storage::disk('public')->path($path);
	$type = mime_content_type($fullPath) ?: 'application/octet-stream';

	return response($file, 200)
		->header('Content-Type', $type)
		->header('Content-Disposition', 'inline; filename="' . $filename . '"');
})->name('sertifikat.show');
