<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('rab_pengadaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users'); // Penjaga Gudang yang mengajukan
            $table->enum('status', ['diajukan', 'disetujui', 'ditolak'])->default('diajukan');
            $table->text('keterangan')->nullable(); // Alasan umum pengajuan
            $table->date('tanggal_pengajuan');
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users'); // Kepala Gudang yang memproses
            $table->date('tanggal_keputusan')->nullable();
            $table->text('catatan_kepala')->nullable(); // Catatan dari Kepala Gudang
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('rab_pengadaans');
    }
};