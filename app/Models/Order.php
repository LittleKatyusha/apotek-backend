<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
   protected $fillable = [
    'user_id',
    'nama_penerima',
    'alamat_pengiriman',
    'telepon',
    'total_harga', // ini akan menjadi subtotal
    'ongkir',
    'status',
    ];

    /**
     * Mendapatkan user yang memiliki order ini.
     * Relasi "belongsTo" (milik dari).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan semua item dalam order ini.
     * Relasi "hasMany" (memiliki banyak).
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}