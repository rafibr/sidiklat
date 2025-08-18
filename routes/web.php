<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelatihanController;
use App\Http\Controllers\ProgressController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/progress', [ProgressController::class, 'index'])->name('progress');
Route::resource('pelatihan', PelatihanController::class);
