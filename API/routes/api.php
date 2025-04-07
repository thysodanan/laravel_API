<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::post('/register',[AuthController::class, 'register'])->name('register');
Route::post('/login',[AuthController::class, 'login'])->name('login');
// Route::post('/register',[AuthController::class, 'register'])->name('register');
Route::middleware('auth:sanctum')->group(function(){
    Route::get('/product',[ProductController::class, 'index']);
    Route::post('/product',[ProductController::class, 'store']);
    Route::delete('/product/{id}',[ProductController::class, 'destroy']);
    Route::get('/product/{id}',[ProductController::class, 'getById']);
    Route::post('/product/{id}',[ProductController::class, 'update']);
    Route::post('/logout',[AuthController::class, 'logout'])->name('logout');
});
