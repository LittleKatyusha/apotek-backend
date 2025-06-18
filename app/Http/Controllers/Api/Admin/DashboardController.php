<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Obat;

class DashboardController extends Controller
{
   public function dashboardStats()
{
    $totalPendapatan = Order::where('status', 'Selesai')->sum('total_harga');

    $pesananBaru = Order::where('status', 'Menunggu Pembayaran')->count();
    $jumlahPelanggan = \App\Models\User::count();
    $jumlahProduk = \App\Models\Obat::count();

    return response()->json([
        'total_pendapatan' => $totalPendapatan,
        'pesanan_baru' => $pesananBaru,
        'jumlah_pelanggan' => $jumlahPelanggan,
        'jumlah_produk' => $jumlahProduk,
    ]);
}
}
