<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // 1. Import Storage
use Illuminate\Support\Facades\Validator;

class ObatController extends Controller
{
    /**
     * Menampilkan semua produk untuk admin.
     */
    public function index()
    {
        return Obat::orderBy('created_at', 'desc')->get();
    }

    /**
     * Menyimpan produk baru dengan file upload.
     */
    public function store(Request $request)
    {
        // 2. Ubah aturan validasi
        $validator = Validator::make($request->all(), [
            'nama_obat' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kategori' => 'required|string',
            'harga' => 'required|integer|min:0',
            'stok' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi untuk file gambar
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $validator->validated();

        // 3. Proses file upload jika ada
        if ($request->hasFile('gambar')) {
            // Simpan gambar ke storage/app/public/produk-images
            $path = $request->file('gambar')->store('produk-images', 'public');
            // Simpan path yang akan diakses publik ke database
            $data['gambar_url'] = Storage::url($path);
        }

        $obat = Obat::create($data);

        return response()->json($obat, 201);
    }

    /**
     * Menampilkan satu produk spesifik.
     */
    public function show(Obat $obat)
    {
        return response()->json($obat);
    }

    /**
     * Memperbarui produk dengan file upload.
     */
    public function update(Request $request, Obat $obat)
    {
        $validator = Validator::make($request->all(), [
            'nama_obat' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kategori' => 'required|string',
            'harga' => 'required|integer|min:0',
            'stok' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada untuk menghemat ruang
            if ($obat->gambar_url) {
                // Ubah URL menjadi path storage untuk dihapus
                $oldPath = str_replace('/storage', '', $obat->gambar_url);
                Storage::disk('public')->delete($oldPath);
            }
            // Simpan gambar baru dan perbarui path
            $path = $request->file('gambar')->store('produk-images', 'public');
            $data['gambar_url'] = Storage::url($path);
        }

        $obat->update($data);

        return response()->json($obat);
    }

    /**
     * Menghapus produk.
     */
    public function destroy(Obat $obat)
    {
        // Hapus juga file gambar dari storage
        if ($obat->gambar_url) {
            $oldPath = str_replace('/storage', '', $obat->gambar_url);
            Storage::disk('public')->delete($oldPath);
        }
        
        $obat->delete();
        return response()->json(null, 204);
    }
}
