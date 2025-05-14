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
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')->constrained('pelanggan')->onDelete('cascade');
            $table->string('no_faktur')->nullable();
            $table->enum('status', ['pesan', 'bayar'])->default('pesan');
            $table->dateTime('tgl');
            $table->decimal('total_tagihan', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};
