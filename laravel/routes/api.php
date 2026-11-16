<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\CourierController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

// Публічні маршрути (токен не потрібен)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
Route::post('/token/refresh', [AuthController::class, 'refresh']);

// Захищені маршрути (потрібен валідний JWT)
Route::middleware('jwt')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Каталог доступний усім авторизованим користувачам
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);

    // Перегляд одного замовлення
    Route::get('/orders/{order}', [OrderController::class, 'show'])->middleware('can:view,order');

    // Права Менеджера та Адміна: перегляд списків, створення та редагування
    Route::middleware('can:manager')->group(function () {
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::match(['put', 'patch'], '/categories/{category}', [CategoryController::class, 'update']);

        Route::post('/products', [ProductController::class, 'store']);
        Route::match(['put', 'patch'], '/products/{product}', [ProductController::class, 'update']);

        Route::apiResource('clients', ClientController::class)->except(['destroy']);
        Route::apiResource('couriers', CourierController::class)->except(['destroy']);

        Route::get('/orders', [OrderController::class, 'index']);
        Route::post('/orders', [OrderController::class, 'store']);
        Route::match(['put', 'patch'], '/orders/{order}', [OrderController::class, 'update']);
    });

    // Права Адміна: видалення будь-яких сутностей[cite: 10]
    Route::middleware('can:admin')->group(function () {
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);
        Route::delete('/clients/{client}', [ClientController::class, 'destroy']);
        Route::delete('/couriers/{courier}', [CourierController::class, 'destroy']);
        Route::delete('/orders/{order}', [OrderController::class, 'destroy']);
    });
});
