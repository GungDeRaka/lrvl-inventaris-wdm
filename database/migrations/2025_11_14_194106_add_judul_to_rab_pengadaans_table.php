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
        Schema::table('rab_pengadaans', function (Blueprint $table) {
            $table->string('judul', 50)->after('user_id'); // Maksimal 50 karakter
        });
    }

    public function down(): void
    {
        Schema::table('rab_pengadaans', function (Blueprint $table) {
            $table->dropColumn('judul');
        });
    }
};
