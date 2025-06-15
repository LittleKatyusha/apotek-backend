<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Obat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /**
     * Mengambil data statistik untuk dasbor admin.
     */
    public function getStats()
    {
        // Menghitung total pendapatan dari pesanan yang statusnya 'Selesai'
        $totalPendapatan = Order::where('status', 'Selesai')->sum('total_harga');

        // Menghitung jumlah pesanan baru (yang belum selesai atau dibatalkan)
        $pesananBaru = Order::whereNotIn('status', ['Selesai', 'Dibatalkan'])->count();

        // Menghitung jumlah total pelanggan (user dengan role 'user')
        $jumlahPelanggan = User::where('role', 'user')->count();

        // Menghitung jumlah total produk yang ada
        $jumlahProduk = Obat::count();

        // Mengembalikan semua data dalam satu respons JSON
        return response()->json([
            'total_pendapatan' => $totalPendapatan,
            'pesanan_baru' => $pesananBaru,
            'jumlah_pelanggan' => $jumlahPelanggan,
            'jumlah_produk' => $jumlahProduk,
        ]);
    }
}
