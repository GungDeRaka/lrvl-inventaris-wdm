<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('transaksis', function (Blueprint $table) {
            // Mengubah tipe kolom enum dengan pilihan status yang baru
            $table->enum('status', ['diajukan', 'disetujui', 'dipinjam', 'dikembalikan', 'ditolak'])->default('diajukan')->change();
        });
    }
    public function down(): void {
        Schema::table('transaksis', function (Blueprint $table) {
            // Mengembalikan ke status lama jika migrasi di-rollback
            $table->enum('status', ['dipinjam', 'dikembalikan'])->default('dipinjam')->change();
        });
    }
};