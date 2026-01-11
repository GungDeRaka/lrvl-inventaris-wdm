<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
   public function up()
{
    // Cek jika driver BUKAN sqlite, baru jalankan perintah MySQL ini
    if (DB::getDriverName() !== 'sqlite') {
        DB::statement("ALTER TABLE rab_pengadaans MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'diajukan'");
    } 
    else {
        // OPSI UNTUK SQLITE (Biasanya di-skip saja saat testing tidak apa-apa, 
        // asalkan logika aplikasi tidak hancur)
        
        // Atau gunakan cara Schema Builder di sini:
        Schema::table('rab_pengadaans', function (Blueprint $table) {
            $table->string('status', 50)->default('diajukan')->change();
        });
    }
}

    public function down()
    {
        // Kembalikan ke status lama jika rollback (hati-hati data baru bisa hilang/error)
        DB::statement("ALTER TABLE rab_pengadaans MODIFY COLUMN status VARCHAR(50)");
        DB::statement("ALTER TABLE rab_pengadaans MODIFY COLUMN status ENUM('diajukan', 'disetujui', 'ditolak') NOT NULL DEFAULT 'diajukan'");
    }
};