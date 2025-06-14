<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'consultation_id',
        'sender_id',
        'message'
    ];

    /**
     * Mendapatkan user pengirim pesan ini.
     * PASTIKAN FUNGSI INI BERADA DI DALAM CLASS
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}