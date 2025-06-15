<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ObatController;
// ... import lainnya
use App\Http\Controllers\Api\OrderController; 
use App\Http\Controllers\Api\ConsultationController;
// 1. Pastikan kita mengimpor AdminObatController dengan benar
use App\Http\Controllers\Api\Admin\ObatController as AdminObatController; 
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;

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
    
    // Rute Pesanan & Konsultasi
    Route::post('/checkout', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);
    Route::post('/consultations', [ConsultationController::class, 'store']);
    Route::get('/consultations/{consultation}/messages', [ConsultationController::class, 'fetchMessages']);
    Route::post('/consultations/{consultation}/messages', [ConsultationController::class, 'sendMessage']);

    // Grup Rute Khusus Admin
    Route::prefix('admin')->middleware('auth.admin')->group(function () {
        // 2. Pastikan kita menggunakan alias yang benar di sini
        Route::apiResource('produk', AdminObatController::class);
        Route::get('pesanan', [AdminOrderController::class, 'index']); // GET /api/admin/pesanan
        Route::get('pesanan/{order}', [AdminOrderController::class, 'show']); // GET /api/admin/pesanan/{id}
        Route::delete('pesanan/{order}', [AdminOrderController::class, 'destroy']);
        Route::put('pesanan/{order}/status', [AdminOrderController::class, 'updateStatus']); 
    });
});
