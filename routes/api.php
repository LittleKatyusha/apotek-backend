<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ObatController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ConsultationController;

// Rute Publik (tidak butuh login)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/obat', [ObatController::class, 'index']);
Route::get('/obat/{id}', [ObatController::class, 'show']);

// Rute yang Membutuhkan Otentikasi
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Rute Pesanan
    Route::post('/checkout', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);

    // Rute Konsultasi
    Route::post('/consultations', [ConsultationController::class, 'store']);
    Route::get('/consultations/{consultation}/messages', [ConsultationController::class, 'fetchMessages']);
    Route::post('/consultations/{consultation}/messages', [ConsultationController::class, 'sendMessage']);
});