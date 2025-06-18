<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Obat;
use Illuminate\Support\Facades\DB; // 1. Import DB Facade

class ObatController extends Controller
{
    /**
     * Menampilkan semua produk dengan pencarian.
     */
public function index(Request $request)
{
    $query = Obat::query();

    if ($request->has('search')) {
        $searchTerm = $request->search;
        $query->where(function ($q) use ($searchTerm) {
            $q->where('nama_obat', 'like', '%' . $searchTerm . '%')
              ->orWhere('kategori', 'like', '%' . $searchTerm . '%'); // Tambahkan kategori
        });
    }

    $obats = $query->get();
    return response()->json($obats);
}

    
    /**
     * Menampilkan 4 produk terlaris.
     */
    public function bestsellers()
    {
        // Ambil 4 ID obat yang paling banyak terjual dari tabel order_items
        $topObatIds = DB::table('order_items')
            ->select('obat_id', DB::raw('SUM(kuantitas) as total_sold'))
            ->groupBy('obat_id')
            ->orderBy('total_sold', 'desc')
            ->limit(4)
            ->pluck('obat_id');

        // Jika belum ada penjualan sama sekali, tampilkan 4 produk terbaru sebagai gantinya
        if ($topObatIds->isEmpty()) {
            $obats = Obat::latest()->limit(4)->get();
            return response()->json($obats);
        }

        // Ambil detail produk berdasarkan ID terlaris dan jaga urutannya
        $obats = Obat::whereIn('id', $topObatIds)
            ->orderByRaw("FIELD(id, " . $topObatIds->implode(',') . ")")
            ->get();
            
        return response()->json($obats);
    }

    /**
     * Menampilkan detail satu produk.
     */
    public function show($id)
    {
        $obat = Obat::findOrFail($id);
        return response()->json($obat);
    }
}
