<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ProjectController;
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

Route::prefix('projects')->name('projects.')->group(function () {
    Route::get('/', [ProjectController::class, 'index'])->name('index');
    Route::get('/{id}', [ProjectController::class, 'show'])->name('show')->where('id', '[0-9]+');
    Route::get('/{id}/files/{fileId}', [ProjectController::class, 'showFile'])->name('file')->where(['id' => '[0-9]+', 'fileId' => '[0-9]+']);
    Route::post('/{id}/prepare', [ProjectController::class, 'prepare'])->name('prepare')->where('id', '[0-9]+');
    Route::post('/{id}/deploy', [ProjectController::class, 'deploy'])->name('deploy')->where('id', '[0-9]+');
    Route::get('/{id}/test', [ProjectController::class, 'test'])->name('test')->where('id', '[0-9]+');
});
