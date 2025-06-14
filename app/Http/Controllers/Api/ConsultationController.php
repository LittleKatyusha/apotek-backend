<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\User;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    /**
     * Memulai atau menemukan sesi konsultasi yang sudah ada.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        // Untuk sekarang, kita asumsikan konsultan adalah user pertama dengan role 'admin'
        $consultant = User::where('role', 'admin')->first();
        if (!$consultant) {
            return response()->json(['message' => 'Tidak ada konsultan yang tersedia saat ini.'], 404);
        }

        // Cari atau buat sesi konsultasi baru yang aktif
        $consultation = Consultation::firstOrCreate(
            [
                'user_id' => $user->id,
                'consultant_id' => $consultant->id,
                'status' => 'active',
            ]
        );

        return response()->json($consultation, 201);
    }

    /**
     * Mengambil semua pesan dari sebuah sesi konsultasi.
     */
    public function fetchMessages(Request $request, Consultation $consultation)
    {
        // Otorisasi: Pastikan user yang meminta adalah bagian dari konsultasi
        if ($request->user()->id !== $consultation->user_id && $request->user()->id !== $consultation->consultant_id) {
            return response()->json(['message' => 'Tidak diizinkan'], 403);
        }

        // Ambil semua pesan beserta data pengirimnya
        $messages = $consultation->messages()->with('sender:id,name')->get();
        return response()->json($messages);
    }

    /**
     * Mengirim pesan baru dalam sebuah sesi konsultasi.
     */
    public function sendMessage(Request $request, Consultation $consultation)
    {
        // Otorisasi
        if ($request->user()->id !== $consultation->user_id && $request->user()->id !== $consultation->consultant_id) {
            return response()->json(['message' => 'Tidak diizinkan'], 403);
        }

        $request->validate(['message' => 'required|string']);

        $message = $consultation->messages()->create([
            'sender_id' => $request->user()->id,
            'message' => $request->message,
        ]);

        return response()->json($message->load('sender:id,name'), 201);
    }
}