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
        Schema::create('pengadaan_barangs', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke tabel barangs
            $table->foreignId('barang_id')->constrained()->onDelete('cascade');
            $table->integer('jumlah');
            // Menggunakan decimal untuk harga agar bisa menampung nilai rupiah
            $table->decimal('harga_satuan', 15, 2)->nullable(); // 15 digit total, 2 di belakang koma
            $table->string('sumber_dana');
            $table->date('tanggal_pengadaan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengadaan_barangs');
    }
};