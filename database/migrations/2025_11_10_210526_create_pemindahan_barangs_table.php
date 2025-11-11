<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pemindahan_barangs', function (Blueprint $table) {
            $table->id();
            // Barang asal (sumber)
            $table->foreignId('barang_asal_id')->constrained('barangs')->onDelete('cascade');
            // Barang tujuan (baru)
            $table->foreignId('barang_tujuan_id')->constrained('barangs')->onDelete('cascade');
            $table->integer('jumlah_dipindahkan');
            $table->foreignId('user_id')->constrained('users'); // Kepala Gudang
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('pemindahan_barangs');
    }
};