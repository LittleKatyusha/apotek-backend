<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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
     * Menyimpan pesanan baru, memvalidasi stok, dan mengurangi stok.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_penerima' => 'required|string|max:255',
            'alamat_pengiriman' => 'required|string',
            'telepon' => 'required|string|max:20',
            'ongkir' => 'required|integer|min:0',
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
            // Memulai Transaksi Database untuk menjaga konsistensi data
            $order = DB::transaction(function () use ($user, $orderItemsData, $request) {
                
                // 1. Validasi Stok dan Hitung Subtotal
                $subTotal = 0;
                foreach ($orderItemsData as $itemData) {
                    $obat = Obat::find($itemData['id']);
                    // Cek apakah stok mencukupi
                    if ($obat->stok < $itemData['quantity']) {
                        // Jika stok tidak cukup, batalkan transaksi dengan melempar exception
                        throw ValidationException::withMessages([
                            'stok' => 'Stok untuk produk ' . $obat->nama_obat . ' tidak mencukupi. Sisa stok: ' . $obat->stok,
                        ]);
                    }
                    $subTotal += $obat->harga * $itemData['quantity'];
                }

                // Total harga sekarang adalah subtotal + ongkir
                $totalKeseluruhan = $subTotal + $request->ongkir;

                // 2. Buat record di tabel 'orders'
                $newOrder = Order::create([
                    'user_id' => $user->id,
                    'nama_penerima' => $request->nama_penerima,
                    'alamat_pengiriman' => $request->alamat_pengiriman,
                    'telepon' => $request->telepon,
                    'total_harga' => $totalKeseluruhan,
                    'ongkir' => $request->ongkir,
                    'status' => 'Menunggu Pembayaran',
                ]);

                // 3. Buat record di tabel 'order_items' DAN kurangi stok
                foreach ($orderItemsData as $itemData) {
                    $obat = Obat::find($itemData['id']);
                    $newOrder->items()->create([
                        'obat_id' => $itemData['id'],
                        'kuantitas' => $itemData['quantity'],
                        'harga' => $obat->harga,
                    ]);

                    // Kurangi stok produk
                    $obat->decrement('stok', $itemData['quantity']);
                }
                return $newOrder;
            });

            return response()->json([
                'message' => 'Pesanan berhasil dibuat!',
                'order_id' => $order->id,
            ], 201);

        } catch (ValidationException $e) {
            // Tangkap error validasi stok dan kirim pesan yang jelas ke frontend
            return response()->json([
                'message' => 'Gagal membuat pesanan.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Tangkap error lainnya
            return response()->json([
                'message' => 'Terjadi kesalahan saat membuat pesanan.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Membatalkan pesanan milik pengguna.
     */
public function cancel(Request $request, Order $order)
{
    // Cek apakah pesanan milik user yang sedang login
    if ($request->user()->id !== $order->user_id) {
        return response()->json(['message' => 'Tidak diizinkan.'], 403);
    }

    // Hanya bisa dibatalkan jika masih menunggu pembayaran
    if ($order->status !== 'Menunggu Pembayaran') {
        return response()->json(['message' => 'Pesanan tidak dapat dibatalkan.'], 422);
    }

    // Ubah status dan simpan
    $order->status = 'Dibatalkan';
    $order->save();

    return response()->json([
        'message' => 'Pesanan berhasil dibatalkan.',
        'order' => $order,
    ]);
}
}
