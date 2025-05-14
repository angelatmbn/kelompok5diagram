<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('detail_penjualan')) {
            Schema::create('detail_penjualan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('penjualan_id')->constrained('penjualan')->onDelete('cascade');
                $table->string('menu_id');
                $table->integer('jumlah'); // jumlah yang dibeli
                $table->integer('harga_satuan'); // harga satuan saat transaksi
                $table->integer('subtotal'); // jumlah x harga
                $table->timestamps();

                // Karena menu_id bukan foreignId() bawaan, maka perlu foreign() manual
                $table->foreign('menu_id')->references('id_menu')->on('menu')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_penjualan');
    }
};
