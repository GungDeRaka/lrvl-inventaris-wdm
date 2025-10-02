<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaksi;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;

class TolakBookingOtomatis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:tolak-booking-otomatis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Secara otomatis menolak permintaan booking yang sudah melewati waktu peminjaman.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Cari transaksi yang statusnya 'diajukan' & waktu pinjamnya sudah lewat dari sekarang
        $permintaanKedaluwarsa = Transaksi::where('status', 'diajukan')
            ->where('waktu_pinjam', '<', now())
            ->get();

        if ($permintaanKedaluwarsa->count() > 0) {
            foreach ($permintaanKedaluwarsa as $permintaan) {
                // Ubah statusnya menjadi 'ditolak'
                $permintaan->update(['status' => 'ditolak']);
            }
            $this->info($permintaanKedaluwarsa->count() . ' permintaan kedaluwarsa berhasil ditolak secara otomatis.');
        } else {
            $this->info('Tidak ada permintaan kedaluwarsa yang ditemukan.');
        }

        return 0;
    }
}
