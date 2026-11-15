<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CollectionEventController;
use App\Http\Controllers\Api\CollectionPointController;
use App\Http\Controllers\Api\DeliveryController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\RecyclingCenterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rutas públicas (no requieren token)
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas (requieren token de Sanctum)
Route::middleware(['auth:sanctum', 'react.access'])->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Catálogos de solo consulta para usuarios autorizados de React
    Route::get('/materials', [MaterialController::class, 'index']);
    Route::get('/materials/{id}', [MaterialController::class, 'show']);
    Route::get('/collection-points', [CollectionPointController::class, 'index']);
    Route::get('/collection-points/{id}', [CollectionPointController::class, 'show']);
    Route::get('/recycling-centers', [RecyclingCenterController::class, 'index']);
    Route::get('/recycling-centers/{id}', [RecyclingCenterController::class, 'show']);

    // API de Jornadas de Recolección
    Route::get('/collection-events', [CollectionEventController::class, 'index']);
    Route::get('/collection-events/{id}', [CollectionEventController::class, 'show']);

    // API de Entregas (Deliveries)
    Route::get('/deliveries', [DeliveryController::class, 'index']);
    Route::get('/deliveries/{id}', [DeliveryController::class, 'show']);

    // API de Inventario
    Route::get('/inventory', [InventoryController::class, 'index']);
    Route::get('/inventory/{id}', [InventoryController::class, 'show']);

    Route::middleware('react.write')->group(function () {
        Route::post('/collection-events', [CollectionEventController::class, 'store']);
        Route::post('/deliveries', [DeliveryController::class, 'store']);
    });
});
