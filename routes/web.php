<?php

use App\Http\Controllers\CollectionPointController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\RecyclingCenterController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Rutas del patrón MVC
    Route::resource('materials', MaterialController::class)->except('show');
    Route::resource('points', CollectionPointController::class)->except('show');
    Route::resource('centers', RecyclingCenterController::class)->except('show');
    Route::resource('users', UserController::class)->except('show')->middleware('administrator');
});

require __DIR__.'/settings.php';
