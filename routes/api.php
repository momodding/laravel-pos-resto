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

Route::post('/login', 'App\Http\Controllers\API\AuthController@login');
Route::post('/logout', 'App\Http\Controllers\API\AuthController@logout')->middleware('auth:sanctum');
// products api resource with middleware auth:sanctum
Route::apiResource('/product', 'App\Http\Controllers\API\ProductController')->middleware('auth:sanctum');
// categories api resource with middleware auth:sanctum
Route::apiResource('/category', 'App\Http\Controllers\API\CategoryController')->middleware('auth:sanctum');
Route::post('/order', 'App\Http\Controllers\API\OrderController@saveOrder')->middleware('auth:sanctum');
// discounts api resource with middleware auth:sanctum
Route::apiResource('/discount', 'App\Http\Controllers\API\DiscountController')->middleware('auth:sanctum');
