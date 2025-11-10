<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            // Kolom baru untuk mencatat waktu pengembalian yang sebenarnya
            $table->timestamp('waktu_pengembalian_aktual')->nullable()->after('waktu_kembali');
        });
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropColumn('waktu_pengembalian_aktual');
        });
    }
};