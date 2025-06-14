<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_obats_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obats', function (Blueprint $table) {
            $table->id();
            $table->string('nama_obat');
            $table->text('deskripsi');
            $table->string('kategori');
            $table->integer('harga');
            $table->integer('stok');
            $table->string('gambar_url')->nullable();
            $table->timestamps();
        });
    }
    // ...
};