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
        Schema::create('penggajian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pegawai')->constrained('pegawaii')->onDelete('cascade');
            $table->date('tanggal');
            $table->integer('gaji_pokok');
            $table->integer('potongan')->default(0);
            $table->integer('total_gaji');
            $table->enum('status_pembayaran', ['belum', 'dibayar'])->default('belum');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penggajian');
    }
};
