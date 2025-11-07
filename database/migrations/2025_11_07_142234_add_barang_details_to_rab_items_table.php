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
        Schema::table('rab_items', function (Blueprint $table) {
            // Kolom untuk detail barang baru
            $table->string('kode_barang_baru')->nullable()->after('nama_barang_baru');
            $table->foreignId('kategori_id')->nullable()->constrained()->after('spesifikasi');
            $table->foreignId('ruangan_id')->nullable()->constrained()->after('kategori_id');
            $table->integer('stok_minimum_baru')->default(0)->after('ruangan_id');
        });
    }

    public function down(): void
    {
        Schema::table('rab_items', function (Blueprint $table) {
            $table->dropForeign(['kategori_id']);
            $table->dropForeign(['ruangan_id']);
            $table->dropColumn(['kode_barang_baru', 'kategori_id', 'ruangan_id', 'stok_minimum_baru']);
        });
    }
};
