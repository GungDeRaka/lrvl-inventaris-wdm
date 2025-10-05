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
        $this->info('Scheduler Pengecekan Notifikasi Dijalankan...');
        $admins = User::whereIn('peran', ['kepala_gudang', 'penjaga_gudang'])->get();
        if ($admins->isEmpty()) {
            $this->info('Tidak ada admin ditemukan.');
            return;
        }

        // 1. Cek transaksi yang sudah jatuh tempo (overdue)
        $jatuhTempo = Transaksi::whereIn('status', ['dipinjam', 'disetujui'])
            ->where('waktu_kembali', '<', now())
            ->get();

        foreach ($jatuhTempo as $transaksi) {
            $message = 'Jatuh Tempo: ' . $transaksi->barangs->pluck('nama_barang')->join(', ') . ' oleh ' . $transaksi->siswa->nama . '.';
            foreach ($admins as $admin) {
                Notification::firstOrCreate(['user_id' => $admin->id, 'message' => $message, 'read_at' => null]);
            }
        }

        // 2. Cek untuk pengingat jam pulang sekolah (jam 14:00 atau 2 siang)
        if (now()->hour == 14) {
            $perluDikembalikanHariIni = Transaksi::whereIn('status', ['dipinjam', 'disetujui'])
                ->whereDate('waktu_kembali', today())
                ->get();

            if ($perluDikembalikanHariIni->isNotEmpty()) {
                $message = 'Pengingat: Ada ' . $perluDikembalikanHariIni->count() . ' peminjaman yang harus kembali sebelum jam pulang hari ini.';
                foreach ($admins as $admin) {
                    // Gunakan firstOrCreate untuk mencegah notifikasi duplikat di jam yang sama
                    Notification::firstOrCreate(['user_id' => $admin->id, 'message' => $message, 'read_at' => null]);
                }
            }
        }

        $this->info('Pengecekan notifikasi selesai.');
        return 0;
    }
}
