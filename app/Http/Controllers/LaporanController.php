<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf; 

class LaporanController extends Controller
{
    public function cetakTransaksi()
    {
        // 1. Ambil semua data transaksi yang diperlukan
        $transaksis = Transaksi::with(['siswa', 'barang', 'user'])->get();

        // Data tambahan untuk laporan
        $data = [
            'tanggal' => date('d F Y'),
            'transaksis' => $transaksis
        ];

        // 2. Muat view Blade sebagai PDF
        $pdf = Pdf::loadView('laporan.transaksi_pdf', $data);

        // 3. Download PDF dengan nama file tertentu
        return $pdf->download('laporan-transaksi-inventaris.pdf');
    }
}
