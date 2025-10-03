<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('transaksis', function (Blueprint $table) {
            // Hapus foreign key sebelum menghapus kolom
            $table->dropForeign(['barang_id']);
            $table->dropColumn(['barang_id', 'kuantitas']);
        });
    }
    public function down(): void {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->foreignId('barang_id')->constrained();
            $table->integer('kuantitas')->default(1);
        });
    }
};