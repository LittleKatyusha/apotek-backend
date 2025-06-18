<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import semua controller yang digunakan
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ObatController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ConsultationController;
use App\Http\Controllers\Api\Admin\ObatController as AdminObatController;
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Di sini kita mendaftarkan semua "alamat" untuk API kita.
*/

// == RUTE PUBLIK ==
// Rute ini bisa diakses siapa saja tanpa perlu login.
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/obat', [ObatController::class, 'index']);
Route::get('/obat/{id}', [ObatController::class, 'show']);
Route::get('/produk/terlaris', [ObatController::class, 'bestsellers']);


// == RUTE TERPROTEKSI UNTUK PENGGUNA YANG LOGIN ==
// Semua rute di dalam grup ini memerlukan token otentikasi (login).
Route::middleware('auth:sanctum')->group(function () {
    
    // Mengambil data user yang sedang login
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Rute untuk Alur Pesanan Pengguna
    Route::post('/checkout', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);
    
    // Rute untuk Fitur Konsultasi
    Route::post('/consultations', [ConsultationController::class, 'store']);
    Route::get('/consultations/{consultation}/messages', [ConsultationController::class, 'fetchMessages']);
    Route::post('/consultations/{consultation}/messages', [ConsultationController::class, 'sendMessage']);
    // Rute baru untuk konfirmasi pembayaran
    Route::post('/orders/{order}/confirm-payment', [OrderController::class, 'confirmPayment']);

    // == GRUP RUTE KHUSUS ADMIN ==
    // Rute ini hanya bisa diakses oleh user yang login DAN memiliki role 'admin'.
    Route::prefix('admin')->middleware('auth.admin')->group(function () {
        
        // Rute Statistik Dasbor
        Route::get('/dashboard-stats', [DashboardController::class, 'dashboardStats']);

        
        // Rute CRUD Produk oleh Admin
        Route::apiResource('produk', AdminObatController::class);
        
        // Rute Manajemen Pesanan oleh Admin
        Route::get('pesanan', [AdminOrderController::class, 'index']);
        Route::get('pesanan/{order}', [AdminOrderController::class, 'show']);
        Route::delete('pesanan/{order}', [AdminOrderController::class, 'destroy']);
        Route::put('pesanan/{order}/status', [AdminOrderController::class, 'updateStatus']);

    });
});
