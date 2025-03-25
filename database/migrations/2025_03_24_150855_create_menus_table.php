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
        Schema::create('menu', function (Blueprint $table) {
            $table->string('id_menu')->primary();
            $table->string('nama_menu');
            $table->string('id_kategori');
            $table->string('nama_kategori');
            $table->integer('harga');
            $table->string('foto');
            $table->timestamps();

            $table->foreign('id_kategori')
                ->references('id_kategori')
                ->on('kategori_menu')
                ->onUpdate('cascade') // Update otomatis jika id_kategori berubah
                ->onDelete('cascade'); // Hapus data jika kategori dihapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};
