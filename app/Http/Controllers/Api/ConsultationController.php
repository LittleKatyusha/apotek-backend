<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\User;
use Illuminate\Http\Request;
use App\Events\ChatMessageSent; // Pastikan Event ini di-import

class ConsultationController extends Controller
{
    /**
     * Memulai sesi konsultasi baru antara pengguna yang login dan seorang admin.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        // Asumsikan konsultan adalah user pertama dengan role 'admin'.
        $consultant = User::where('role', 'admin')->first();
        
        if (!$consultant) {
            return response()->json(['message' => 'Tidak ada konsultan yang tersedia saat ini.'], 404);
        }

        // Cari atau buat sesi konsultasi baru yang statusnya 'active'.
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
     * Mengambil semua riwayat pesan dari sebuah sesi konsultasi.
     */
    public function fetchMessages(Request $request, Consultation $consultation)
    {
        if ($request->user()->id !== $consultation->user_id && $request->user()->id !== $consultation->consultant_id) {
            return response()->json(['message' => 'Tidak diizinkan untuk mengakses riwayat chat ini.'], 403);
        }

        $messages = $consultation->messages()->with('sender:id,name')->get();
        
        return response()->json($messages);
    }

    /**
     * Menyimpan pesan baru ke database dan menyiarkannya secara real-time.
     */
    public function sendMessage(Request $request, Consultation $consultation)
    {
        if ($request->user()->id !== $consultation->user_id && $request->user()->id !== $consultation->consultant_id) {
            return response()->json(['message' => 'Tidak diizinkan untuk mengirim pesan di sesi ini.'], 403);
        }

        $request->validate(['message' => 'required|string']);

        $message = $consultation->messages()->create([
            'sender_id' => $request->user()->id,
            'message' => $request->message,
        ]);
        
        $message->load('sender:id,name');

        broadcast(new ChatMessageSent($message))->toOthers();

        return response()->json($message, 201);
    }
}
