<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('rab_items', function (Blueprint $table) {
            // Cek apakah kolom SUDAH ADA. Jika belum, baru tambahkan.
            if (!Schema::hasColumn('rab_items', 'barang_id')) {
                $table->foreignId('barang_id')
                      ->nullable()
                      ->after('rab_pengadaan_id')
                      ->constrained('barangs')
                      ->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('rab_items', function (Blueprint $table) {
            // Cek apakah kolom ADA sebelum menghapus
            if (Schema::hasColumn('rab_items', 'barang_id')) {
                // Hapus Foreign Key dulu (format: nama_tabel_nama_kolom_foreign)
                $table->dropForeign(['barang_id']); 
                $table->dropColumn('barang_id');
            }
        });
    }
};