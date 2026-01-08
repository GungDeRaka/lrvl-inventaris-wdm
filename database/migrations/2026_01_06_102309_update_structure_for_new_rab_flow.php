<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. SOLUSI ERROR: Ubah kolom status jadi VARCHAR (String) dulu agar fleksibel
        // Ini membiarkan semua data lama tetap aman sementara waktu
        DB::statement("ALTER TABLE rab_pengadaans MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'diajukan'");

        // 2. Mapping Data Lama ke Status Baru
        // Data lama yang statusnya 'disetujui' kita anggap 'selesai' (karena di sistem lama artinya sudah oke)
        DB::table('rab_pengadaans')
            ->where('status', 'disetujui')
            ->update(['status' => 'selesai']);
        
        // (Opsional) Jika ada status lain yang aneh, kembalikan ke diajukan
        // DB::table('rab_pengadaans')->whereNotIn('status', ['diajukan', 'selesai', 'ditolak'])->update(['status' => 'diajukan']);

        // 3. Update Tabel Users (Tambah No HP & Role Bendahara)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'no_hp')) {
                $table->string('no_hp', 20)->nullable()->after('email');
            }
        });

        // Ubah kolom peran untuk mendukung 'bendahara'
        DB::statement("ALTER TABLE users MODIFY COLUMN peran ENUM('kepala_gudang', 'penjaga_gudang', 'bendahara') NOT NULL");

        // 4. TERAPKAN ENUM BARU (Sekarang aman karena tidak ada lagi status 'disetujui')
        DB::statement("ALTER TABLE rab_pengadaans MODIFY COLUMN status ENUM('diajukan', 'menunggu_bendahara', 'disetujui_bendahara', 'menunggu_verifikasi', 'selesai', 'ditolak') NOT NULL DEFAULT 'diajukan'");

        // 5. Update Tabel Rab Items
        Schema::table('rab_items', function (Blueprint $table) {
            if (!Schema::hasColumn('rab_items', 'kode_barang_fix')) {
                $table->string('kode_barang_fix')->nullable()->after('nama_barang_baru');
                $table->foreignId('kategori_id_fix')->nullable()->after('kode_barang_fix'); 
                $table->foreignId('ruangan_id_fix')->nullable()->after('kategori_id_fix');
            }
        });
    }

    public function down()
    {
        // Kembalikan ke status lama jika rollback (hati-hati data baru bisa hilang/error)
        DB::statement("ALTER TABLE rab_pengadaans MODIFY COLUMN status VARCHAR(50)");
        DB::statement("ALTER TABLE rab_pengadaans MODIFY COLUMN status ENUM('diajukan', 'disetujui', 'ditolak') NOT NULL DEFAULT 'diajukan'");
    }
};