<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelatihanController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\PegawaiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/progress', [ProgressController::class, 'index'])->name('progress');
Route::get('/pegawai/{pegawai}', [PegawaiController::class, 'show'])->name('pegawai.show');
Route::get('/pelatihan/comparison', [PelatihanController::class, 'comparison'])->name('pelatihan.comparison');
Route::resource('pelatihan', PelatihanController::class);
