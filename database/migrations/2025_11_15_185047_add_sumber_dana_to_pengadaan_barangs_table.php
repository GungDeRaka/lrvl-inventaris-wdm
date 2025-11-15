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
        Schema::table('pengadaan_barangs', function (Blueprint $table) {
            // Tambahkan kolom baru
            $table->foreignId('sumber_dana_id')->nullable()->after('barang_id')->constrained('sumber_danas')->onDelete('set null');
            $table->decimal('total_harga', 15, 2)->default(0)->after('harga_satuan');
            // Hapus kolom lama jika ada (misalnya kolom 'sumber_dana' string manual)
            if (Schema::hasColumn('pengadaan_barangs', 'sumber_dana')) {
                $table->dropColumn('sumber_dana');
            }
            // Pastikan kolom user_id ada (jika belum)
            if (!Schema::hasColumn('pengadaan_barangs', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('tanggal_pengadaan')->constrained('users');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengadaan_barangs', function (Blueprint $table) {
            $table->dropForeign(['sumber_dana_id']);
            $table->dropColumn(['sumber_dana_id', 'total_harga']);
            // Kembalikan kolom lama jika rollback (opsional)
            $table->string('sumber_dana')->nullable();
        });
    }
};
