<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Mengambil riwayat pesanan milik pengguna yang sedang login.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $orders = Order::where('user_id', $user->id)
                        ->with('items.obat')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return response()->json($orders);
    }

    /**
     * Menyimpan pesanan baru dari proses checkout.
     */
    public function store(Request $request)
    {
        // ... (Fungsi store tidak berubah, biarkan seperti sebelumnya)
    }

    /**
     * Membatalkan pesanan milik pengguna.
     */
    public function cancel(Request $request, Order $order)
    {
        // 1. Cek Otorisasi: Apakah pengguna yang login adalah pemilik order ini?
        if ($request->user()->id !== $order->user_id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        // 2. Cek Status: Apakah status pesanan masih 'Menunggu Pembayaran'?
        if ($order->status !== 'Menunggu Pembayaran') {
            return response()->json([
                'message' => 'Pesanan tidak dapat dibatalkan karena sudah diproses.'
            ], 422); // Unprocessable Entity
        }

        // 3. Jika semua pengecekan lolos, ubah status dan simpan
        $order->status = 'Dibatalkan';
        $order->save();

        return response()->json([
            'message' => 'Pesanan berhasil dibatalkan.',
            'order' => $order,
        ]);
    }
}