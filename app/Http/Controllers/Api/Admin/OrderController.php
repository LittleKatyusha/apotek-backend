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
        $orders = Order::with('user')
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);

        return response()->json($orders);
    }

    /**
     * Menampilkan detail satu pesanan.
     */
    public function show(Order $order)
    {
        $order->load('user', 'items.obat');
        return response()->json($order);
    }

    /**
     * Menghapus pesanan.
     */
    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(null, 204);
    }

    /**
     * Memperbarui status pesanan.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:Menunggu Pembayaran,Dibayar,Diproses,Dikirim,Selesai,Dibatalkan',
        ]);

        $order->status = $request->status;
        $order->save();

        return response()->json([
            'message' => 'Status pesanan berhasil diperbarui!',
            'order' => $order,
        ]);
    }
}
