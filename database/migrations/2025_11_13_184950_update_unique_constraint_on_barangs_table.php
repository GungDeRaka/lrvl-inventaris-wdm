<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            // 1. Hapus index unique lama pada kode_barang (nama index biasanya barangs_kode_barang_unique)
            $table->dropUnique(['kode_barang']);

            // 2. Tambahkan index unique baru kombinasi (kode_barang + ruangan_id)
            // Artinya: Kode barang boleh sama, ASALKAN ruangannya beda.
            $table->unique(['kode_barang', 'ruangan_id']);
        });
    }

    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropUnique(['kode_barang', 'ruangan_id']);
            $table->unique('kode_barang');
        });
    }
};