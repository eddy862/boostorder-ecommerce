<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\GoogleController;

require __DIR__.'/auth.php';

Route::get('/auth/google', [GoogleController::class, 'redirect']);
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);

Route::get('/api/user', function () {
    return Auth::user();
});

Route::get('/api/products', [ProductController::class, 'index']);
Route::get('/api/products/{id}', [ProductController::class, 'show']);

Route::get('/api/cart', [CartController::class, 'index']);
Route::post('/api/cart/add/{id}', [CartController::class, 'add']);
Route::post('/api/cart/remove/{id}', [CartController::class, 'remove']);
Route::post('/api/cart/update/{id}', [CartController::class, 'update']);

Route::post('/api/checkout', [OrderController::class, 'checkout'])->middleware('auth', 'verified'); 

Route::get('/api/orders', [OrderController::class, 'index'])->middleware('auth', 'verified');
Route::post('/api/orders/{id}/status', [OrderController::class, 'updateStatus'])->middleware('auth', 'verified');

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');


