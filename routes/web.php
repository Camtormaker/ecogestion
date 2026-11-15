<?php

use App\Http\Controllers\CollectionPointController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\RecyclingCenterController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::middleware(['auth', 'verified', 'web.access'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::get('materials', [MaterialController::class, 'index'])->name('materials.index');
    Route::get('points', [CollectionPointController::class, 'index'])->name('points.index');
    Route::get('centers', [RecyclingCenterController::class, 'index'])->name('centers.index');

    Route::middleware('administrator')->group(function () {
        Route::resource('materials', MaterialController::class)->except(['index', 'show']);
        Route::resource('points', CollectionPointController::class)->except(['index', 'show']);
        Route::resource('centers', RecyclingCenterController::class)->except(['index', 'show']);
        Route::resource('users', UserController::class)->except('show');
    });
});

require __DIR__.'/settings.php';
