<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar semua pesanan untuk admin.
     */
    public function index()
    {
        $orders = Order::with('user:id,name')
                        ->orderBy('created_at', 'desc')
                        ->get();
        return response()->json($orders);
    }

    /**
     * Menampilkan detail satu pesanan.
     */
    public function show(Order $order)
    {
        // Muat semua relasi yang diperlukan untuk halaman detail
        $order->load('user', 'items.obat');
        return response()->json($order);
    }

    /**
     * Menghapus pesanan.
     */
    public function destroy(Order $order)
    {
        // Menghapus order akan otomatis menghapus order_items
        // karena kita menggunakan onDelete('cascade') di migration.
        $order->delete();
        return response()->json(null, 204); // 204 No Content
    }
}
