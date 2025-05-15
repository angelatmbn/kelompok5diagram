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
        Schema::create('stok_log', function (Blueprint $table) {
            $table->id();
            $table->string('menu_id');
            $table->integer('perubahan_stok'); // Bisa ubah jadi 'perubahan_stok' kalau mau bisa positif & negatif
            $table->string('keterangan');
            $table->timestamp('tanggal_kejadian')->useCurrent(); // Waktu kejadian sebenarnya
            $table->timestamps();

            $table->foreign('menu_id')->references('id_menu')->on('menu')->onDelete('cascade');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('stok_log');
    }
};
