<?php

use App\Models\Consultation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Aturan otorisasi untuk channel konsultasi
Broadcast::channel('consultation.{consultation}', function ($user, Consultation $consultation) {
    // Izinkan jika ID user yang login sama dengan user_id atau consultant_id dari sesi konsultasi
    return $user->id === $consultation->user_id || $user->id === $consultation->consultant_id;
});
