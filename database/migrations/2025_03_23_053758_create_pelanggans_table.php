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
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->id();
            $table->string('id_pelanggan')->unique();
            $table->string('nama');
            $table->string('alamat');
            $table->string('telepon')->nullable();
            $table->string('email')->unique()->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan'])->nullable();
            $table->timestamps();
});
       

    }
    
    public function down(): void
    {
        Schema::dropIfExists('pelanggan');
    }
};
