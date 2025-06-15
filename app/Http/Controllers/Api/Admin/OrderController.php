<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar semua pesanan untuk admin dengan pagination.
     */
    public function index()
    {
        // PERBAIKAN: Ganti .get() menjadi .paginate()
        // Ini akan secara otomatis membagi hasil menjadi halaman-halaman
        // dan mengembalikan objek JSON yang sesuai dengan yang diharapkan frontend.
        $orders = Order::with('user') // Mengambil data user yang berelasi
                        ->orderBy('created_at', 'desc')
                        ->paginate(15); // Mengambil 15 pesanan per halaman

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
            'status' => 'required|string|in:Menunggu Pembayaran,Diproses,Dikirim,Selesai,Dibatalkan',
        ]);

        $order->status = $request->status;
        $order->save();

        return response()->json([
            'message' => 'Status pesanan berhasil diperbarui!',
            'order' => $order,
        ]);
    }
}
