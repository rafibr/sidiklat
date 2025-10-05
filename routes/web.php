<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelatihanController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\PegawaiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Halaman utama bisa diakses tanpa login (untuk stakeholder/pimpinan)
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/progress', [ProgressController::class, 'index'])->name('progress');

    // Pegawai routes
    Route::resource('pegawai', PegawaiController::class);
    Route::post('/pegawai/update-jp-default', [PegawaiController::class, 'updateJpDefault'])->name('pegawai.update-jp-default');

    Route::get('/pelatihan/comparison', [PelatihanController::class, 'comparison'])->name('pelatihan.comparison');
    Route::get('/pelatihan/export', [PelatihanController::class, 'export'])->name('pelatihan.export');
    Route::resource('pelatihan', PelatihanController::class);

    // API route for mobile stats
    Route::get('/api/mobile-stats', [DashboardController::class, 'mobileStats'])->name('api.mobile-stats');

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
});

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
