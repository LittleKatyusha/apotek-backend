<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; // 1. Import Request
use App\Models\Obat;

class ObatController extends Controller
{
    // 2. Modifikasi fungsi index untuk menerima Request
    public function index(Request $request)
    {
        // 3. Buat query builder
        $query = Obat::query();

        // 4. Jika ada parameter 'search' di URL, tambahkan kondisi WHERE
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where('nama_obat', 'like', '%' . $searchTerm . '%');
        }

        // 5. Eksekusi query dan ambil hasilnya
        $obats = $query->get();

        return response()->json($obats);
    }

    public function show($id)
    {
        $obat = Obat::findOrFail($id);
        return response()->json($obat);
    }
}