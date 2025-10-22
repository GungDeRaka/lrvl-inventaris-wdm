<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('rab_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rab_pengadaan_id')->constrained('rab_pengadaans')->onDelete('cascade');
            $table->foreignId('barang_id')->nullable()->constrained('barangs'); // Jika menambah stok barang yg ada
            $table->string('nama_barang_baru')->nullable(); // Jika mengajukan barang baru
            $table->text('spesifikasi')->nullable();
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('harga_total', 15, 2); // Disimpan agar tidak berubah jika harga satuan diedit
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('rab_items');
    }
};