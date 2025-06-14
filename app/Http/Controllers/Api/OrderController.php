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
        $validator = Validator::make($request->all(), [
            'nama_penerima' => 'required|string|max:255',
            'alamat_pengiriman' => 'required|string',
            'telepon' => 'required|string|max:20',
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:obats,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = Auth::user();
        $orderItemsData = $request->items;

        try {
            $order = DB::transaction(function () use ($user, $orderItemsData, $request) {
                
                $totalHarga = 0;
                foreach ($orderItemsData as $itemData) {
                    $obat = Obat::find($itemData['id']);
                    $totalHarga += $obat->harga * $itemData['quantity'];
                }

                $newOrder = Order::create([
                    'user_id' => $user->id,
                    'nama_penerima' => $request->nama_penerima,
                    'alamat_pengiriman' => $request->alamat_pengiriman,
                    'telepon' => $request->telepon,
                    'total_harga' => $totalHarga,
                    'status' => 'Menunggu Pembayaran',
                ]);

                foreach ($orderItemsData as $itemData) {
                    $obat = Obat::find($itemData['id']);
                    $newOrder->items()->create([
                        'obat_id' => $itemData['id'],
                        'kuantitas' => $itemData['quantity'],
                        'harga' => $obat->harga,
                    ]);
                }

                return $newOrder;
            });

            return response()->json([
                'message' => 'Pesanan berhasil dibuat!',
                'order' => $order->load('items.obat'),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat membuat pesanan.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}