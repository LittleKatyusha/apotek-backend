<?php
// app/Models/Obat.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama_obat',
        'deskripsi',
        'kategori',
        'harga',
        'stok',
        'gambar_url',
    ];
}