<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//use App\Http\Controllers\ProductController;


//Route::get('/products', [ProductController::class, 'getProducts']);
//Route::get('/products/{id}', [ProductController::class, 'getProductItem']);
//Route::post('/products', [ProductController::class, 'createProduct']);
//Route::match(['put', 'patch'], '/products/{id}', [ProductController::class, 'updateProduct']);
//Route::delete('/products/{id}', [ProductController::class, 'deleteProduct']);
//

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\CourierController;
use App\Http\Controllers\Api\OrderController;

Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('clients', ClientController::class);
Route::apiResource('couriers', CourierController::class);
Route::apiResource('orders', OrderController::class);
