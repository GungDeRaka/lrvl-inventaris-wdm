<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

class BatalkanPeminjamanOtomatis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:batalkan-peminjaman-otomatis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membatalkan peminjaman yang disetujui jika siswa tidak mengambil barang melewati waktu booking.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Cari transaksi yang statusnya 'disetujui' & waktu pinjamnya sudah lewat
        $peminjamanKedaluwarsa = Transaksi::with('barangs')
            ->where('status', 'disetujui')
            ->where('waktu_pinjam', '<', now())
            ->get();

        if ($peminjamanKedaluwarsa->count() > 0) {
            foreach ($peminjamanKedaluwarsa as $transaksi) {
                DB::transaction(function () use ($transaksi) {
                    // 1. Ubah status dan tambahkan alasan
                    $transaksi->update([
                        'status' => 'ditolak',
                        'alasan_penolakan' => 'Siswa tidak mengambil barang yang dipesan sesuai waktu booking.'
                    ]);

                    // 2. Kembalikan stok untuk setiap barang dalam transaksi
                    foreach ($transaksi->barangs as $barang) {
                        $barang->increment('jumlah_saat_ini', $barang->pivot->kuantitas);
                    }
                });
            }
            $this->info($peminjamanKedaluwarsa->count() . ' peminjaman yang disetujui berhasil dibatalkan secara otomatis.');
        } else {
            $this->info('Tidak ada peminjaman disetujui yang kedaluwarsa.');
        }

        return 0;
    }
}
