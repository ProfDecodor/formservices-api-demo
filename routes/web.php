<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::prefix('applications')->name('applications.')->group(function () {
    Route::get('/', [ApplicationController::class, 'index'])->name('index');
    Route::get('/{id}', [ApplicationController::class, 'show'])->name('show')->where('id', '[0-9]+');
});

Route::prefix('files')->name('files.')->group(function () {
    Route::get('/', [FileController::class, 'index'])->name('index');
    Route::get('/{uuid}', [FileController::class, 'show'])->name('show');
});

Route::get('/auth', [AuthController::class, 'index'])->name('auth.index');
