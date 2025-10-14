<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Storage;

class ExportTransactionsCommand extends Command
{
    protected $signature = 'app:export-transactions';
    protected $description = 'Mengekspor data riwayat transaksi ke file CSV untuk analisis LSTM.';

    public function handle()
    {
        $this->info('Memulai ekspor data transaksi...');

        $filePath = 'exports/transactions.csv';

        // Pastikan folder exports ada
        if (!Storage::disk('local')->exists('exports')) {
            Storage::disk('local')->makeDirectory('exports');
        }

        $fullPath = storage_path('app/' . $filePath);

        $file = fopen($fullPath, 'w');

        fputcsv($file, ['tanggal_pinjam', 'barang_id', 'kuantitas']);

        // Contoh menulis data (ubah sesuai model kamu)
        $transactions = Transaksi::all();
        foreach ($transactions as $transaction) {
            fputcsv($file, [
                $transaction->tanggal_pinjam,
                $transaction->barang_id,
                $transaction->kuantitas
            ]);
        }

        fclose($file);

        $this->info('Ekspor selesai. File disimpan di: ' . $fullPath);
    }
}
