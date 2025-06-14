<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ObatController;
use App\Http\Controllers\Api\OrderController;

// Rute Publik
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/obat', [ObatController::class, 'index']);
Route::get('/obat/{id}', [ObatController::class, 'show']);

// Rute yang Membutuhkan Otentikasi
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/checkout', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
});