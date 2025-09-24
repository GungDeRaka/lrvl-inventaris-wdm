<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaksi;
use App\Models\Notification;
use App\Models\User;

class CekPeminjamanNotifikasi extends Command
{
    protected $signature = 'app:cek-peminjaman-notifikasi';
    protected $description = 'Menyimpan notifikasi untuk transaksi yang relevan.';

    public function handle()
    {
        $admins = User::whereIn('peran', ['kepala_gudang', 'penjaga_gudang'])->get();
        if ($admins->isEmpty()) return;

        // Cek transaksi yang sudah jatuh tempo
        $jatuhTempo = Transaksi::where('status', 'dipinjam')
            ->where('waktu_kembali', '<', now())
            ->get();

        foreach ($jatuhTempo as $transaksi) {
            $message = 'Jatuh Tempo: ' . $transaksi->barang->nama_barang . ' oleh ' . $transaksi->siswa->nama . '.';
            // Buat notifikasi untuk setiap admin
            foreach ($admins as $admin) {
                Notification::firstOrCreate(['user_id' => $admin->id, 'message' => $message, 'read_at' => null]);
            }
        }

        $this->info('Pengecekan notifikasi selesai.');
        return 0;
    }
}
