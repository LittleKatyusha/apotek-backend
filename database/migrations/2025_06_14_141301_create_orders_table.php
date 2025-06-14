<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 // database/migrations/xxxx_xx_xx_xxxxxx_create_orders_table.php
public function up(): void
{
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->string('nama_penerima');
        $table->text('alamat_pengiriman');
        $table->string('telepon');
        $table->integer('total_harga');
        $table->string('status')->default('Menunggu Pembayaran');
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
