<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['user_id', 'consultant_id', 'status'];

    /**
     * Mendapatkan semua pesan dalam sesi konsultasi ini.
     */
    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * Mendapatkan pengguna yang memulai konsultasi.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Mendapatkan konsultan untuk sesi ini.
     */
    public function consultant()
    {
        return $this->belongsTo(User::class, 'consultant_id');
    }
}